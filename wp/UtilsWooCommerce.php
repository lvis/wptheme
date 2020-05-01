<?php /** Author: Vitali Lupu <vitaliix@gmail.com> */

namespace wp;

use WC_Admin_Menus;
use WC_Coupon;
use WC_Customer;
use WC_Form_Handler;
use WC_Product;
use WC_Shipping_Rate;
use WC_Validation;
use WC_Widget_Cart;
use WC_Widget_Layered_Nav;
use WC_Widget_Layered_Nav_Filters;
use WC_Widget_Price_Filter;
use WC_Widget_Product_Categories;
use WC_Widget_Product_Search;
use WC_Widget_Product_Tag_Cloud;
use WC_Widget_Products;
use WC_Widget_Rating_Filter;
use WC_Widget_Recent_Reviews;
use WC_Widget_Recently_Viewed;
use WC_Widget_Top_Rated_Products;
use WooCommerce;

final class UtilsWooCommerce
{
    /**
     * Hook: woocommerce_shop_loop.
     * @hooked WC_Structured_Data::generate_product_data() - 10
     */
    const TEXT_DOMAIN = 'woocommerce';
    const SHOP_LOOP = 'woocommerce_shop_loop';
    const SHOP_LOOP_ITEM_BEFORE = 'woocommerce_before_shop_loop_item';
    const SHOP_LOOP_ITEM_AFTER = 'woocommerce_after_shop_loop_item';
    const SHOP_LOOP_ITEM_TITLE_BEFORE = 'woocommerce_before_shop_loop_item_title';
    const SHOP_LOOP_ITEM_TITLE = 'woocommerce_shop_loop_item_title';
    const SHOP_LOOP_ITEM_TITLE_AFTER = 'woocommerce_after_shop_loop_item_title';

    private $uriToLibs = '';
    private $useWooScripts = true;
    private $pluginName = 'Shop';

    protected static $instance = null;

