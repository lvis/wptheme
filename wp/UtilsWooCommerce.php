<?php namespace wp;

use WC_Customer;
use WC_Form_Handler;
use WC_Shipping_Rate;
use WC_Validation;

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
    protected static $instance = null;
    private $uriToLibs = '';
    private $useWooScripts = true;
    public static function i()
    {
        if (!self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
    }
    protected function __construct()
    {
        if (class_exists('WooCommerce')) {
            $this->uriToLibs = get_template_directory_uri() . '/libs/';
            add_action(WPActions::THEME_SETUP, [$this, 'themeSetupWooCommerce']);
            add_action(WPActions::ENQUEUE_SCRIPTS_THEME, [$this, 'enqueueScriptsWooCommerce']);
//            add_filter('woocommerce_get_asset_url', [$this, 'handleGetAssetUrl'], 10, 2);
//            remove_action(WPActions::WIDGETS_INIT, 'wc_register_widgets' );
            add_action(WPActions::WIDGETS_INIT, [$this, 'initSidebarWidgets']);
//            add_filter('woocommerce_get_cart_url', [$this, 'handleGetCartUrl'], 10, 0);
            $namePage = 'cart';
            add_filter("woocommerce_get_{$namePage}_page_permalink", [$this, 'handleGetCartPagePermalink']);
            add_filter('woocommerce_checkout_redirect_empty_cart', [$this, 'handleCheckoutRedirectEmptyCart'], 10, 0);
            //$nameSetting = 'woocommerce_enable_guest_checkout';
            //add_filter("pre_option_{$nameSetting}", [$this, 'handlePreOptionEnableGuestCheckout']);
            //------------------------------------------------[Menu Cart Item]
            //TODO Handle Menu Item icons in Navigator Widget Class
            add_filter(WPActions::NAV_MENU_ITEM_LINK_ATTRIBUTES, [$this, 'handleNavMenuItemLinkAttributes'], 10, 4);
            add_filter('woocommerce_add_to_cart_fragments', [$this, 'handleNavMenuItemsAddToCart']);
            //------------------------------------------------[Product Grid Item]
            add_action(WPActions::INIT, [$this, 'initShopLoopItemHandlers'], 10);
            //------------------------------------------------[Page: Address Editing]
            add_action('woocommerce_login_redirect', [$this, 'handleLoginRedirect'], 10, 1);
            remove_action('woocommerce_account_edit-address_endpoint', 'woocommerce_account_edit_address');
            add_action('woocommerce_account_edit-address_endpoint', [$this, 'handleEditAddressEndpoint']);
            remove_action('template_redirect', [WC_Form_Handler::class, 'save_address']);
            add_action('template_redirect', [$this, 'handleTemplateRedirectSaveAddress']);
            /**------------------------------------------------[Page: Checkout]*/
            add_filter('woocommerce_gateway_icon', [$this, 'handleGatewayIcon'], 10, 2);
            /** Order Review */
            remove_action('woocommerce_checkout_order_review', 'woocommerce_order_review');
            /** Fields */
            //https://github.com/woocommerce/woocommerce/issues/14618
            //https://docs.woocommerce.com/document/tutorial-customising-checkout-fields-using-actions-and-filters/#
            //add_filter( 'woocommerce_billing_fields' , [$this, 'handleFieldsBilling']);
            //add_filter( 'woocommerce_shipping_fields' , [$this, 'handleFieldsShipping']);
//            add_filter('woocommerce_checkout_fields', [$this, 'handleCheckoutFields']);
//            add_filter('woocommerce_default_address_fields', [$this, 'handleCheckoutFieldsDefaultAddress']);
            /**------------------------------------------------[WooCommerce: Text]*/
            global $woocommerce;
            remove_action(WPActions::WP_HEAD, [$woocommerce, 'generator']);
            if (defined('WPLANG') && WPLANG) {
                add_action(WPActions::INIT, [$this, 'handleInit']);
            } else {
                add_filter(WPActions::GET_TEXT, [$this, 'handleGetText']);
            }
            add_filter(WPActions::ALL_PLUGINS, [$this, 'handleAllPlugins']);
            /**------------------------------------------------[WooCommerce: Icon]*/
            add_action(WPActions::ADMIN_HEAD, [$this, 'handleAdminHead']);
            add_action(WPActions::ADMIN_MENU, [$this, 'handleAdminMenu']);
            add_action(WPActions::ADMIN_INIT, [$this, 'handleAdminInit']);
            /**------------------------------------------------[Page: Account]*/
            remove_action('woocommerce_account_content', 'woocommerce_output_all_notices', 10);
            add_action('woocommerce_account_content', 'woocommerce_output_all_notices', 9);
        }
    }

    function getShopPageName(){
        return _x('Shop', 'Page title', self::TEXT_DOMAIN);
    }
    /** Add WooComerce Theme Support */
    function themeSetupWooCommerce()
    {
        add_theme_support('woocommerce');
        //[Commerce] - Make product view customizeable
        //add_theme_support('wc-product-gallery-slider');
        add_theme_support('wc-product-gallery-lightbox');
        //add_theme_support('wc-product-gallery-zoom');
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
    function initSidebarWidgets()
    {
        Widget::i();
        unregister_widget( \WC_Widget_Cart::class);
        unregister_widget( \WC_Widget_Layered_Nav_Filters::class);
        unregister_widget( \WC_Widget_Layered_Nav::class);
        unregister_widget( \WC_Widget_Price_Filter::class);
        unregister_widget( \WC_Widget_Product_Categories::class);
        unregister_widget( \WC_Widget_Product_Search::class);
        unregister_widget( \WC_Widget_Product_Tag_Cloud::class);
        unregister_widget( \WC_Widget_Products::class);
        unregister_widget( \WC_Widget_Recently_Viewed::class);
        unregister_widget( \WC_Widget_Top_Rated_Products::class);
        unregister_widget( \WC_Widget_Recent_Reviews::class);
        unregister_widget( \WC_Widget_Rating_Filter::class);
    }
    /**
     * Load Required CSS Styles and Javascript Files
     * Docs: https://developer.wordpress.org/themes/basics/including-css-javascript/
     */
    function enqueueScriptsWooCommerce()
    {
        wp_deregister_script('selectWoo');
        if (is_checkout()) {
            wp_enqueue_script('wc-cart');
            wp_add_inline_script('wc-cart', "jQuery( document ).on('change select', '.qty', function() {
                  jQuery('[name=update_cart]').trigger('click');
            });");
        }
    }
    function handleGetCartUrl()
    {
        $urlToCart = wc_get_page_permalink('checkout');
        if ($urlToCart && (is_ssl() || 'yes' === get_option('woocommerce_force_ssl_checkout'))) {
            $urlToCart = str_replace('http:', 'https:', $urlToCart); // Force SSL if needed.
        }
        return $urlToCart;
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
    function initShopLoopItemHandlers(){
        //[Container:Before]
        remove_action(self::SHOP_LOOP_ITEM_BEFORE, 'woocommerce_template_loop_product_link_open');
        add_action(self::SHOP_LOOP_ITEM_BEFORE, [$this, 'handleShopLoopItemBefore']);
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
        add_action(self::SHOP_LOOP_ITEM_AFTER, [$this, 'handleShopLoopItemAfter'], 5);
    }
    function handleShopLoopItemBefore()
    {
        echo "<div class='card'><div class='card-content'>";
    }
    function handleShopLoopItemAfter()
    {
        echo '</div></div>';
    }
    function handleShopLoopItemTitleBefore()
    {
        $productThumb = woocommerce_get_product_thumbnail();
        $productLink = $this->getProductLink();
        echo "<a href='{$productLink}' class='woocommerce-LoopProduct-link woocommerce-loop-product__link'>{$productThumb}";
    }
    function handleShopLoopItemTitle()
    {
        $productTitle = get_the_title();
        echo "<h6 class='woocommerce-loop-product__title card-title text-hide-overflow'>{$productTitle}</h6></a>";
    }
    function handleShopLoopItemTitleAfter()
    {
        /**@var $product \WC_Product */
        global $product;
        //[Category]
        $htmlProductCategories = '';
        $productCategories = get_the_terms(get_the_ID(), 'product_cat');
        if (is_array($productCategories)) {
            /**@var $category \WP_Term */
            foreach ($productCategories as $category) {
                $categoryLink = get_term_link($category->term_id, 'product_cat');
                $htmlProductCategories .= "<a href='{$categoryLink}' class='text-info'>{$category->name}</a>";
            }
            $htmlProductCategories = "<h6 class='category'>{$htmlProductCategories}</h6>";
        }
        //[Price]
        if ($htmlPrice = $product->get_price_html()) {
            $htmlPrice = "<h5>{$htmlPrice}</h5>";
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
        echo "{$htmlProductCategories}{$htmlRating}{$htmlPrice}{$htmlAddToCart}";
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
    function handleFieldsBilling($addressFields)
    {
        /*
        billing_first_name 10
        billing_last_name  20
        billing_company    30  110
        billing_country    40  50
        billing_address_1  50  80
        billing_address_2  60  90
        billing_city       70  70
        billing_state      80  60
        billing_postcode   90  100
        billing_email      100 30
        billing_phone      110 40
        Ex: $addressFields['billing_first_name']['priority'] = 10;
        */
        return $addressFields;
    }
    function handleFieldsShipping($addressFields)
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
        Ex: $addressFields['shipping_first_name']['priority'] = 10;
        */
        return $addressFields;
    }
    function handleCheckoutFields($addressFields)
    {
        //$addressFields['billing']['billing_first_name']['priority'] = 10;
        //$addressFields['shipping']['shipping_first_name']['priority'] = 10;
        //$addressFields['order']['order_comments']['priority'] = 10;
        $addressFields['billing']['billing_phone']['priority'] = 30;
        $addressFields['billing']['billing_email']['priority'] = 40;
        $addressFields['billing']['billing_country']['priority'] = 50;
        $addressFields['billing']['billing_state']['priority'] = 60;
        $addressFields['billing']['billing_city']['priority'] = 70;
        $addressFields['billing']['billing_address_1']['priority'] = 80;
        unset($addressFields['billing']['billing_address_2']);
//        $addressFields['billing']['billing_address_2']['priority'] = 90;
//        $addressFields['billing']['billing_address_2']['placeholder'] = __('Apartment, suite, unit etc.', self::TEXT_DOMAIN );
        $addressFields['billing']['billing_postcode']['priority'] = 90;
        $addressFields['billing']['billing_company']['priority'] = 100;
        $addressFields['billing']['billing_company']['label'] = __('Company name', self::TEXT_DOMAIN);
        $addressFields['shipping']['shipping_country']['priority'] = 30;
        $addressFields['shipping']['shipping_state']['priority'] = 40;
        $addressFields['shipping']['shipping_city']['priority'] = 50;
        $addressFields['shipping']['shipping_address_1']['priority'] = 60;
        unset($addressFields['shipping']['shipping_address_2']);
//        addressFields['shipping']['shipping_address_1']['priority'] = 70;
//        $addressFields['shipping']['shipping_address_2']['placeholder'] = __('Apartment, suite, unit etc.', self::TEXT_DOMAIN );
        $addressFields['shipping']['shipping_postcode']['priority'] = 70;
        $addressFields['shipping']['shipping_company']['priority'] = 80;
        $addressFields['shipping']['shipping_company']['label'] = __('Company name', self::TEXT_DOMAIN);
        unset($addressFields['shipping']['shipping_address_2']);
        return $addressFields;
    }
    function handleCheckoutFieldsDefaultAddress($addressFields)
    {
        /*
        first_name 10
        last_name  20
        company    30  90
        country    40  30
        address_1  50  60
        address_2  60  70
        city       70  40
        state      80  50
        postcode   90  80 */
        /*$addressFields['country']['priority'] = 30;*/
        $addressFields['country']['priority'] = 50;
        $addressFields['state']['priority'] = 60;
        $addressFields['city']['priority'] = 70;
        $addressFields['postcode']['priority'] = 80;
        $addressFields['address_1']['priority'] = 90;
        $addressFields['address_2']['priority'] = 100;
        $addressFields['address_2']['placeholder'] = __('Apartment, suite, unit etc.', self::TEXT_DOMAIN);
        $addressFields['company']['priority'] = 110;
        $addressFields['company']['label'] = __('Company name', self::TEXT_DOMAIN);
        return $addressFields;
    }
    static function getCartContents()
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
        foreach (wc()->cart->get_cart() as $cartItemKey => $cartItem) {
            /**@var  $cartProduct \WC_Product */
            $cartProduct = apply_filters('woocommerce_cart_item_product', $cartItem['data'], $cartItem, $cartItemKey);
            $cartProductId = apply_filters('woocommerce_cart_item_product_id', $cartItem['product_id'], $cartItem, $cartItemKey);
            if ($cartProduct && $cartProduct->exists() && $cartItem['quantity'] > 0 &&
                apply_filters($filterNameCartItemVisible, true, $cartItem, $cartItemKey)) {
                //Image
                $cartProductImage = $cartProduct->get_image([78, 108], ['class' => 'float-xs-left']);
                $cartProductImage = apply_filters('woocommerce_cart_item_thumbnail', $cartProductImage, $cartItem, $cartItemKey);
                //Name
                $cartProductName = $cartProduct->get_name();
                $cartProductName = apply_filters('woocommerce_cart_item_name', $cartProductName, $cartItem, $cartItemKey);
                $cartProductName = wp_kses_post($cartProductName);
                $cartProductName .= UtilsWp::doAction('woocommerce_after_cart_item_name', $cartItem, $cartItemKey);
                //Link
                $cartProductLink = '';
                if ($cartProduct->is_visible()) {
                    $cartProductLink = $cartProduct->get_permalink();
                }
                $cartProductLink = apply_filters('woocommerce_cart_item_permalink', $cartProductLink, $cartItem,
                    $cartItemKey);
                $cartProductLink = esc_url($cartProductLink);
                //Attributes
                $productAttr = wc_get_formatted_cart_item_data($cartItem);
                //Back Order Notifications
                $productBackOrder = '';
                if ($cartProduct->backorders_require_notification() && $cartProduct->is_on_backorder($cartItem['quantity'])) {
                    $textAvailableOnBackOrder = __('Available on backorder', self::TEXT_DOMAIN);
                    $productBackOrder = apply_filters('woocommerce_cart_item_backorder_notification',
                        "<p class='backorder_notification'>{$textAvailableOnBackOrder}</p>", $cartProductId);
                    $productBackOrder = wp_kses_post($productBackOrder);
                }
                //Price
                $cartProductPrice = wc()->cart->get_product_price($cartProduct);
                $cartProductPrice = apply_filters('woocommerce_cart_item_price', $cartProductPrice, $cartItem,
                    $cartItemKey);
                //Quantity
                if ($cartProduct->is_sold_individually()) {
                    $cartProductQty = "<input name='cart[{$cartItemKey}][qty]' value='1' type='hidden'>";
                } else {
                    $cartProductQty = woocommerce_quantity_input([
                        'input_name' => "cart[{$cartItemKey}][qty]",
                        'input_value' => $cartItem['quantity'],
                        'max_value' => $cartProduct->get_max_purchase_quantity(),
                        'min_value' => '0',
                        'product_name' => $cartProductName,
                    ], $cartProduct, false);
                }
                $cartProductQty = apply_filters($filterNameCartItemQty, $cartProductQty, $cartItemKey, $cartItem);
                //Remove Button
                $textRemove = __('Remove', self::TEXT_DOMAIN);
                $urlProductRemove = esc_url(wc_get_cart_remove_url($cartItemKey));
                $cartProductDataAttr = '';
                if ($cartProductId) {
                    $cartProductDataAttr .= " data-product_id='{$cartProductId}'";
                }
                $cartProductSku = esc_attr($cartProduct->get_sku());
                if ($cartProductSku) {
                    $cartProductDataAttr .= " data-product_sku='{$cartProductSku}'";
                }
                if ($cartItemKey) {
                    $cartProductDataAttr .= " data-cart_item_key='{$cartItemKey}'";
                }
                $productRemoveButton = "<span class='product-remove'><a href='{$urlProductRemove}' class='remove'
                title='{$textRemove}' {$cartProductDataAttr} data-bind='click:handleClickCartProductRemove'>
                    <i class='fas fa-trash-alt'></i>
                </a></span>";
                $productRemoveButton = apply_filters('woocommerce_cart_item_remove_link', $productRemoveButton, $cartItemKey);
                //Total
                $cartProductPriceSubTotal = wc()->cart->get_product_subtotal($cartProduct, $cartItem['quantity']);
                $cartProductPriceTotal = apply_filters('woocommerce_cart_item_subtotal', $cartProductPriceSubTotal,
                    $cartItem, $cartItemKey);
                //Content
                $cssCartItem = apply_filters("woocommerce_{$cartItemClass}_class", $cartItemClasses, $cartItem, $cartItemKey);
                $cssCartItem = esc_attr($cssCartItem);
                $contentCartItems .= "<div class='row {$cssCartItem}'>
                <div class='col-xs-6'>
                    <a href='{$cartProductLink}'>
                        {$cartProductImage}
                        <span style='vertical-align: top;'>{$cartProductName}</span>
                        {$cartProductPrice}
                    </a>
                    {$productAttr}
                    {$productBackOrder}
                </div>
                <div class='col-xs-3 text-xs-center'>{$cartProductQty} {$productRemoveButton}</div>
                <div class='col-xs-3 text-xs-center'>{$cartProductPriceTotal}</div></div>";
            }
        }
        return $contentCartItems;
    }
    static function getShippingMethodLabel(WC_Shipping_Rate $method)
    {
        $label = $method->get_label();
        $methodKeyId = str_replace(':', '_', $method->id);
        $methodLabel = get_option("woocommerce_{$methodKeyId}_settings", true)['title'];
        if ($methodLabel) {
            $label = $methodLabel;
        }
        return $label;
    }
    static function getShippingMethodLabelFull(WC_Shipping_Rate $method)
    {
        $label = self::getShippingMethodLabel($method);
        $label .= ': ' . self::getShippingMethodPrice($method);
        return apply_filters('woocommerce_cart_shipping_method_full_label', $label, $method);
    }
    static function getShippingMethodPrice(WC_Shipping_Rate $method)
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
        /**@var $product \WC_Product */
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
                $fieldLabel = "<label for='{$labelId}' class='col-xs-4 title {$fieldLabelCssClasses}' 
                title='{$inputLabelTitle}'>{$fieldLabel}</label>";
            }
            $field = "<p id='{$inputId}_field' class='row form-row {$fieldCssClasses}' data-priority='{$fieldPriority}'{$fieldAttrContainer}>
            {$fieldLabel}<span class='col-xs-8'>{$field}{$fieldDescription}</span></p>";
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
    static function getCartSubtotal(\WC_Product $product, $quantity, bool $showCurrency = true)
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
    /**------------------------------------------------[WooCommerce: Text]*/
    /** Replaces a string in the internationalisation table with a custom value. */
    function handleInit()
    {
        global $l10n;
        if (is_array($l10n)) {
            foreach ($l10n as $pluginKey => $pluginValue) {
                foreach ($pluginValue->entries as $entryKey => $entryValue) {
                    foreach ($entryValue->translations as $translationKey => $translationValue) {
                        if (stristr($translationValue, self::TEXT_DOMAIN)) {
                            $translationChanged = str_ireplace(self::TEXT_DOMAIN, $this->getShopPageName(), $translationValue);
                            $l10n[$pluginKey]->entries[$entryKey]->translations[$translationKey] = $translationChanged;
                        }
                    }
                }
            }
        }
    }
    function handleGetText(string $translation)
    {
        return str_ireplace(self::TEXT_DOMAIN, $this->getShopPageName(), $translation);
    }
    function handleAllPlugins($plugins)
    {
        foreach ($plugins as $key => $value) {
            $plugins[$key]['Name'] = str_replace(['WooCommerce', self::TEXT_DOMAIN, 'Woocommerce'],
                $this->getShopPageName(),
                $plugins[$key]['Name']);
            $plugins[$key]['Description'] = str_replace(['WooCommerce', self::TEXT_DOMAIN, 'Woocommerce'],
                $this->getShopPageName(),
                $plugins[$key]['Description']);
        }
        return $plugins;
    }
    /**------------------------------------------------[WooCommerce: Icon]*/
    function handleAdminHead()
    {
        echo "<style type='text/css'>
            #adminmenu #toplevel_page_woocommerce .menu-icon-generic div.wp-menu-image::before {
                font-family: dashicons, sans-serif !important;
                content: '\\f513' !important;
            }
        </style>";
    }
    function handleAdminMenu()
    {
        global $menu;
        if (is_array($menu)) {
            foreach ($menu as $k => $v) {
                if ($v[0] == 'WooCommerce' || $v[0] == $this->getShopPageName()) {
                    $menu[$k][0] = $this->getShopPageName();
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
                $fields[$fieldKey]['desc'] = str_replace('WooCommerce', $this->getShopPageName(),
                    $fields[$fieldKey]['desc']);
            }
            if (isset($fieldValue['name'])) {
                $fields[$fieldKey]['name'] = str_replace('WooCommerce', $this->getShopPageName(),
                    $fields[$fieldKey]['name']);
            }
        }
        return $fields;
    }
}