    public static function i()
    {
        if (!self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    protected function __construct()
    {
        if (self::isWooCommerceActive()) {
            $this->uriToLibs = get_template_directory_uri() . '/libs/';
            add_action(WPActions::THEME_SETUP, [$this, 'themeSetupWooCommerce']);
            add_action(WPActions::LOADED, [$this, 'handleLoaded']);
            if (is_admin()) {
                $this->addHandlersForBackend();
            } else {
                $this->addHandlersForFrontend();
            }
        }
    }

    function handleLoaded()
    {
        $this->pluginName = _x($this->pluginName, 'Page title', self::TEXT_DOMAIN);
    }

    function addHandlersForFrontend()
    {
        //HTML
        global $woocommerce;
        remove_action(WPActions::WP_HEAD, [$woocommerce, 'generator']);
        //------------------------------------------------[Scripts & Styles]
        add_action(WPActions::ENQUEUE_SCRIPTS_THEME, [$this, 'enqueueScriptsWooCommerce']);
        add_filter('woocommerce_enqueue_styles', [$this, 'handleWooCommerceEnqueueStyle']);
        //add_filter('woocommerce_get_asset_url', [$this, 'handleGetAssetUrl'], 10, 2);
        //------------------------------------------------[URL]
        $namePage = 'cart';
        add_filter("woocommerce_get_{$namePage}_page_permalink", [$this, 'handleGetCartPagePermalink']);
        add_filter('woocommerce_checkout_redirect_empty_cart', [$this, 'handleCheckoutRedirectEmptyCart'], 10, 0);
        //------------------------------------------------[Widgets]
        //remove_action(WPActions::WIDGETS_INIT, 'wc_register_widgets' );
        add_action(WPActions::WIDGETS_INIT, [$this, 'initWidgetsForSidebar']);
        //------------------------------------------------[Page: Address Editing]
        add_action('woocommerce_login_redirect', [$this, 'handleLoginRedirect'], 10, 1);
        remove_action('woocommerce_account_edit-address_endpoint', 'woocommerce_account_edit_address');
        add_action('woocommerce_account_edit-address_endpoint', [$this, 'handleEditAddressEndpoint']);
        remove_action('template_redirect', [WC_Form_Handler::class, 'save_address']);
        add_action('template_redirect', [$this, 'handleTemplateRedirectSaveAddress']);
        //------------------------------------------------[Menu Cart Item]
        //TODO Handle Menu Item icons in Navigator Widget Class
        add_filter(WPActions::NAV_MENU_ITEM_LINK_ATTRIBUTES, [$this, 'handleNavMenuItemLinkAttributes'], 10, 4);
        add_filter('woocommerce_add_to_cart_fragments', [$this, 'handleNavMenuItemsAddToCart']);
        //------------------------------------------------[Product Grid Item]
        add_action(WPActions::INIT, [$this, 'initShopLoopItemHandlers'], 10);
        //------------------------------------------------[Page: Checkout]
        add_filter('woocommerce_gateway_icon', [$this, 'handleGatewayIcon'], 10, 2);
        //[Order Review]
        remove_action('woocommerce_checkout_order_review', 'woocommerce_order_review');
        //[Fields]
        //https://github.com/woocommerce/woocommerce/issues/14618
        //https://docs.woocommerce.com/document/tutorial-customising-checkout-fields-using-actions-and-filters/#
        //https://rudrastyh.com/woocommerce/reorder-checkout-fields.html
        //https://wordpress.org/support/topic/change-order-of-billing-fields-on-checkout-page/ - Solution for reordering fields
        add_filter('woocommerce_default_address_fields', [$this, 'handleFieldsDefaultAddress']);
        add_filter('woocommerce_billing_fields', [$this, 'handleFieldsBilling']);
        add_filter('woocommerce_shipping_fields', [$this, 'handleFieldsShipping']);
        add_filter('woocommerce_checkout_fields', [$this, 'handleCheckoutFields']);
        //------------------------------------------------[Page: Account]
    }

    function addHandlersForBackend()
    {
        //------------------------------------------------[Notification]
        //Disable: Notice “CONNECT YOUR STORE TO WOOCOMMERCE.COM”
        remove_action( 'admin_notices', 'woothemes_updater_notice' );
        //------------------------------------------------[Settings]
        //$nameSetting = 'woocommerce_enable_guest_checkout';
        //add_filter("pre_option_{$nameSetting}", [$this, 'handlePreOptionEnableGuestCheckout']);
        add_filter('woocommerce_helper_suppress_connect_notice', '__return_true');
        //------------------------------------------------[WooCommerce: Text]
        if (defined('WPLANG') && WPLANG) {
            add_action(WPActions::INIT, [$this, 'handleInit']);
        } else {
            add_filter(WPActions::GET_TEXT, [$this, 'handleGetText']);
            add_filter(WPActions::GET_TEXT_WITH_CONTEXT, [$this, 'handleGetText']);
        }
        add_filter(WPActions::ALL_PLUGINS, [$this, 'handleAllPlugins']);
        add_filter( 'block_categories', function( $categories){
            /*foreach ($categories as &$category){
                if ($category['title'] == 'WooCommerce'){

                }
            }*/
            return $categories;
        }, 9999);
        //------------------------------------------------[WooCommerce: Icon]
		add_action('enqueue_block_editor_assets', [$this, 'enqueueBlockEditorAssets']);
        add_action(WPActions::ADMIN_HEAD, [$this, 'handleAdminHead']);
        add_action(WPActions::ADMIN_MENU, [$this, 'handleAdminMenu']);
        add_action(WPActions::ADMIN_INIT, [$this, 'handleAdminInit']);

    }

    static function isWooCommerceActive()
    {
        return class_exists('WooCommerce');
    }

    function handleWooCommerceEnqueueStyle($styles)
    {
        //unset($styles['woocommerce-layout']);
        //unset($styles['woocommerce-smallscreen']);
        return $styles;
    }

    /** Add WooComerce Theme Support */
    function themeSetupWooCommerce()
    {
        add_theme_support('woocommerce');
        //[Commerce] - Make product view customizeable
//        add_theme_support('wc-product-gallery-slider');
//        add_theme_support('wc-product-gallery-lightbox');
//        add_theme_support('wc-product-gallery-zoom');
        /*
        $optionsProductGrid = [
            'default_rows'    => 3,
            'min_rows'        => 2,
            'max_rows'        => 8,
            'default_columns' => 4,
            'min_columns'     => 2,
            'max_columns'     => 5,
        ];
        $optionsThemeSupport = [
            'thumbnail_image_width' => 150,
            'single_image_width' => 300,
            'product_grid' => $optionsProductGrid
        ];
        add_theme_support( 'woocommerce', $optionsThemeSupport);*/
    }

    function handleGetAssetUrl($fullPath, $path)
    {
        $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
        $pathAssetFrontEnd = 'assets/js/frontend/';
        if ("{$pathAssetFrontEnd}cart-fragments{$suffix}.js" == $path && $this->useWooScripts == false) {
            return "{$this->uriToLibs}cart-fragments.js";
        }
        return $fullPath;
    }

    function initWidgetsForSidebar()
    {
        Widget::i();
        unregister_widget(WC_Widget_Cart::class);
        unregister_widget(WC_Widget_Layered_Nav_Filters::class);
        unregister_widget(WC_Widget_Layered_Nav::class);
        unregister_widget(WC_Widget_Price_Filter::class);
        unregister_widget(WC_Widget_Product_Categories::class);
        unregister_widget(WC_Widget_Product_Search::class);
        unregister_widget(WC_Widget_Product_Tag_Cloud::class);
        unregister_widget(WC_Widget_Products::class);
        unregister_widget(WC_Widget_Recently_Viewed::class);
        unregister_widget(WC_Widget_Top_Rated_Products::class);
        unregister_widget(WC_Widget_Recent_Reviews::class);
        unregister_widget(WC_Widget_Rating_Filter::class);
    }

    /**
     * Load Required CSS Styles and Javascript Files
     * Docs: https://developer.wordpress.org/themes/basics/including-css-javascript/
     */
    function enqueueScriptsWooCommerce()
    {
        wp_enqueue_style('fixes', $this->uriToLibs . 'fixes.css', ['woocommerce-general']);
        wp_deregister_script('selectWoo');
        if (is_checkout()) {
            wp_enqueue_script('wc-cart');
            wp_add_inline_script('wc-cart', "jQuery( document ).on('change select', '.qty', function() {
                  jQuery('[name=update_cart]').trigger('click');
            });");
        }
    }

    function handleGetCartPagePermalink()
    {
        return wc_get_page_permalink('checkout');
    }

    function handleCheckoutRedirectEmptyCart()
    {
        return false;
    }

    function handlePreOptionEnableGuestCheckout()
    {
        /** Always Disable Guest Checkout */
        return 'no';
    }

    //------------------------------------------------[Menu Cart Item]
    function getCartTitle()
    {
        $result = __('Cart', self::TEXT_DOMAIN);
        $countCartContents = wc()->cart->get_cart_contents_count();
        if ($countCartContents !== 0) {
            $textCartTotal = wc()->cart->get_cart_total();
            $result = "<i class='fa fa-shopping-cart'></i>
            <sup style='margin-left: -7px;font-weight: bold;'>{$countCartContents}</sup> {$textCartTotal}";
            $attributes['title'] = __('View cart', self::TEXT_DOMAIN);
        } else {
            $result = "<i class='far fa-shopping-cart'></i> {$result}";
        }
        return "<span class='cart-contents'>$result</span>";
    }

    /**
     * Filters the HTML attributes applied to a menu item's anchor element.
     * @param array $attributes The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
     * @param \WP_Post $item The current menu item.
     * @param \stdClass $args An object of wp_nav_menu() arguments.
     * @param int $depth Depth of menu item. Used for padding.
     * @return array
     */
    function handleNavMenuItemLinkAttributes($attributes, \WP_Post $item, \stdClass $args, int $depth)
    {
        if ($args->theme_location == '' && $depth == 0 && $attributes['href'] == wc_get_checkout_url()) {
            $item->title = $this->getCartTitle();
        }
        return $attributes;
    }

    function handleNavMenuItemsAddToCart($fragments)
    {
        $fragments['span.cart-contents'] = $this->getCartTitle();
        return $fragments;
    }

    //------------------------------------------------[Product Grid Item]
    function initShopLoopItemHandlers()
    {
        //[Container:Before]
        remove_action(self::SHOP_LOOP_ITEM_BEFORE, 'woocommerce_template_loop_product_link_open');
        //add_action(self::SHOP_LOOP_ITEM_BEFORE, [$this, 'handleShopLoopItemBefore']);
        //[Thumbnail]
        remove_action(self::SHOP_LOOP_ITEM_TITLE_BEFORE, 'woocommerce_template_loop_product_thumbnail');
        remove_action(self::SHOP_LOOP_ITEM_TITLE_BEFORE, 'woocommerce_show_product_loop_sale_flash');
        add_action(self::SHOP_LOOP_ITEM_TITLE_BEFORE, [$this, 'handleShopLoopItemTitleBefore']);
        //[Title]
        remove_action(self::SHOP_LOOP_ITEM_TITLE, 'woocommerce_template_loop_product_title');
        add_action(self::SHOP_LOOP_ITEM_TITLE, [$this, 'handleShopLoopItemTitle']);
        //[Rating]
        remove_action(self::SHOP_LOOP_ITEM_TITLE_AFTER, 'woocommerce_template_loop_rating', 5);
        //[Price]
        remove_action(self::SHOP_LOOP_ITEM_TITLE_AFTER, 'woocommerce_template_loop_price');
        add_action(self::SHOP_LOOP_ITEM_TITLE_AFTER, [$this, 'handleShopLoopItemTitleAfter']);
        //[Container: After]
        remove_action(self::SHOP_LOOP_ITEM_AFTER, 'woocommerce_template_loop_add_to_cart');
        remove_action(self::SHOP_LOOP_ITEM_AFTER, 'woocommerce_template_loop_product_link_close', 5);
        //add_action(self::SHOP_LOOP_ITEM_AFTER, [$this, 'handleShopLoopItemAfter'], 5);
    }

    function handleShopLoopItemBefore()
    {
        echo "<div class='item'>";
    }

    function handleShopLoopItemAfter()
    {
        echo '</div>';
    }

    function handleShopLoopItemTitleBefore()
    {
        $productThumb = woocommerce_get_product_thumbnail();
        $productLink = $this->getProductLink();
        echo "<a href='{$productLink}' class='woocommerce-LoopProduct-link woocommerce-loop-product__link'><figure>{$productThumb}";
    }

    function handleShopLoopItemTitle()
    {
        $productTitle = get_the_title();
        echo "<figcaption class='woocommerce-loop-product__title text-hide-overflow'>{$productTitle}</figcaption></figure></a>";
    }

    function handleShopLoopItemTitleAfter()
    {
        /**@var $product WC_Product */
        global $product;
        //[Category]
        $htmlProductCategories = '';
        /*$productCategories = get_the_terms(get_the_ID(), 'product_cat');
        if (is_array($productCategories)) {
            //@var $category \WP_Term
            foreach ($productCategories as $category) {
                $categoryLink = get_term_link($category->term_id, 'product_cat');
                $htmlProductCategories .= "<a href='{$categoryLink}' class='text-info'>{$category->name}</a>";
            }
            $htmlProductCategories = "<p class='category'>{$htmlProductCategories}</p>";
        }*/
        //[Price]
        if ($htmlPrice = $product->get_price_html()) {
            $htmlPrice = "<div class='price'>{$htmlPrice}</div>";
        }
        //[Rating]
        $htmlRating = '';
        if (get_option('woocommerce_enable_review_rating') !== 'no') {
            $ratingWidth = (($product->get_average_rating() / 5) * 100);
            $htmlRating = "<div class='star-rating'><span style='width:{$ratingWidth}%'></span></div>";
        }
        //[Add To Cart]
        ob_start();
        woocommerce_template_loop_add_to_cart();
        $htmlAddToCart = ob_get_clean();
        echo "{$htmlProductCategories}{$htmlRating}{$htmlPrice}<div class='d-xs-block'>{$htmlAddToCart}</div>";
    }

    /*------------------------------------------------[Page: Address Editing]*/
    function handleLoginRedirect($redirect)
    {
        $redirect_page_id = url_to_postid($redirect);
        $checkout_page_id = wc_get_page_id('checkout');
        if ($redirect_page_id != $checkout_page_id) {
            $redirect = wc_get_account_endpoint_url('orders');
        }
        return $redirect;
    }

    function handleEditAddressEndpoint()
    {
        wc_get_template('myaccount/my-address.php');
    }

    function handleTemplateRedirectSaveAddress()
    {
        /** Save and and update a billing or shipping address if the form was submitted through the user account page. */
        if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
            if (isset($_POST['action']) && strpos($_POST['action'], 'edit_address') === 0) {
                wc_nocache_headers();
                $nonce_value = wc_get_var($_REQUEST['woocommerce-edit-address-nonce'], wc_get_var($_REQUEST['_wpnonce'], '')); // @codingStandardsIgnoreLine.
                if (wp_verify_nonce($nonce_value, 'woocommerce-edit_address')) {
                    $user_id = get_current_user_id();
                    $load_address = substr($_POST['action'], strlen('edit_address_'));
                    if ($user_id > 0 && empty($load_address) === false) {
                        $address = wc()->countries->get_address_fields(esc_attr($_POST[$load_address . '_country']), $load_address . '_');
                        foreach ($address as $key => $field) {
                            if (!isset($field['type'])) {
                                $field['type'] = 'text';
                            }
                            // Get Value.
                            switch ($field['type']) {
                                case 'checkbox' :
                                    $_POST[$key] = (int)isset($_POST[$key]);
                                    break;
                                default :
                                    $_POST[$key] = isset($_POST[$key]) ? wc_clean($_POST[$key]) : '';
                                    break;
                            }
                            // Hook to allow modification of value.
                            $_POST[$key] = apply_filters('woocommerce_process_myaccount_field_' . $key, $_POST[$key]);
                            // Validation: Required fields.
                            if (!empty($field['required']) && empty($_POST[$key])) {
                                wc_add_notice(sprintf(__('%s is a required field.', self::TEXT_DOMAIN), $field['label']), 'error');
                            }
                            if (!empty($_POST[$key])) {

                                // Validation rules.
                                if (!empty($field['validate']) && is_array($field['validate'])) {
                                    foreach ($field['validate'] as $rule) {
                                        switch ($rule) {
                                            case 'postcode' :
                                                $_POST[$key] = strtoupper(str_replace(' ', '', $_POST[$key]));
                                                if (!WC_Validation::is_postcode($_POST[$key], $_POST[$load_address . '_country'])) {
                                                    wc_add_notice(__('Please enter a valid postcode / ZIP.', self::TEXT_DOMAIN), 'error');
                                                } else {
                                                    $_POST[$key] = wc_format_postcode($_POST[$key], $_POST[$load_address . '_country']);
                                                }
                                                break;
                                            case 'phone' :
                                                $_POST[$key] = wc_format_phone_number($_POST[$key]);
                                                if (!WC_Validation::is_phone($_POST[$key])) {
                                                    wc_add_notice(sprintf(__('%s is not a valid phone number.', self::TEXT_DOMAIN), '<strong>' . $field['label'] . '</strong>'), 'error');
                                                }
                                                break;
                                            case 'email' :
                                                $_POST[$key] = strtolower($_POST[$key]);
                                                if (!is_email($_POST[$key])) {
                                                    wc_add_notice(sprintf(__('%s is not a valid email address.', self::TEXT_DOMAIN), '<strong>' . $field['label'] . '</strong>'), 'error');
                                                }
                                                break;
                                        }
                                    }
                                }
                            }
                        }
                        do_action('woocommerce_after_save_address_validation', $user_id, $load_address, $address);
                        if (0 === wc_notice_count('error')) {
                            $customer = new WC_Customer($user_id);
                            if ($customer) {
                                foreach ($address as $key => $field) {
                                    if (is_callable([$customer, "set_$key"])) {
                                        $customer->{"set_$key"}(wc_clean($_POST[$key]));
                                    } else {
                                        $customer->update_meta_data($key, wc_clean($_POST[$key]));
                                    }
                                    if (wc()->customer && is_callable([wc()->customer, "set_$key"])) {
                                        wc()->customer->{"set_$key"}(wc_clean($_POST[$key]));
                                    }
                                }
                                $customer->save();
                            }
                            wc_add_notice(__('Address changed successfully.', self::TEXT_DOMAIN));
                            do_action('woocommerce_customer_save_address', $user_id, $load_address);
                            wp_safe_redirect(wc_get_endpoint_url('edit-address', '', wc_get_page_permalink('myaccount')));
                            exit;
                        }
                    }
                }
            }
        }
    }

    /*------------------------------------------------[Page: Address Editing]*/
    function handleGatewayIcon($iconHtml, $idGateway)
    {
        if ($idGateway === 'paypal') {
            $textWhatIsPayPal = __('What is PayPal?', self::TEXT_DOMAIN);
            $country = strtoupper(UtilsWp::getLanguageShortCode());
            if ($country === 'EN') {
                $country = 'US';
            }
            $urlToWhatIsPayPal = 'https://www.paypal.com/' . strtolower($country);
            $home_counties = ['BE', 'CZ', 'DK', 'HU', 'IT', 'JP', 'NL', 'NO', 'ES', 'SE', 'TR', 'IN'];
            $countries = ['DZ', 'AU', 'BH', 'BQ', 'BW', 'CA', 'CN', 'CW', 'FI', 'FR', 'DE', 'GR', 'HK',
                'ID', 'JO', 'KE', 'KW', 'LU', 'MY', 'MA', 'OM', 'PH', 'PL', 'PT', 'QA', 'IE', 'RU', 'BL',
                'SX', 'MF', 'SA', 'SG', 'SK', 'KR', 'SS', 'TW', 'TH', 'AE', 'GB', 'US', 'VN'];
            if (in_array($country, $home_counties, true)) {
                $urlToWhatIsPayPal .= '/webapps/mpp/home';
            } else if (in_array($country, $countries, true)) {
                $urlToWhatIsPayPal .= '/webapps/mpp/paypal-popup';
            } else {
                $urlToWhatIsPayPal .= '/cgi-bin/webscr?cmd=xpt/Marketing/general/WIPaypal-outside';
            }
            $iconHtml = "<a href='{$urlToWhatIsPayPal}' 
            onclick=\"window.open('{$urlToWhatIsPayPal}','WIPaypal',
            'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes'); 
            return false;\">
            <small>{$textWhatIsPayPal}</small></a>";

        }
        return $iconHtml;
    }

    function handleFieldsDefaultAddress($fields)
    {
        //first_name 10
        //last_name  20
        //company    30
        //country    40
        //address_1  50
        //address_2  60
        //city       70
        //state      80
        //postcode   90
        //$fields['address_2']['placeholder'] = __('Apartment, suite, unit etc.', self::TEXT_DOMAIN);
        $fields_order = ['first_name', 'last_name', 'company', 'phone', 'email', 'country', 'state', 'address_1', 'address_2', 'postcode', 'city'];
        $priority = 10;// Set fields priority
        foreach ($fields_order as $key) {
            if (!isset($fields[$key])) {
                continue;
            }
            $fields[$key]['priority'] = $priority;
            $priority += 10;
        }
        $fields_ordered = [];// Change fields order
        foreach ($fields_order as $key) {
            if (isset($fields[$key])) {
                $fields_ordered[$key] = $fields[$key];
            }
        }
        $fields_ordered['state']['required'] = true;
        unset($fields_ordered['city']);
        return $fields_ordered;
    }

    function handleFieldsBilling($fields)
    {
        $fields['billing_phone']['priority'] = 21;
        $fields['billing_email']['priority'] = 22;
        return $fields;
    }

    function handleFieldsShipping($fields)
    {
        /*
        shipping_first_name 10
        shipping_last_name  20
        shipping_company    30
        shipping_address_1  40
        shipping_address_2  50
        shipping_city       60
        shipping_postcode   70
        shipping_country    80
        shipping_state      90
        Ex: $fields['shipping_first_name']['priority'] = 10;
        */
        return $fields;
    }

    function handleCheckoutFields($fields)
    {
        //$addressFields['billing']['billing_address_2']['placeholder'] = __('Apartment, suite, unit etc.', self::TEXT_DOMAIN );
        //$addressFields['shipping']['shipping_address_2']['placeholder'] = __('Apartment, suite, unit etc.', self::TEXT_DOMAIN );
        $fields['billing']['billing_phone']['priority'] = 21;
        $fields['billing']['billing_email']['priority'] = 22;
        return $fields;
    }

    function getShippingMethodLabel(WC_Shipping_Rate $method)
    {
        $label = $method->get_label();
        $methodKeyId = str_replace(':', '_', $method->id);
        $methodLabel = get_option("woocommerce_{$methodKeyId}_settings", true)['title'];
        if ($methodLabel) {
            $label = __($methodLabel, self::TEXT_DOMAIN);
        }
        return $label;
    }

    function getShippingMethodLabelFull(WC_Shipping_Rate $method)
    {
        $label = $this->getShippingMethodLabel($method);
        $label .= ': ' . $this->getShippingMethodPrice($method);
        return apply_filters('woocommerce_cart_shipping_method_full_label', $label, $method);
    }

    function getShippingMethodPrice(WC_Shipping_Rate $method)
    {
        $shippingMethodPrice = '';
        if ($method->cost >= 0 && $method->get_method_id() !== 'free_shipping') {
            if (wc()->cart->display_prices_including_tax()) {
                $shippingMethodPrice .= wc_price($method->cost + $method->get_shipping_tax());
                if ($method->get_shipping_tax() > 0 && !wc_prices_include_tax()) {
                    $shippingMethodPrice .= ' <small class="tax_label">' . wc()->countries->inc_tax_or_vat() . '</small>';
                }
            } else {
                $shippingMethodPrice .= wc_price($method->cost);
                if ($method->get_shipping_tax() > 0 && wc_prices_include_tax()) {
                    $shippingMethodPrice .= ' <small class="tax_label">' . wc()->countries->ex_tax_or_vat() . '</small>';
                }
            }
        }
        return $shippingMethodPrice;
    }

    static function getProductLink()
    {
        /**@var $product WC_Product */
        global $product;
        return esc_url(apply_filters('woocommerce_loop_product_link', get_the_permalink(), $product));
    }

    /**
     * Outputs a checkout/address form field.
     * @param string $key Key.
     * @param mixed $args Arguments.
     * @param string $value (default: null).
     * @return string
     */
    static function getFormField($key, $args, $value = null)
    {
        $args = wp_parse_args($args, [
            'id' => $key,
            'label' => '',
            'type' => 'text',
            'description' => '',
            'placeholder' => '',
            'default' => '',
            'priority' => '',
            'autofocus' => '',
            'return' => false,
            'required' => false,
            'maxlength' => false,
            'autocomplete' => false,
            'class' => [],
            'label_class' => [],
            'input_class' => [],
            'validate' => [],
            'options' => [],
            'custom_attributes' => [],
        ]);
        $args = apply_filters('woocommerce_form_field_args', $args, $key, $value);
        if (is_string($args['label_class'])) {
            $args['label_class'] = [$args['label_class']];
        }
        if (is_null($value)) {
            $value = $args['default'];
        }
        $args['custom_attributes'] = array_filter((array)$args['custom_attributes'], 'strlen');
        if ($args['maxlength']) {
            $args['custom_attributes']['maxlength'] = absint($args['maxlength']);
        }
        if (!empty($args['autocomplete'])) {
            $args['custom_attributes']['autocomplete'] = $args['autocomplete'];
        }
        if (true === $args['autofocus']) {
            $args['custom_attributes']['autofocus'] = 'autofocus';
        }
        $inputId = esc_attr($args['id']);
        $fieldDescription = $args['description'];
        if ($fieldDescription) {
            $fieldDescriptionId = "{$inputId}-description";
            $args['custom_attributes']['aria-describedby'] = $fieldDescriptionId;
            $fieldDescription = wp_kses_post($args['description']);
            $fieldDescription = "<span id='{$fieldDescriptionId}' class='description' aria-hidden='true'>{$fieldDescription}</span>";
        }
        $fieldLabel = '';
        if ($args['label']) {
            $fieldLabel = $args['label'];
        }
        $isFieldRequired = $args['required'] ? true : false;
        if ($isFieldRequired) {
            $inputLabelTitle = __('required', self::TEXT_DOMAIN);
            $fieldLabel .= "&nbsp;<abbr class='required' title='{$inputLabelTitle}'>*</abbr>";
        } else {
            $inputLabelTitle = __('optional', self::TEXT_DOMAIN);
            if ($fieldDescription) {
                $fieldDescription .= " ({$inputLabelTitle})";
            } else {
                $fieldDescription = "({$inputLabelTitle})";
            }
            $fieldLabel .= "&nbsp;<span class='optional'></span>";
        }
        $inputAttrs = '';
        if (!empty($args['custom_attributes']) && is_array($args['custom_attributes'])) {
            foreach ($args['custom_attributes'] as $attrName => $attrValue) {
                $attrName = esc_attr($attrName);
                $attrValue = esc_attr($attrValue);
                $inputAttrs .= " {$attrName}='{$attrValue}'";
            }
        }
        $inputName = esc_attr($key);
        $inputPlaceHolder = esc_attr($args['placeholder']);
        $inputClass = esc_attr(implode(' ', $args['input_class']));
        $fieldType = $args['type'];
        $field = '';
        $labelId = $inputId;
        $fieldAttrContainer = '';
        switch ($fieldType) {
            case 'text':
            case 'password':
            case 'datetime':
            case 'datetime-local':
            case 'date':
            case 'month':
            case 'time':
            case 'week':
            case 'number':
            case 'email':
            case 'url':
            case 'tel':
                $inputType = esc_attr($fieldType);
                $inputValue = esc_attr($value);
                $field = "<input id='{$inputId}' name='{$inputName}'  placeholder='{$inputPlaceHolder}' 
                class='input-text {$inputClass}' {$inputAttrs} type='{$inputType}' value='{$inputValue}'>";
                break;
            case 'textarea':
                $inputValue = esc_textarea($value);
                if (empty($args['custom_attributes']['rows'])) {
                    $inputAttrs .= ' rows="2"';
                }
                if (empty($args['custom_attributes']['cols'])) {
                    $inputAttrs .= ' cols="5"';
                }
                $field = "<textarea id='{$inputId}' name='{$inputName}' placeholder='{$inputPlaceHolder}' 
                class='input-text {$inputClass}' {$inputAttrs}>{$inputValue}</textarea>";
                break;
            case 'checkbox':
                $inputType = esc_attr($fieldType);
                $inputChecked = checked($value, 1, false);
                $fieldLabelCssClasses = esc_attr(implode(' ', $args['label_class']));
                $field = "<label title='{$inputLabelTitle}' class='checkbox {$fieldLabelCssClasses}' {$inputAttrs}>
                <input id='{$inputId}' name='{$inputName}' type='{$inputType}' class='{$inputClass}' 
                value='1' {$inputChecked}>{$fieldLabel}</label>";
                break;
            case 'radio':
                if (empty($args['options']) == false) {
                    $labelId = current(array_keys($args['options']));
                    $fieldLabelCssClasses = esc_attr(implode(' ', $args['label_class']));
                    foreach ($args['options'] as $optionKey => $optionText) {
                        $inputValue = esc_attr($optionKey);
                        $optionSelected = checked($value, $optionKey, false);
                        $optionText = esc_attr($optionText);
                        $field .= "<input id='{$inputId}_{$inputValue}' name='{$inputName}' value='{$inputValue}' 
                        type='radio' class='input-radio {$inputClass}' {$inputAttrs} {$optionSelected}>
                        <label for='{$inputId}_{$inputValue}' class='radio {$fieldLabelCssClasses}'>{$optionText}</label>";
                    }
                }
                break;
            case 'select':
                if (empty($args['options']) == false) {
                    $options = '';
                    foreach ($args['options'] as $optionKey => $optionText) {
                        if ($optionKey === '') {
                            if (empty($args['placeholder'])) {
                                $args['placeholder'] = $optionText;
                                if (empty($optionText)) {
                                    $args['placeholder'] = __('Choose an option', self::TEXT_DOMAIN);
                                }
                            }
                            $inputAttrs .= ' data-allow_clear="true"';
                        }
                        $inputValue = esc_attr($optionKey);
                        $optionSelected = selected($value, $optionKey, false);
                        $optionText = esc_attr($optionText);
                        $options .= "<option value='{$inputValue}' {$optionSelected}>{$optionText}</option>";
                    }
                    $field = "<select id='{$inputId}' name='{$inputName}' {$inputAttrs} 
                    class='select {$inputClass}' data-placeholder='{$inputPlaceHolder}'>{$options}</select>";
                }
                break;
            case 'country':
                if ($key === 'shipping_country') {
                    $countries = wc()->countries->get_shipping_countries();
                } else {
                    $countries = wc()->countries->get_allowed_countries();
                }
                if (count($countries) === 1) {
                    $inputValue = current(array_keys($countries));
                    $currentCountry = current(array_values($countries));
                    $field = "<strong>{$currentCountry}</strong><input id='{$inputId}' name='{$inputName}' {$inputAttrs} 
                    class='country_to_state' value='{$inputValue}' type='hidden' readonly='readonly'>";
                } else {
                    $textSelectCountry = __('Select a country&hellip;', self::TEXT_DOMAIN);
                    $textUpdateCountry = __('Update country', self::TEXT_DOMAIN);
                    $options = "<option value=''>{$textSelectCountry}</option>";
                    foreach ($countries as $countryKey => $countryValue) {
                        $inputValue = esc_attr($countryKey);
                        $optionSelected = selected($value, $countryKey, false);
                        $options .= "<option value='{$inputValue}' {$optionSelected}>{$countryValue}</option>";
                    }
                    $field = "<select id='{$inputId}' name='{$inputName}' {$inputAttrs} 
                    class='country_to_state country_select {$inputClass}'>{$options}</select>
                    <noscript><button type='submit' name='woocommerce_checkout_update_totals' value='{$textUpdateCountry}'>
                    {$textUpdateCountry}</button></noscript>";
                }
                break;
            case 'state':
                /* Get country this state field is representing */
                if (isset($args['country'])) {
                    $currentCountry = $args['country'];
                } else {
                    $currentCountry = 'shipping_country';
                    if ($key === 'billing_state') {
                        $currentCountry = 'billing_country';
                    }
                    $currentCountry = wc()->checkout->get_value($currentCountry);
                }
                $states = wc()->countries->get_states($currentCountry);
                if (is_array($states) && empty($states)) {
                    $field = "<input id='{$inputId}' name='{$inputName}' placeholder='{$inputPlaceHolder}' {$inputAttrs}  
                    class='hidden' value='' type='hidden' readonly='readonly'>";
                    $fieldAttrContainer = ' style="display: none"';
                } else if (is_array($states) && is_null($currentCountry) == false) {
                    $textSelectState = __('Select a state&hellip;', self::TEXT_DOMAIN);
                    $options = "<option value=''>{$textSelectState}</option>";
                    foreach ($states as $stateKey => $stateValue) {
                        $inputValue = esc_attr($stateKey);
                        $optionSelected = selected($value, $stateKey, false);
                        $options .= "<option value='{$inputValue}' {$optionSelected}>{$stateValue}</option>";
                    }
                    $field = "<select id='{$inputId}' name='{$inputName}' {$inputAttrs} class='state_select {$inputClass}' 
                    data-placeholder='{$inputPlaceHolder}'>{$options}</select>";
                } else {
                    $inputValue = esc_attr($value);
                    $field = "<input id='{$inputId}' name='{$inputName}' placeholder='{$inputPlaceHolder}' {$inputAttrs} 
                     class='input-text {$inputClass}' value='{$inputValue}' type='text'>";
                }
                break;
        }
        if (empty($field) == false) {
            $fieldCssClasses = esc_attr(implode(' ', $args['class']));
            if ($isFieldRequired) {
                $fieldCssClasses .= ' validate-required';
            }
            if (is_array($args['validate'])) {
                foreach ($args['validate'] as $validate) {
                    $fieldCssClasses .= " validate-{$validate}";
                }
            }
            $fieldPriority = '';
            if ($args['priority']) {
                $fieldPriority = esc_attr($args['priority']);
            }
            if ($fieldType !== 'checkbox') {
                $fieldLabelCssClasses = esc_attr(implode(' ', $args['label_class']));
                if ($labelId == 'billing_address_2' || $labelId == 'shipping_address_2') {
                    $fieldLabelCssClasses = str_replace('screen-reader-text', '', $fieldLabelCssClasses);
                }
                if (empty($fieldLabelCssClasses)) {
                    $fieldLabelCssClasses = '';
                }
                $fieldLabel = "<label for='{$labelId}' class='col-xs-5 {$fieldLabelCssClasses}' 
                title='{$inputLabelTitle}'>{$fieldLabel}</label>";
            }
            $field = "<p id='{$inputId}_field' class='row form-row {$fieldCssClasses}' data-priority='{$fieldPriority}'{$fieldAttrContainer}>
            {$fieldLabel}<span class='col-xs-7'>{$field}{$fieldDescription}</span></p>";
        }
        /** Filter by type. */
        $field = apply_filters("woocommerce_form_field_{$fieldType}", $field, $key, $args, $value);
        /** General filter on form fields. @since 3.4.0 */
        $field = apply_filters('woocommerce_form_field', $field, $key, $args, $value);
        return $field;
    }

    static function getFormattedPrice($price, $args = [], $showCurrency = true)
    {
        $currency = '';
        if (isset($args['currency'])) {
            $currency = $args['currency'];
        }
        $args = apply_filters('wc_price_args', wp_parse_args($args, [
            'ex_tax_label' => false,
            'currency' => get_woocommerce_currency(),
            'currencySymbol' => get_woocommerce_currency_symbol($currency),
            'decimal_separator' => wc_get_price_decimal_separator(),
            'thousand_separator' => wc_get_price_thousand_separator(),
            'decimals' => wc_get_price_decimals(),
            'price_format' => get_woocommerce_price_format(),
        ]));
        $unformatted_price = $price;
        $negative = $price < 0;
        $price = apply_filters('raw_woocommerce_price', floatval($negative ? $price * -1 : $price));
        $price = apply_filters('formatted_woocommerce_price', number_format($price,
            $args['decimals'],
            $args['decimal_separator'],
            $args['thousand_separator']),
            $price,
            $args['decimals'],
            $args['decimal_separator'],
            $args['thousand_separator']);
        if (apply_filters('woocommerce_price_trim_zeros', false) && $args['decimals'] > 0) {
            $price = wc_trim_zeros($price);
        }
        $return = ($negative ? '-' : '');
        if ($showCurrency) {
            $return .= sprintf($args['price_format'], $args['currency'], $price);
        } else {
            $return .= $price;
        }
        if ($args['ex_tax_label'] && wc_tax_enabled()) {
            $return .= wc()->countries->ex_tax_or_vat();
        }
        /**
         * Filters the string of price markup.
         * @param string $return Price HTML markup.
         * @param string $price Formatted price.
         * @param array $args Pass on the args.
         * @param float $unformatted_price Price as float to allow plugins custom formatting. Since 3.2.0.
         */
        return apply_filters('wc_price', $return, $price, $args, $unformatted_price);
    }

    static function getCartSubtotal(WC_Product $product, $quantity, $showCurrency)
    {
        $productPrice = $product->get_price();
        $cart = wc()->cart;
        if ($product->is_taxable()) {
            if ($cart->display_prices_including_tax()) {
                $pricePerRow = wc_get_price_including_tax($product, ['qty' => $quantity]);
                $productSubtotal = self::getFormattedPrice($pricePerRow, [], $showCurrency);
                if (!wc_prices_include_tax() && $cart->get_subtotal_tax() > 0) {
                    $productSubtotal .= wc()->countries->inc_tax_or_vat();
                }
            } else {
                $pricePerRow = wc_get_price_excluding_tax($product, ['qty' => $quantity]);
                $productSubtotal = self::getFormattedPrice($pricePerRow, [], $showCurrency);
                if (wc_prices_include_tax() && $cart->get_subtotal_tax() > 0) {
                    $productSubtotal .= wc()->countries->ex_tax_or_vat();
                }
            }
        } else {
            $pricePerRow = $productPrice * $quantity;
            $productSubtotal = self::getFormattedPrice($pricePerRow, [], $showCurrency);
        }
        return apply_filters('woocommerce_cart_product_subtotal', $productSubtotal, $product, $quantity, $cart);
    }

    function getContentCartItems(WooCommerce $wc)
    {
        $contentCartItems = '';
        $filterNameCartItemVisible = 'woocommerce_widget_cart_item_visible';
        $filterNameCartItemQty = 'woocommerce_widget_cart_item_quantity';
        $cartItemClass = 'mini_cart_item';
        $cartItemClasses = "woocommerce-mini-cart-item {$cartItemClass}";
        if (is_page('cart') || is_cart()) {
            $filterNameCartItemVisible = 'woocommerce_cart_item_visible';
            $filterNameCartItemQty = 'woocommerce_cart_item_quantity';
            $cartItemClass = 'cart_item';
            $cartItemClasses = "woocommerce-cart-form__cart-item {$cartItemClass}";
        }
        foreach ($wc->cart->get_cart() as $cartItemKey => $cartItem) {
            /**@var  $cartProduct WC_Product */
            $cartProduct = apply_filters('woocommerce_cart_item_product', $cartItem['data'], $cartItem, $cartItemKey);
            $cartProductVisible = apply_filters($filterNameCartItemVisible, true, $cartItem, $cartItemKey);
            if ($cartProductVisible && $cartProduct && $cartProduct->exists() && $cartItem['quantity'] > 0) {
                //--------------------Product: Attributes
                //Product: ID
                $cartProductDataAttr = '';
                $cartProductId = apply_filters('woocommerce_cart_item_product_id', $cartItem['product_id'], $cartItem, $cartItemKey);
                if ($cartProductId) {
                    $cartProductDataAttr .= " data-product_id='{$cartProductId}'";
                }
                //Product: SKU
                $cartProductSku = esc_attr($cartProduct->get_sku());
                if ($cartProductSku) {
                    $cartProductDataAttr .= " data-product_sku='{$cartProductSku}'";
                }
                //Product: TAG
                if ($cartItemKey) {
                    $cartProductDataAttr .= " data-cart_item_key='{$cartItemKey}'";
                }
                //Product: Attributes
                $productAttr = wc_get_formatted_cart_item_data($cartItem);
                //Product: Remove
                $textRemove = __('Remove', self::TEXT_DOMAIN);
                $urlProductRemove = esc_url(wc_get_cart_remove_url($cartItemKey));
                $productRemoveButton = "<a href='{$urlProductRemove}' class='product-remove fas fa-trash-alt'
                title='{$textRemove}' {$cartProductDataAttr}></a>";
                $productRemoveButton = apply_filters('woocommerce_cart_item_remove_link', $productRemoveButton, $cartItemKey);
                //Product: Back Order Notifications
                $productBackOrder = '';
                if ($cartProduct->backorders_require_notification() && $cartProduct->is_on_backorder($cartItem['quantity'])) {
                    $textAvailableOnBackOrder = __('Available on backorder', self::TEXT_DOMAIN);
                    $contentBackOrder = "<p class='backorder_notification'>{$textAvailableOnBackOrder}</p>";
                    $productBackOrder = apply_filters('woocommerce_cart_item_backorder_notification', $contentBackOrder, $cartProductId);
                    $productBackOrder = wp_kses_post($productBackOrder);
                }
                //Product: Image
                $cartProductImage = $cartProduct->get_image();
                $cartProductImage = apply_filters('woocommerce_cart_item_thumbnail', $cartProductImage, $cartItem, $cartItemKey);
                //Product: Name
                $cartProductName = $cartProduct->get_name();
                $cartProductName = apply_filters('woocommerce_cart_item_name', $cartProductName, $cartItem, $cartItemKey);
                $cartProductName = wp_kses_post($cartProductName);
                $cartProductName .= UtilsWp::doAction('woocommerce_after_cart_item_name', $cartItem, $cartItemKey);
                //Product: Link
                $cartProductLink = '';
                if ($cartProduct->is_visible()) {
                    $cartProductLink = $cartProduct->get_permalink();
                }
                $cartProductLink = apply_filters('woocommerce_cart_item_permalink', $cartProductLink, $cartItem, $cartItemKey);
                $cartProductLink = esc_url($cartProductLink);
                //Product: Price
                $cartProductPrice = $wc->cart->get_product_price($cartProduct);
                $cartProductPrice = apply_filters('woocommerce_cart_item_price', $cartProductPrice, $cartItem, $cartItemKey);
                //Product: Quantity
                if ($cartProduct->is_sold_individually()) {
                    $cartProductQty = "<input name='cart[{$cartItemKey}][qty]' value='1' type='hidden'>";
                } else {
                    $cartProductQty = woocommerce_quantity_input([
                        'input_name' => "cart[{$cartItemKey}][qty]",
                        'product_name' => $cartProductName,
                        'input_value' => $cartItem['quantity'],
                        'min_value' => '0',
                        'max_value' => $cartProduct->get_max_purchase_quantity(),
                    ], $cartProduct, false);
                }
                $cartProductQty = apply_filters($filterNameCartItemQty, $cartProductQty, $cartItemKey, $cartItem);
                //Product: Total
                $cartProductPriceSubTotal = $wc->cart->get_product_subtotal($cartProduct, $cartItem['quantity']);
                $cartProductPriceTotal = apply_filters('woocommerce_cart_item_subtotal', $cartProductPriceSubTotal,
                    $cartItem, $cartItemKey);
                //Product: Content
                $cssCartItem = apply_filters("woocommerce_{$cartItemClass}_class", $cartItemClasses, $cartItem, $cartItemKey);
                $cssCartItem = esc_attr($cssCartItem);
                $contentCartItems .= "<section class='{$cssCartItem}'>
                <div class='col-xs-6'><span class='col-xs-3 float-xs-left'>{$cartProductImage}</span>
                <a href='{$cartProductLink}'>{$cartProductName} {$cartProductPrice}</a>{$productAttr}{$productBackOrder}</div>
                <div class='col-xs-3'>{$productRemoveButton} {$cartProductQty}</div>
                <div class='col-xs-3 text-xs-center'>{$cartProductPriceTotal}</div></section>";
            }
        }
        return $contentCartItems;
    }

    function getContentCartSubtotal(WooCommerce $wc)
    {
        $textSubtotal = __('Subtotal', 'woocommerce');
        $cartItemsCount = $wc->cart->get_cart_contents_count();
        $contentCartItemsCount = _n('%s product', '%s products', $cartItemsCount, 'woocommerce');
        $contentCartItemsCount = sprintf($contentCartItemsCount, $cartItemsCount);
        $contentCartSubtotal = $wc->cart->get_cart_subtotal();
        return "<section class='cart-subtotal'>
        <div class='col-xs-6'><i class='fa fa-chart-pie'></i> {$textSubtotal}</div>
        <div class='col-xs-3'><i class='fa fa-wine-bottle'></i> {$contentCartItemsCount}</div>
        <div class='col-xs-3 text-xs-center'>{$contentCartSubtotal}</div></section>";
    }

    function getContentCartShipping(WooCommerce $wc)
    {
        $contentShipping = '';
        if ($wc->cart->needs_shipping()) {
            if ($wc->cart->show_shipping()) {
                $packages = $wc->shipping()->get_packages();
                $shippingPackageCount = count($packages);
                $shippingPackageHasMany = ($shippingPackageCount > 1);
                //Shipping Packages
                foreach ($packages as $index => $package) {
                    //Shipping: Package Name
                    $packageIndex = $index + 1;
                    if ($packageIndex > 1) {
                        $packageName = _x('Shipping %d', 'shipping packages', 'woocommerce');
                        $packageName = sprintf($packageName, $packageIndex);
                    } else {
                        $packageName = _x('Shipping', 'shipping packages', 'woocommerce');
                    }
                    $packageName = apply_filters('woocommerce_shipping_package_name', $packageName, $index, $package);
                    $packageName = wp_kses_post($packageName);
                    //Shipping: Package Details
                    if ($shippingPackageHasMany) {
                        $productNames = [];
                        foreach ($package['contents'] as $item_id => $values) {
                            $productNames[$item_id] = $values['data']->get_name() . ' &times;' . $values['quantity'];
                        }
                        $productNames = apply_filters('woocommerce_shipping_package_details_array', $productNames, $package);
                        $package_details = implode(', ', $productNames);
                        $packageName .= "<span>{$package_details}</span>";
                    }
                    //Shipping: Methods
                    $contentShippingMethods = '';
                    $shippingMethods = $package['rates'];
                    $countShippingMethods = count($shippingMethods);
                    $shippingMethodChosenPrice = '';
                    if ($countShippingMethods >= 1) {
                        $shippingMethodChosen = '';
                        if (isset($wc->session->chosen_shipping_methods[$index])) {
                            $shippingMethodChosen = $wc->session->chosen_shipping_methods[$index];
                        }
                        foreach ($shippingMethods as $shippingMethod) {
                            /**@var $shippingMethod WC_Shipping_Rate */
                            $actionShippingRateAfter = UtilsWp::doAction('woocommerce_after_shipping_rate', $shippingMethod, $index);
                            $methodId = "shipping_method_{$index}_" . sanitize_title($shippingMethod->id);
                            $methodValue = esc_attr($shippingMethod->id);
                            $methodLabel = $this->getShippingMethodLabel($shippingMethod);
                            //$methodLabel = $this->getShippingMethodLabelFull($shippingMethod);
                            $methodPrice = $this->getShippingMethodPrice($shippingMethod);
                            //$methodChecked = checked($shippingMethod->id, $shippingMethodChosen, false);
                            if ($shippingMethod->id == $shippingMethodChosen) {
                                $shippingMethodChosenPrice = $methodPrice;
                            }
                            $methodChecked = selected($shippingMethod->id, $shippingMethodChosen, false);
                            /*$contentShippingMethods .= "<div class='col-xs-6'>
                            <input id='{$methodId}' value='{$methodValue}' name='shipping_method[{$index}]' type='radio'
                            data-index='{$index}' class='shipping_method' {$methodChecked}>
                            <label for='{$methodId}'>{$methodLabel}</label></div>
                            <div class='col-xs-3'>{$actionShippingRateAfter}</div>
                            <div class='col-xs-3 text-xs-center'>{$methodPrice}</div>";*/
                            $contentShippingMethods .= "<option id='{$methodId}' value='{$methodValue}' name='shipping_method[{$index}]'
                             data-index='{$index}' {$methodChecked}>{$methodLabel}</option>";
                        }
                        $contentShippingMethods = "<select class='shipping_method' data-index='{$index}'>{$contentShippingMethods}</select>";
                    } else if ($wc->customer->has_calculated_shipping()) {
                        $textNoShipping = __('There are no shipping methods available. Please ensure that your address has been entered correctly, or contact us if you need any help.', 'woocommerce');
                        if (is_cart()) {
                            $textNoShipping = apply_filters('woocommerce_cart_no_shipping_available_html', $textNoShipping);
                        } else {
                            $textNoShipping = apply_filters('woocommerce_no_shipping_available_html', $textNoShipping);
                        }
                        $contentShippingMethods = "<div class='col-xs-12'>{$textNoShipping}</div>";
                    } else if (is_cart() == false) {
                        $textEnterAddress = __('Enter your full address to see shipping costs.', 'woocommerce');
                        $contentShippingMethods = "<div class='col-xs-12'>{$textEnterAddress}</div>";
                    }
                    //Shipping: Content
                    /*$contentShipping .= "<section class='shipping'>
                    <div class='col-xs-9'><i class='fa fa-shipping-fast'></i> {$packageName}</div>
                    <div class='col-xs-3 text-xs-center'>{$shippingMethodChosenPrice}</div>
                    <div id='shipping_method'>{$contentShippingMethods}</div></section>";*/
                    $contentShipping .= "<section class='shipping'>
                    <div class='col-xs-6'><i class='fa fa-shipping-fast'></i> {$packageName}</div>
                    <div class='col-xs-3' id='shipping_method'>{$contentShippingMethods}</div>
                    <div class='col-xs-3 text-xs-center'>{$shippingMethodChosenPrice}</div></section>";
                }
                $actionReviewOrderShippingBefore = UtilsWp::doAction('woocommerce_review_order_before_shipping');
                $actionReviewOrderShippingAfter = UtilsWp::doAction('woocommerce_review_order_after_shipping');
                $actionCartTotalsShippingBefore = UtilsWp::doAction('woocommerce_cart_totals_before_shipping');
                $actionCartTotalsShippingAfter = UtilsWp::doAction('woocommerce_cart_totals_after_shipping');
                $contentShipping = "{$actionReviewOrderShippingBefore}{$contentShipping}{$actionReviewOrderShippingAfter}";
            } else if (get_option('woocommerce_enable_shipping_calc') === 'yes') {
                $contentShipping = $this->getContentCartShippingCalculator($wc);
            }
        }
        return $contentShipping;
    }

    function getContentCartShippingCalculator(WooCommerce $wc)
    {
        //yourtheme/woocommerce/cart/shipping-calculator.php.
        $contentShippingCalculator = '';
        if (get_option('woocommerce_enable_shipping_calc') === 'yes') {
            //Shipping Calculator: Country
            wp_enqueue_script('wc-country-select');
            $cssColFirst = 'col-xs-6 col-md-3 text-truncate';
            $cssColLast = 'col-xs-6 col-md-9';
            $idCountry = 'calc_shipping_country';
            $contentCountryOptions = '';
            foreach ($wc->countries->get_shipping_countries() as $key => $value) {
                $key = esc_attr($key);
                $value = esc_html($value);
                $countrySelected = selected($wc->customer->get_shipping_country(), $key, false);
                $contentCountryOptions .= "<option value='{$key}' {$countrySelected}>{$value}</option>";
            }
            $textShippingCountryRegion = __('Shipping Country / Region', 'woocommerce');
            $textSelectCountry = __('Select a country / region&hellip;', 'woocommerce');
            $contentShippingCountry = "<p id='{$idCountry}_field' class='form-row form-row-wide'>
            <label for='{$idCountry}' class='{$cssColFirst}'>
                <i class='fa fa-globe-europe'></i> {$textShippingCountryRegion}
            </label>
            <span class='{$cssColLast}'>
            <select id='{$idCountry}' name='{$idCountry}' rel='calc_shipping_state' class='country_to_state country_select'>
            <option value=''>{$textSelectCountry}</option>{$contentCountryOptions}
            </select></span></p>";
            //Shipping Calculator: State
            $contentShippingState = '';
            if (apply_filters('woocommerce_shipping_calculator_enable_state', true)) {
                $idState = 'calc_shipping_state';
                $textShippingState = __('Shipping State', 'woocommerce');
                $currentShippingCountry = $wc->customer->get_shipping_country();
                $states = $wc->countries->get_states($currentShippingCountry);
                if (empty($states)) {
                    $contentShippingState = "<input type='hidden' name='{$idState}' id='{$idState}' placeholder='{$textShippingState}'>";
                } else {
                    $currentShippingState = $wc->customer->get_shipping_state();
                    if (is_array($states)) {
                        $textState = __('State', 'woocommerce');
                        $textSelectState = __('Select a state&hellip;', 'woocommerce');
                        $contentShippingStateOptions = '';
                        foreach ($states as $stateId => $stateName) {
                            $stateSelected = selected($currentShippingState, $stateId, false);
                            $stateId = esc_attr($stateId);
                            $stateName = esc_html($stateName);
                            $contentShippingStateOptions .= "<option value='{$stateId}' {$stateSelected}>{$stateName}</option>";
                        }
                        $contentShippingState = "<select id='{$idState}' name='{$idState}' placeholder='{$textState}' class='state_select'>
                        <option value=''>{$textSelectState}</option>{$contentShippingStateOptions}</select>";
                    } else {
                        $currentShippingState = esc_attr($currentShippingState);
                        $textStateCountryCode = __('State / County or state code', 'woocommerce');
                        $contentShippingState = "<input id='{$idState}' name='{$idState}' value='{$currentShippingState}' 
                        type='text'  placeholder='{$textStateCountryCode}'>";
                    }
                    $contentShippingState = "<p id='{$idState}_field' class='form-row form-row-wide'>
                    <label for='{$idState}' class='{$cssColFirst}'>
                        <i class='fa fa-map-marked'></i> {$textShippingState}
                    </label>
                    <span class='{$cssColLast}'>{$contentShippingState}</span></p>";
                }
            }
            //Shipping Calculator: City
            $contentShippingCity = '';
            if (apply_filters('woocommerce_shipping_calculator_enable_city', true)) {
                $idCity = 'calc_shipping_city';
                $textCity = __('City', 'woocommerce');
                $textShippingCity = __('Shipping City', 'woocommerce');
                $valueShippingCity = esc_attr($wc->customer->get_shipping_city());
                $contentShippingCity = "<p id='{$idCity}_field' class='form-row form-row-wide'>
                <label for='{$idCity}' class='{$cssColFirst}'>
                    <i class='fa fa-city'></i> {$textShippingCity}
                </label>
                <span class='{$cssColLast}'>
                <input type='text' id='{$idCity}' name='{$idCity}' value='{$valueShippingCity}' placeholder='{$textCity}' class='input-text'>
                </span></p>";
            }
            //Shipping Calculator: ZIP Code
            $contentShippingPostCode = '';
            if (apply_filters('woocommerce_shipping_calculator_enable_postcode', true)) {
                $idPostCode = 'calc_shipping_postcode';
                $textPostCode = __('Postcode / ZIP', 'woocommerce');
                $valueShippingPostCode = esc_attr($wc->customer->get_shipping_postcode());
                $contentShippingPostCode = "<p id='calc_shipping_postcode_field' class='form-row form-row-wide'>
                <label for='{$idPostCode}' class='{$cssColFirst}'><i class='fa fa-mail-bulk'></i> {$textPostCode}</label>
                <span class='{$cssColLast}'><input type='text'  id='{$idPostCode}' name='{$idPostCode}' value='{$valueShippingPostCode}' 
                placeholder='{$textPostCode}' class='input-text'></span></p>";
            }
            //Shipping Calculator: Update
            $textUpdateTotals = __('Update totals', 'woocommerce');
            $urlCart = esc_url(wc_get_cart_url());
            $nonceShippingCalculator = wp_nonce_field('woocommerce-shipping-calculator',
                'woocommerce-shipping-calculator-nonce', true, false);
            //Shipping Calculator: Content
            $actionShippingCalculatorBefore = UtilsWp::doAction('woocommerce_before_shipping_calculator');
            $actionShippingCalculatorAfter = UtilsWp::doAction('woocommerce_after_shipping_calculator');
            $textCalculateShipping = __('Calculate shipping', 'woocommerce');
            $contentShippingCalculator = "<section class='shipping'>
            {$actionShippingCalculatorBefore}
            <form class='woocommerce-shipping-calculator' action='{$urlCart}' method='post'>
                <p class='form-row form-row-wide'>
                <span class='col-md-9 col-sm-6'><i class='fas fa-calculator-alt'></i> {$textCalculateShipping}</span>
                <span class='col-md-3 col-sm-6 text-xs-center'>
                    <button type='submit' name='calc_shipping' value='1' class='button'>{$textUpdateTotals}</button>
                </span>
                </p>
                {$contentShippingCountry}{$contentShippingState}{$contentShippingCity}{$contentShippingPostCode}
                {$nonceShippingCalculator}
            </form>{$actionShippingCalculatorAfter}</section>";
        }
        return $contentShippingCalculator;
    }

    function getContentCartCoupons(WooCommerce $wc)
    {
        $contentCoupons = '';
        if (wc_coupons_enabled()) {
            $cartCoupons = $wc->cart->get_coupons();
            foreach ($cartCoupons as $couponKey => $coupon) {
                if (is_string($coupon)) {
                    $coupon = new WC_Coupon($coupon);
                }
                $couponCode = $coupon->get_code();
                $amount = $wc->cart->get_coupon_discount_amount($couponCode, $wc->cart->display_cart_ex_tax);
                if (empty($amount)) {
                    $htmlDiscountAmount = '';
                    if ($coupon->get_free_shipping()) {
                        $htmlDiscountAmount = __('Free shipping coupon', 'woocommerce');
                    }
                } else {
                    $htmlDiscountAmount = '-' . wc_price($amount);
                }
                $htmlDiscountAmount = apply_filters('woocommerce_coupon_discount_amount_html', $htmlDiscountAmount, $coupon);
                /** Remove Button*/
                $linkCouponRemove = add_query_arg('remove_coupon', rawurlencode($couponCode), wc_get_checkout_url());
                $linkCouponRemove = esc_url($linkCouponRemove);
                $textRemove = __('Remove', 'woocommerce');
                $couponCode = esc_attr($couponCode);
                $couponCodeEsc = esc_attr(sanitize_title($couponKey));
                $couponLabel = wc_cart_totals_coupon_label($coupon, false);
                $couponDescription = $coupon->get_description();
                $contentCoupons .= "<section class='cart-discount coupon-{$couponCodeEsc}'>
                <div class='col-xs-9'><i class='fas fa-tag'></i>  {$couponLabel} {$couponDescription}</div>
                <div class='col-xs-3 text-xs-center'>{$htmlDiscountAmount}
                <a href='{$linkCouponRemove}' class='woocommerce-remove-coupon fal fa-trash-alt' title='{$textRemove}' 
                data-coupon='{$couponCode}' data-bind='click:handleClickCouponRemove'></a>
                </div></section>";
            }
            $textIfYouHaveCoupon = __('If you have a coupon code, please apply it below.', 'woocommerce');
            $textCouponCode = __('Coupon code', 'woocommerce');
            $textApplyCoupon = __('Apply coupon', 'woocommerce');
            $textApply = __('Apply');
            $actionCartCoupon = UtilsWp::doAction('woocommerce_cart_coupon');
            $contentCoupons = "<section class='coupons'>
            <section class='col-xs-12'><i class='fas fa-tags'></i> {$textIfYouHaveCoupon}</section>
            <section class='coupon'>
            <label for='coupon_code' class='col-xs-6'><i class='fas fa-user-tag'></i> {$textApplyCoupon}</label> 
            <input id='coupon_code' name='coupon_code' type='text' placeholder='{$textCouponCode}'
            data-bind='textInput:couponCode, event:{keypress:handleKeyPressAddCoupon}' class='col-xs-3'>
            <span class='col-xs-3 text-xs-center'>
            <button type='submit' name='apply_coupon' value='{$textApplyCoupon}' title='{$textApplyCoupon}' 
            data-bind='click:handleClickCouponAdd, enable:hasCouponCode' class='button'>{$textApply}</button>
            </span>
            </section>
            {$contentCoupons}
            {$actionCartCoupon}</section>";
        }
        return $contentCoupons;
    }

    function getContentCartFees(WooCommerce $wc)
    {
        $contentFees = '';
        $cartFees = $wc->cart->get_fees();
        foreach ($cartFees as $fee) {
            $feeAmount = wc_price($fee->total);
            if ($wc->cart->display_prices_including_tax()) {
                $feeAmount = wc_price($fee->total + $fee->tax);
            }
            $feeAmount = apply_filters('woocommerce_cart_totals_fee_html', $feeAmount, $fee);
            $feeLabel = esc_html($fee->name);
            $contentFees .= "<section class='fee'>
            <div class='col-xs-8'><i class='fa fa-percent'></i>{$feeLabel}</div>
            <div class='col-xs-4 text-xs-center'>{$feeAmount}</div></section>";
        }
        return $contentFees;
    }

    function getEstimatedTax(WooCommerce $wc)
    {
        $textEstimated = '';
        if ($wc->customer->is_customer_outside_base() && $wc->customer->has_calculated_shipping() == false) {
            $taxableAddress = $wc->customer->get_taxable_address()[0];
            $textEstimated = $wc->countries->estimated_for_prefix($taxableAddress);
            $textEstimated .= $wc->countries->countries[$taxableAddress];
            $textEstimated = sprintf(__('(estimated for %s)', 'woocommerce'), $textEstimated);
        }
        return $textEstimated;
    }

    function getContentCartTaxes(WooCommerce $wc)
    {
        $contentTax = '';
        if (wc_tax_enabled() && $wc->cart->display_prices_including_tax() == false) {
            $textEstimated = $this->getEstimatedTax($wc);
            $optionTaxTotalDisplay = get_option('woocommerce_tax_total_display');
            if ($optionTaxTotalDisplay === 'itemized') {
                $taxTotals = $wc->cart->get_tax_totals();
                foreach ($taxTotals as $code => $tax) {
                    $code = sanitize_title($code);
                    $taxLabel = esc_html($tax->label);
                    $taxTotal = wp_kses_post($tax->formatted_amount);
                    $contentTax .= "<div class='tax-rate tax-rate-{$code}'>
                    <div class='col-xs-8'><i class='fa fa-percent'></i>{$taxLabel}{$textEstimated}</div>
                    <div class='col-xs-4 text-xs-center'>{$taxTotal}</div></div>";
                }
            } else {
                $taxLabel = $wc->countries->tax_or_vat();
                $taxLabel = esc_html($taxLabel);
                $taxTotal = $wc->cart->get_taxes_total();
                $taxTotal = wc_price($taxTotal);
                $taxTotal = apply_filters('woocommerce_cart_totals_taxes_total_html', $taxTotal);
                $contentTax = "<section class='tax-total'>
                <div class='col-xs-8'><i class='fa fa-cash-register'></i>{$taxLabel}{$textEstimated}</div>
                <div class='col-xs-4 text-xs-center'>{$taxTotal}</div></section>";
            }
        }
        return $contentTax;
    }

    function getContentCartTotal(WooCommerce $wc)
    {
        $contentTotal = $wc->cart->get_total();
        if (wc_tax_enabled() && $wc->cart->display_prices_including_tax()) {
            // If prices are tax inclusive, show taxes here.
            $taxValues = [];
            $cartTaxTotal = $wc->cart->get_tax_totals();
            if (get_option('woocommerce_tax_total_display') === 'itemized') {
                foreach ($cartTaxTotal as $code => $tax) {
                    $taxValues[] = sprintf('%s %s', $tax->formatted_amount, $tax->label);
                }
            } elseif (empty($cartTaxTotal) == false) {
                $cartTaxesTotal = $wc->cart->get_taxes_total(true, true);
                $cartTaxesTotal = wc_price($cartTaxesTotal);
                $taxValues[] = sprintf('%s %s', $cartTaxesTotal, $wc->countries->tax_or_vat());
            }
            if (empty($taxValues) == false) {
                $textEstimated = $this->getEstimatedTax($wc);
                /* translators: %s: tax information */
                $includeTaxValue = implode(', ', $taxValues) . ' ' . $textEstimated;
                $contentIncludeTax = sprintf(__('(includes %s)', 'woocommerce'), $includeTaxValue);
                $contentTotal .= "<small class='includes_tax'>{$contentIncludeTax}</small>";
            }
        }
        return apply_filters('woocommerce_cart_totals_order_total_html', $contentTotal);
    }
    /**------------------------------------------------[WooCommerce: Text]*/
    /** Replace a string in the internationalisation table with a custom value. */
    function handleInit()
    {
        global $l10n;
        if (is_array($l10n)) {
            foreach ($l10n as $pluginKey => $pluginValue) {
                foreach ($pluginValue->entries as $entryKey => $entryValue) {
                    foreach ($entryValue->translations as $translationKey => $translationValue) {
                        if (stristr($translationValue, self::TEXT_DOMAIN)) {
                            $translationChanged = str_ireplace(self::TEXT_DOMAIN, $this->pluginName, $translationValue);
                            $l10n[$pluginKey]->entries[$entryKey]->translations[$translationKey] = $translationChanged;
                        }
                    }
                }
            }
        }
    }

    function handleGetText(string $translation)
    {
        return str_ireplace(self::TEXT_DOMAIN, $this->pluginName, $translation);
    }

    function handleAllPlugins($plugins)
    {
        foreach ($plugins as $key => $value) {
            $plugins[$key]['Name'] = str_replace(['WooCommerce', self::TEXT_DOMAIN, 'Woocommerce'], $this->pluginName, $plugins[$key]['Name']);
            $plugins[$key]['Description'] = str_replace(['WooCommerce', self::TEXT_DOMAIN, 'Woocommerce'], $this->pluginName, $plugins[$key]['Description']);
        }
        return $plugins;
    }

    /**------------------------------------------------[WooCommerce: Icon]*/
	function enqueueBlockEditorAssets(){
		wp_add_inline_script('wp-data', "window.addEventListener('load', function() {
            wp.blocks.updateCategory( 'woocommerce', { title: '{$this->pluginName}' } ); 
        });");
	}
    function handleAdminHead()
    {
        //https://wordpress.stackexchange.com/questions/315511/gutenberg-editor-add-a-custom-category-as-wrapper-for-custom-blocks - Change Block category name
        echo "<style type='text/css'>
        #adminmenu #toplevel_page_woocommerce .menu-icon-generic div.wp-menu-image::before {
            font-family: dashicons, sans-serif !important;
            content: '\\f513' !important;
        }
        a[href*='woocommerce.com'],
        a[href*='section=woocommerce_com'],
        a[href*='page=wc-addons'],
        .woocommerce-BlankState a[href*='woocommerce'],
        .woocommerce-layout__header .woocommerce-layout__header-breadcrumbs span:first-child,
        .woocommerce-layout__header .woocommerce-layout__header-breadcrumbs span:nth-child(2):before{
			display:none !important;
		}
        </style>";
    }

    function handleAdminMenu()
    {
        global $menu;
        if (is_array($menu)) {
            foreach ($menu as $k => $v) {
                if ($v[0] == 'WooCommerce' || $v[0] == $this->pluginName) {
                    $menu[$k][0] = $this->pluginName;
                    $menu[$k][6] = 'dashicons-store';
                    break;
                }
            }
        }
    }

    function handleAdminInit()
    {
        $tabs = ['general', 'page', 'catalog', 'inventory', 'shipping', 'tax', 'email', 'integration'];
        foreach ($tabs as $tab) {
            add_filter("woocommerce_{$tab}_settings", [$this, 'replaceNameWooCommerce'], 10);
        }
    }

    function replaceNameWooCommerce($fields)
    {
        foreach ($fields as $fieldKey => $fieldValue) {
            if (isset($fieldValue['desc'])) {
                $fields[$fieldKey]['desc'] = str_replace('WooCommerce', $this->pluginName,
                    $fields[$fieldKey]['desc']);
            }
            if (isset($fieldValue['name'])) {
                $fields[$fieldKey]['name'] = str_replace('WooCommerce', $this->pluginName,
                    $fields[$fieldKey]['name']);
            }
        }
        return $fields;
    }
}