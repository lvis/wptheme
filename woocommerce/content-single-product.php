<?php
/**
 * The template for displaying product content in the single-product.php template
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

defined('ABSPATH') || exit;

use wp\UtilsWp;

if (post_password_required()) {
    echo get_the_password_form(); // WPCS: XSS ok.
    return;
} else {
    global $post, $product;
    $productId = get_the_ID();
    $productTitle = get_the_title();
    $styleColumnLeft = 'col-xs-3 col-md-4 title align-bottom';
    $styleColumnRight = 'col-xs-9 col-md-8 align-bottom';
    //[SALE]
    $htmlSale = '';
    if ($product->is_on_sale()) {
        $textSale = __('Sale!', 'woocommerce');
        $htmlSale = "<span class='onsale'>{$textSale}</span>";
        $htmlSale = apply_filters('woocommerce_sale_flash', $htmlSale, $post, $product);
    }
    //[SLIDER]
    $productsColumns = apply_filters('woocommerce_product_thumbnails_columns', 4);
    $post_thumbnail_id = $product->get_image_id();
    $wrapper_classes = apply_filters('woocommerce_single_product_image_gallery_classes', [
        'woocommerce-product-gallery',
        'woocommerce-product-gallery--' . (has_post_thumbnail() ? 'with-images' : 'without-images'),
        'woocommerce-product-gallery--columns-' . absint($productsColumns),
        'images',
    ]);
    $textAwaitingImage = __('Awaiting product image', 'woocommerce');
    $imgPlaceHolder = esc_url(wc_placeholder_img_src());
    $htmlSliderContent = "<div class='woocommerce-product-gallery__image--placeholder'>
        <img src='{$imgPlaceHolder}' alt='{$textAwaitingImage}' class='wp-post-image'></div>";
    $htmlSliderContentThumb = '';
    if (has_post_thumbnail() && function_exists('wc_get_gallery_image_html')) {
        $htmlSliderContent = wc_get_gallery_image_html($post_thumbnail_id, true);
        $attachment_ids = $product->get_gallery_image_ids();
        if ($attachment_ids && has_post_thumbnail()) {
            foreach ($attachment_ids as $attachment_id) {
                $htmlSlideThumbs = wc_get_gallery_image_html($attachment_id);
                $htmlSliderContentThumb .= apply_filters('woocommerce_single_product_image_thumbnail_html',
                    $htmlSlideThumbs, $attachment_id);
            }
        }
    }
    $cssImageSlider = esc_attr(implode(' ', array_map('sanitize_html_class', $wrapper_classes)));
    $columnsEscaped = esc_attr($productsColumns);
    //[RATING]
    $htmlRating = '';
    $htmlReviews = '';
    if (post_type_supports('product', 'comments') && 'no' !== get_option('woocommerce_enable_review_rating')) {
        $rating_count = $product->get_rating_count();
        $review_count = $product->get_review_count();
        $average = $product->get_average_rating();
        if ($rating_count > 0) {
            $textRating = __('Rating', 'woocommerce');
            $htmlAverageRating = wc_get_rating_html($average, $rating_count);
            $htmlRating = "<p class='row'><div class='{$styleColumnLeft}'>{$textRating}:</div>
            <div class='{$styleColumnRight}'>{$htmlAverageRating}</div></p>";
            if (comments_open()) {
                $textReviews = __('Reviews', 'woocommerce');
                $textCustomerReview = _n('%s customer review', '%s customer reviews',
                    $review_count, 'woocommerce');
                $htmlReviewCount = "<span class='count'>{$review_count}</span>";
                $customerReviews = sprintf($textCustomerReview, $htmlReviewCount);
                $htmlReviews = "<p class='row'><div class='{$styleColumnLeft}'>{$textReviews}:</div>
                <div class='{$styleColumnRight}'>
                    <a href='#reviews' class='woocommerce-review-link' rel='nofollow'>{$customerReviews}</a>
                </div></p>";
            }
        }
    }
    //[PRODUCT PRICE]
    $textPrice = __('Price:', 'woocommerce');
    $textProdUnavailable = __('Sorry, this product is unavailable. Please choose a different combination.', 'woocommerce');
    $htmlProductPrice = $product->get_price_html();
    //[PRODUCT META: SKU]
    $htmlSKU = '';
    if (wc_product_sku_enabled() && ($product->get_sku() || $product->is_type('variable'))) {
        $sku = $product->get_sku();
        if (!$sku) {
            $sku = __('N/A', 'woocommerce');
        }
        $textSKU = __('SKU:', 'woocommerce');
        $htmlSKU = "<p class='row'>
        <div class='{$styleColumnLeft}'>{$textSKU}</div><div class='{$styleColumnRight}'>{$sku}</div></p>";
    }
    //[PRODUCT META: CATEGORY]
    $countProductCategories = count($product->get_category_ids());
    $textProductCategories = _n('Category:', 'Categories:', $countProductCategories, 'woocommerce');
    $htmlProductCategories = '';
    $productCategories = wp_get_post_terms($productId, 'product_cat');
    foreach ($productCategories as $category) {
        $categoryLink = get_term_link($category->term_id);
        $htmlProductCategories .= "<a href='{$categoryLink}'>{$category->name}</a>";
    }
    //[META: TAG]
    $countProductTags = count($product->get_tag_ids());
    $textProductTags = _n('Tag:', 'Tags:', $countProductTags, 'woocommerce');
    $htmlProductTags = wc_get_product_tag_list($product->get_id(), ', ');

    //[BRAND]
    $textBrand = '';
    $productBrands = '';
    if (function_exists('get_brands')) {
        $terms = get_the_terms($productId, 'product_brand');
        $countBrands = 0;
        if (is_array($terms)) {
            $countBrands = sizeof($terms);
        }
        $taxonomy = get_taxonomy('product_brand');
        $labels = $taxonomy->labels;
        $textBrand = sprintf(_n('%1$s: ', '%2$s: ', $countBrands), $labels->singular_name, $labels->name);
        $productBrands = get_brands($productId, ', ');
    }
    //[ADD TO CART]
    $htmlProductAddToCart = '';
    $productType = $product->get_type();
    switch ($productType) {
        case 'external':
        {
            $htmlBeforeAddToCartForm = UtilsWp::doAction('woocommerce_before_add_to_cart_form');
            $htmlAfterAddToCartForm = UtilsWp::doAction('woocommerce_after_add_to_cart_form');
            $htmlBeforeAddToCartButton = UtilsWp::doAction('woocommerce_before_add_to_cart_button');
            $htmlAfterAddToCartButton = UtilsWp::doAction('woocommerce_after_add_to_cart_button');
            $urlToProduct = $product->add_to_cart_url();
            $htmlFormFields = wc_query_string_form_fields($urlToProduct, [], '', true);
            $urlToProduct = esc_url($urlToProduct);
            $textAddToCart = esc_html($product->single_add_to_cart_text());
            $htmlProductAddToCart = "{$htmlBeforeAddToCartForm}<form action='{$urlToProduct}' method='get' class='cart'>
            {$htmlBeforeAddToCartButton}
            <button type='submit' class='single_add_to_cart_button button alt'>
                <i class='fas fa-cart-plus'></i> <span>{$textAddToCart}</span>
            </button>
            {$htmlFormFields}{$htmlAfterAddToCartButton}</form>{$htmlAfterAddToCartForm}";
            break;
        }
        case 'grouped':
        {
            $groupedProducts = array_map('wc_get_product', $product->get_children());
            $groupedProducts = array_filter($groupedProducts, 'wc_products_array_filter_visible_grouped');
            if ($groupedProducts) {
                $contentAddToCartButton = '';
                $requiredQts = false;
                $groupedProductCols = apply_filters('woocommerce_grouped_product_columns', ['quantity', 'label', 'price'], $product);
                $contentGroupedProducts = '';
                $postPrev = $post;
                foreach ($groupedProducts as $groupedProduct) {
                    /**@var $groupedProduct WC_Product */
                    $groupedProductId = $groupedProduct->get_id();
                    $groupedProductIdEsc = esc_attr($groupedProductId);
                    $post = get_post($groupedProductId);
                    setup_postdata($post);
                    $contentGroupedProduct = '';
                    foreach ($groupedProductCols as $groupedProductColumn) {
                        switch ($groupedProductColumn) {
                            case 'quantity':
                                if (!$groupedProduct->is_purchasable() || $groupedProduct->has_options() ||
                                    !$groupedProduct->is_in_stock()) {
                                    ob_start();
                                    woocommerce_template_loop_add_to_cart();
                                    $contentGroupedProduct .= ob_get_clean();
                                } elseif ($groupedProduct->is_sold_individually()) {
                                    $groupedProductName = esc_attr("quantity[{$groupedProductId}]");
                                    $contentGroupedProduct .= "<input name='{$groupedProductName}' value='1' type='checkbox'  
                                        class='wc-grouped-product-add-to-cart-checkbox'>";
                                } else {
                                    $actionAddToCartQuantityBefore = UtilsWp::doAction('woocommerce_before_add_to_cart_quantity');
                                    $actionAddToCartQuantityAfter = UtilsWp::doAction('woocommerce_after_add_to_cart_quantity');
                                    $groupedProductName = "quantity[{$groupedProductId}]";
                                    $inputMin = apply_filters('woocommerce_quantity_input_min', 0, $groupedProduct);
                                    $inputMax = $groupedProduct->get_max_purchase_quantity();
                                    $inputMax = apply_filters('woocommerce_quantity_input_max', $inputMax, $groupedProduct);
                                    $inputValue = 0;
                                    if (isset($_POST['quantity'][$groupedProductId])) {
                                        $inputValue = $_POST['quantity'][$groupedProductId];
                                        $inputValue = wp_unslash($inputValue);
                                        $inputValue = wc_clean($inputValue);
                                        $inputValue = wc_stock_amount($inputValue);
                                    }
                                    $inputHtml = woocommerce_quantity_input([
                                        'input_name' => $groupedProductName,
                                        'min_value' => $inputMin,
                                        'max_value' => $inputMax,
                                        'input_value' => $inputValue,
                                    ], null, false);
                                    $contentGroupedProduct .= "{$actionAddToCartQuantityBefore}{$inputHtml}{$actionAddToCartQuantityAfter}";
                                }
                                break;
                            case 'label':
                                $labelTitle = $groupedProduct->get_name();
                                if ($groupedProduct->is_visible()) {
                                    $groupedProductPermalink = $groupedProduct->get_permalink();
                                    $groupedProductPermalink = apply_filters('woocommerce_grouped_product_list_link',
                                        $groupedProductPermalink, $groupedProduct->get_id());
                                    $groupedProductPermalink = esc_url($groupedProductPermalink);
                                    $groupedProductName = $groupedProduct->get_name();
                                    $labelTitle = "<a href='{$groupedProductPermalink}'>{$groupedProductName}</a>";
                                }
                                $contentGroupedProduct .= "<label for='product-{$groupedProductId}'>{$labelTitle}</label>";
                                break;
                            case 'price':
                                $contentGroupedProduct = $groupedProduct->get_price_html() . wc_get_stock_html($groupedProduct);
                                break;
                            default:
                                $contentGroupedProduct = '';
                                break;
                        }
                        $actionName = 'woocommerce_grouped_product_list_before_' . $groupedProductColumn;
                        $actionGroupedProductListBefore = UtilsWp::doAction($actionName, $groupedProduct);
                        $actionName = 'woocommerce_grouped_product_list_after_' . $groupedProductColumn;
                        $actionGroupedProductListAfter = UtilsWp::doAction($actionName, $groupedProduct);
                        $actionName = 'woocommerce_grouped_product_list_column_' . $groupedProductColumn;
                        $actionGroupedProductListColumn = apply_filters($actionName, $contentGroupedProduct, $groupedProduct);
                        $groupedProductColumnEsc = esc_attr($groupedProductColumn);
                        $contentGroupedProduct = "{$actionGroupedProductListBefore}
                            <td class='woocommerce-grouped-product-list-item__{$groupedProductColumnEsc}'>
                            {$actionGroupedProductListColumn}</td>{$actionGroupedProductListAfter}";
                    }
                    $groupedProductCss = wc_get_product_class('', $groupedProductId);
                    $groupedProductCss = esc_attr(implode(' ', $groupedProductCss));
                    $contentGroupedProducts .= "<tr class='woocommerce-grouped-product-list-item {$groupedProductCss}' id='product-{$groupedProductIdEsc}'>{$contentGroupedProduct}</tr>";
                    $requiredQts = ($groupedProduct->is_purchasable() && !$groupedProduct->has_options());
                }
                $post = $postPrev;
                setup_postdata($post);
                if ($requiredQts) {
                    $actionAddToCartButtonBefore = UtilsWp::doAction('woocommerce_before_add_to_cart_button');
                    $actionAddToCartButtonAfter = UtilsWp::doAction('woocommerce_after_add_to_cart_button');
                    $textSingleAddToCart = esc_html($product->single_add_to_cart_text());
                    $contentAddToCartButton = "{$actionAddToCartButtonBefore}
                        <button type='submit' class='single_add_to_cart_button button alt'>
                        {$textSingleAddToCart}
                        </button>{$actionAddToCartButtonAfter}";
                }
                $actionAddToCartFormBefore = UtilsWp::doAction('woocommerce_before_add_to_cart_form');
                $actionAddToCartFormAfter = UtilsWp::doAction('woocommerce_after_add_to_cart_form');
                $productIdEsc = esc_attr($product->get_id());
                $urlAddToCartFormAction = esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink()));
                $htmlProductAddToCart = "{$actionAddToCartFormBefore}
                    <form action='{$urlAddToCartFormAction}' method='post' enctype='multipart/form-data' class='cart grouped_form'>
                    <table cellspacing='0' class='woocommerce-grouped-product-list group_table'>
                        <tbody>{$contentGroupedProducts}</tbody>
                    </table>
                    <input type='hidden' name='add-to-cart' value='{$productIdEsc}'>
                    {$contentAddToCartButton}</form>{$actionAddToCartFormAfter}";
            }
            break;
        }
        case 'simple':
        {
            if ($product->is_purchasable()) {
                $htmlStockQuantity = wc_get_stock_html($product);
                if ($product->is_in_stock()) {
                    $addToCartFormBefore = UtilsWp::doAction('woocommerce_before_add_to_cart_form');
                    $addToCartFormAfter = UtilsWp::doAction('woocommerce_after_add_to_cart_form');
                    $addToCartButtonBefore = UtilsWp::doAction('woocommerce_before_add_to_cart_button');
                    $addToCartButtonAfter = UtilsWp::doAction('woocommerce_after_add_to_cart_button');
                    $addToCartQuantityButtonBefore = UtilsWp::doAction('woocommerce_before_add_to_cart_quantity');
                    $addToCartQuantityButtonAfter = UtilsWp::doAction('woocommerce_after_add_to_cart_quantity');
                    $textQuantity = __('Quantity', 'woocommerce');
                    $purchaseQuantityValue = $product->get_min_purchase_quantity();
                    if (isset($_POST['quantity'])) {
                        $purchaseQuantityValue = wc_stock_amount(wp_unslash($_POST['quantity']));
                    }
                    $purchaseQuantityMin = apply_filters('woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product);
                    $purchaseQuantityMax = apply_filters('woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product);
                    $htmlQuantityInput = woocommerce_quantity_input([
                        'min_value' => $purchaseQuantityMin,
                        'max_value' => $purchaseQuantityMax,
                        'input_value' => $purchaseQuantityValue
                    ], $product, false);
                    $productId = esc_attr($product->get_id());
                    $textAddToCart = esc_html($product->single_add_to_cart_text());
                    $urlAddToCartForm = esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink()));
                    $htmlProductAddToCart = "{$addToCartFormBefore}
                    <form action='{$urlAddToCartForm}' method='post' enctype='multipart/form-data' class='cart'>
                    {$addToCartButtonBefore}
                    {$addToCartQuantityButtonBefore}
                    <div class='{$styleColumnLeft}'>{$textQuantity}:</div>
                    <div class='{$styleColumnRight}'>
                        {$htmlQuantityInput}
                        {$addToCartQuantityButtonAfter}
                        <button name='add-to-cart' value='{$productId}' type='submit' class='single_add_to_cart_button button alt'>
                            <i class='fas fa-cart-plus'></i> <span>{$textAddToCart}</span>
                        </button>
                        {$addToCartButtonAfter}
                    </div></form>{$addToCartFormAfter}";
                }
            }
            break;
        }
        case 'variable':
        {
            wp_enqueue_script('wc-add-to-cart-variation');
            $allVariations = count($product->get_children()) <= apply_filters('woocommerce_ajax_variation_threshold', 30, $product);
            $selected_attributes = $product->get_default_attributes();
            $attributes = $product->get_variation_attributes();
            $attribute_keys = array_keys($attributes);
            $htmlVariations = '';
            $available_variations = false;
            if ($allVariations) {
                $available_variations = $product->get_available_variations();
            }
            if ($available_variations) {
                foreach ($attributes as $attrName => $attrValue) {
                    $labelAttributeVariationDropDown = wc_attribute_label($attrName);
                    $idAttributeVariationDropDown = esc_attr(sanitize_title($attrName));
                    ob_start();
                    wc_dropdown_variation_attribute_options(['options' => $attrValue, 'attribute' => $attrName, 'product' => $product]);
                    $htmlAttributeVariationDropDown = ob_get_clean();
                    //TODO Move outside foreach because is added only after last item
                    $htmlResetVariationLink = '';
                    if (end($attribute_keys) === $attrName) {
                        $textClear = __('Clear', 'woocommerce');
                        $htmlResetVariationLink = "<p class='reset_variations'><div class='col-xs-3 title'></div><div class='col-xs-9'>
                            <a class='button' href='#'>{$textClear}</a></div></p>";
                        $htmlResetVariationLink = apply_filters('woocommerce_reset_variations_link', $htmlResetVariationLink);
                        $htmlResetVariationLink = wp_kses_post($htmlResetVariationLink);
                    }
                    $htmlVariations .= "<p class='row'><div class='col-xs-3 title'>
                        <label for='{$idAttributeVariationDropDown}'>{$labelAttributeVariationDropDown}:</label></div>
                        <div class='col-xs-9'>{$htmlAttributeVariationDropDown}</div></p>{$htmlResetVariationLink}";
                }
            } else {
                $textOutOfStockOrUnavailable = __('This product is currently out of stock and unavailable.', 'woocommerce');
                $htmlVariations .= "<p class='stock out-of-stock'>{$textOutOfStockOrUnavailable}</p>";
            }
            $productIdAbs = absint($product->get_id());
            $urlAddToCartFormAction = esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink()));
            $jsonAvailableVariation = htmlspecialchars(wp_json_encode($available_variations));
            $actionAddToCartFormBefore = UtilsWp::doAction('woocommerce_before_add_to_cart_form');
            $actionAddToCartFormAfter = UtilsWp::doAction('woocommerce_after_add_to_cart_form');
            $actionVariationsFormBefore = UtilsWp::doAction('woocommerce_before_variations_form');
            $actionVariationsFormAfter = UtilsWp::doAction('woocommerce_after_variations_form');
            $actionSingleVariationBefore = UtilsWp::doAction('woocommerce_before_single_variation');
            $actionSingleVariationAfter = UtilsWp::doAction('woocommerce_after_single_variation');
            /**
             * Hook: Used to output the cart button and placeholder for variation data.
             * @hooked woocommerce_single_variation - 10 Empty div for variation data.
             * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
             */
            //$actionSingleVariation = UtilsWp::doAction('woocommerce_single_variation');
            $purchaseQtyValue = $product->get_min_purchase_quantity();
            if (isset($_POST['quantity'])) {
                $purchaseQtyValue = wc_stock_amount(wp_unslash($_POST['quantity']));
            }
            $purchaseQtyValue = $product->get_min_purchase_quantity();
            if (isset($_POST['quantity'])) {
                $purchaseQtyValue = wc_stock_amount(wp_unslash($_POST['quantity']));
            }
            $purchaseQtyMin = apply_filters('woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product);
            $purchaseQtyMax = apply_filters('woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product);
            $addToCartButtonBefore = UtilsWp::doAction('woocommerce_before_add_to_cart_button');
            $addToCartButtonAfter = UtilsWp::doAction('woocommerce_after_add_to_cart_button');
            $addToCartQuantityButtonBefore = UtilsWp::doAction('woocommerce_before_add_to_cart_quantity');
            $addToCartQuantityButtonAfter = UtilsWp::doAction('woocommerce_after_add_to_cart_quantity');
            $textQuantity = __('Quantity', 'woocommerce');
            $productIdAbs = absint($product->get_id());
            $textAddToCart = esc_html($product->single_add_to_cart_text());
            $htmlQtyInput = woocommerce_quantity_input([
                'min_value' => $purchaseQtyMin,
                'max_value' => $purchaseQtyMax,
                'input_value' => $purchaseQtyValue
            ], $product, false);
            $htmlProductAddToCart = "{$actionAddToCartFormBefore}
                <form method='post' enctype='multipart/form-data' class='variations_form cart' action='{$urlAddToCartFormAction}' 
                data-product_id='{$productIdAbs}' data-product_variations='{$jsonAvailableVariation}'>
                    {$actionVariationsFormBefore}
                    <div class='variations'>{$htmlVariations}</div>
                    <div class='single_variation_wrap'>
                        {$actionSingleVariationBefore}
                        <div class='woocommerce-variation single_variation'></div>
                        <div class='woocommerce-variation-add-to-cart variations_button'>
                            {$addToCartButtonBefore}
                            {$addToCartQuantityButtonBefore}
                            <p class='row'>
                                <div class='col-xs-3 title'>{$textQuantity}:</div>
                                <div class='col-xs-9'>{$htmlQtyInput}</div>
                            </p>
                            {$addToCartQuantityButtonAfter}
                            <p class='row'>
                                <div class='col-xs-3 title'></div>
                                <div class='col-xs-9'>
                                    <button type='submit' class='single_add_to_cart_button button alt'>{$textAddToCart}</button>
                                </div>
                            </p>
                            {$addToCartButtonAfter}
                            <input type='hidden' name='add-to-cart' value='{$productIdAbs}'>
                            <input type='hidden' name='product_id' value='{$productIdAbs}'>
                            <input type='hidden' name='variation_id' class='variation_id' value='0'>
                        </div>
                        {$actionSingleVariationAfter}
                    </div>
                    {$actionVariationsFormAfter}
                </form>{$actionAddToCartFormAfter}
                <script type='text/template' id='tmpl-variation-template'>
                    <div class='woocommerce-variation-description'>{{{ data.variation.variation_description }}}</div>
                    <p class='woocommerce-variation-price row'>
                        <div class='col-xs-3 title'>{$textPrice}</div>
                        <div class='col-xs-9'>{{{ data.variation.price_html }}}</div>
                    </p>
                    <div class='woocommerce-variation-availability'>{{{ data.variation.availability_html }}}</div>
                </script>
                <script type='text/template' id='tmpl-unavailable-variation-template'>
                    <p>{$textProdUnavailable}</p>
                </script>";
            break;
        }
    }
    // [SHARE]
    $htmlProductShare = UtilsWp::doAction('woocommerce_share');
    //[ATTRIBUTES]
    $htmlProductMeasures = '';
    $display_dimensions = $product->has_weight() || $product->has_dimensions();
    $display_dimensions = apply_filters('wc_product_enable_dimensions_display', $display_dimensions);
    if ($display_dimensions) {
        if ($product->has_weight()) {
            $textWeight = __('Weight', 'woocommerce');
            $productWeight = esc_html(wc_format_weight($product->get_weight()));
            $htmlProductMeasures .= "<p class='row'>
        <div class='{$styleColumnLeft}'>{$textWeight}:</div>
        <div class='{$styleColumnRight}'>{$productWeight}</div></p>";
        }

        if ($product->has_dimensions()) {
            $textDimensions = __('Dimensions', 'woocommerce');
            $productDimensions = esc_html(wc_format_dimensions($product->get_dimensions(false)));
            $htmlProductMeasures .= "<p class='row'>
        <div class='{$styleColumnLeft}'>{$textDimensions}:</div>
        <div class='{$styleColumnRight}'>{$productDimensions}</div></p>";
        }
    }

    if ($htmlProductMeasures) {
        $textAdditionalInfo = __('Additional information', 'woocommerce');
        $htmlProductMeasures = "<h4>{$textAdditionalInfo}</h4>{$htmlProductMeasures}";
    }
    $htmlProductAttributes = '';
    $attributes = array_filter($product->get_attributes(), 'wc_attributes_array_filter_visible');
    foreach ($attributes as $attribute) {
        $textAttributeName = wc_attribute_label($attribute->get_name());
        $values = [];
        if ($attribute->is_taxonomy()) {
            $attribute_taxonomy = $attribute->get_taxonomy_object();
            $attribute_values = wc_get_product_terms($product->get_id(), $attribute->get_name(), ['fields' => 'all']);

            foreach ($attribute_values as $attribute_value) {
                $value_name = esc_html($attribute_value->name);

                if ($attribute_taxonomy->attribute_public) {
                    $termId = $attribute_value->term_id;
                    $attributeName = $attribute->get_name();
                    $termLink = get_term_link($termId, $attributeName);
                    $termLink = esc_url($termLink);
                    $values[] = "<a href='{$termLink}' rel='tag'>{$value_name}</a>";
                } else {
                    $values[] = $value_name;
                }
            }
        } else {
            $values = $attribute->get_options();
            foreach ($values as &$value) {
                $value = make_clickable(esc_html($value));
            }
        }
        $productAttributeValues = wptexturize(implode(', ', $values));
        $productAttributeValues = apply_filters('woocommerce_attribute', $productAttributeValues, $attribute, $values);
        $htmlProductAttributes .= "<p class='row'>
        <div class='{$styleColumnLeft}'>{$textAttributeName}:</div>
        <div class='{$styleColumnRight}'>{$productAttributeValues}</div></p>";
    }
    //[REVIEWS]
    ob_start();
    comments_template();
    $htmlReviewsPage = ob_get_clean();
    //[UP SELL]
    $htmlProductUpSell = '';
    $productsColumns = 4;
    $productsLimit = '-1';
    $productsOrderBy = 'rand';
    $productsOrder = 'desc';
    // Handle the legacy filter which controlled posts per page etc.
    $argsUpSell = apply_filters('woocommerce_upsell_display_args', [
        'columns' => $productsColumns,
        'posts_per_page' => $productsLimit,
        'orderby' => $productsOrderBy,
    ]);
    wc_set_loop_prop('name', 'up-sells');
    //[Columns]
    if (isset($argsUpSell['columns'])) {
        $productsColumns = $argsUpSell['columns'];
    }
    wc_set_loop_prop('columns', apply_filters('woocommerce_upsells_columns', $productsColumns));
    //[Limit]
    if (isset($argsUpSell['posts_per_page'])) {
        $productsLimit = $argsUpSell['posts_per_page'];
    }
    $productsLimit = apply_filters('woocommerce_upsells_total', $productsLimit);
    //[Order]
    if (isset($argsUpSell['orderby'])) {
        $productsOrderBy = $argsUpSell['orderby'];
    }
    $productsOrderBy = apply_filters('woocommerce_upsells_orderby', $productsOrderBy);
    $upSellProductIds = $product->get_upsell_ids();
    $productsUpSell = array_map('wc_get_product', $upSellProductIds);
    $productsUpSell = array_filter($productsUpSell, 'wc_products_array_filter_visible');
    $productsUpSell = wc_products_array_orderby($productsUpSell, $productsOrderBy, $productsOrder);
    if ($productsLimit > 0) {
        $productsUpSell = array_slice($productsUpSell, 0, $productsLimit);
    }
    if ($productsUpSell) {
        $htmlProductUpSellStart = woocommerce_product_loop_start(false);
        foreach ($productsUpSell as $upSellProduct) {
            $post_object = get_post($upSellProduct->get_id());
            setup_postdata($GLOBALS['post'] =& $post_object);
            ob_start();
            wc_get_template_part('content', 'product');
            $htmlProductUpSell .= ob_get_clean();
        }
        $htmlProductUpSellEnd = woocommerce_product_loop_end(false);
        wp_reset_postdata();
        $textYouMayLike = __('You may also like&hellip;', 'woocommerce');
        $htmlProductUpSell = "<section class='up-sells upsells products'>
        <h4 class='title text-xs-center'>{$textYouMayLike}</h4>
        {$htmlProductUpSellStart}{$htmlProductUpSell}{$htmlProductUpSellEnd}</section>";
    }
    //[RELATED]
    $htmlProductsRelated = '';
    $productsColumns = 4;
    $argsRelated = apply_filters('woocommerce_output_related_products_args', [
        'posts_per_page' => 4,
        'columns' => $productsColumns,
        'orderby' => 'rand',
        'order' => 'desc',
    ]);

    $productsRelated = wc_get_related_products($productId, $argsRelated['posts_per_page'], $upSellProductIds);
    $productsRelated = array_map('wc_get_product', $productsRelated);
    $productsRelated = array_filter($productsRelated, 'wc_products_array_filter_visible');
    $productsRelated = wc_products_array_orderby($productsRelated, $argsRelated['orderby'], $argsRelated['order']);
    wc_set_loop_prop('name', 'related');
    //[Columns]
    if (isset($argsUpSell['columns'])) {
        $productsColumns = $argsUpSell['columns'];
    }
    $productsColumns = apply_filters('woocommerce_related_products_columns', $productsColumns);
    wc_set_loop_prop('columns', $productsColumns);
    if (isset($productsRelated)) {
        $htmlProductsRelatedStart = woocommerce_product_loop_start(false);
        foreach ($productsRelated as $relatedProduct) {
            $post_object = get_post($relatedProduct->get_id());
            setup_postdata($GLOBALS['post'] =& $post_object);
            ob_start();
            wc_get_template_part('content', 'product');
            $htmlProductsRelated .= ob_get_clean();
        }
        $htmlProductsRelatedEnd = woocommerce_product_loop_end(false);
        wp_reset_postdata();
        if ($htmlProductsRelated) {
            $textRelatedProducts = __('Related products', 'woocommerce');
            $htmlProductsRelated = "<section class='related products'>
            <h4 class='title text-xs-center'>{$textRelatedProducts}</h4>
            {$htmlProductsRelatedStart}{$htmlProductsRelated}{$htmlProductsRelatedEnd}</section>";
        }
    }
    /**
     * Hook: woocommerce_before_single_product.
     * @hooked wc_print_notices - 10
     */
    $actionSingleProductBefore = UtilsWp::doAction('woocommerce_before_single_product');
    $actionSingleProductAfter = UtilsWp::doAction('woocommerce_after_single_product');
    $actionProductMetaStart = UtilsWp::doAction('woocommerce_product_meta_start');
    //Variation
    $contentVariations = '';
    if (is_a($product, WC_Product_Variable::class)) {
        //$actionProductMetaEnd = UtilsWp::doAction('woocommerce_product_meta_end');
        $actionProductMetaEnd = '';
        $textOptions = __('Variations', 'woocommerce');
        $contentVariations = "<h4>{$textOptions}</h4>{$actionProductMetaEnd}";
    }
    //Description
    $contentDescription = '';
    $htmlProductDescription = get_the_content();
    if ($htmlProductDescription) {
        $textDescription = __('Description', 'woocommerce');
        $contentDescription = "<div id='description' class='col-md-12'>
        <h4 class='title text-xs-center'><i class='fas fa-file-alt'></i> {$textDescription}</h4>
        <p>{$htmlProductDescription}</p></div>";
    }
    $cssProduct = esc_attr(join(' ', wc_get_product_class('', $productId)));
    echo "{$actionSingleProductBefore}
    <div id='product-{$productId}' class='{$cssProduct}'>
        <div class='container'>
            <h1 class='title text-xs-center'>{$productTitle}</h1>
            <div class='{$cssImageSlider} col-sm-5' data-columns='{$columnsEscaped}'>
                <figure class='woocommerce-product-gallery__wrapper'>
                {$htmlSliderContent}
                <figcaption>{$htmlSale}</figcaption>
                {$htmlSliderContentThumb}
                </figure>
            </div>
            <div class='summary entry-summary  col-sm-7'>
                <div class='product_meta'>
                    {$actionProductMetaStart}
                    {$contentVariations}
                    {$htmlSKU}
                    <p class='row'>
                        <div class='{$styleColumnLeft}'>{$textBrand}</div>
                        <div class='{$styleColumnRight}'>{$productBrands}</div>
                    </p>
                    <p class='row'>
                        <div class='{$styleColumnLeft}'>{$textProductCategories}</div>
                        <div class='{$styleColumnRight}'>{$htmlProductCategories}</div>
                    </p>
                    
                    <p class='row'>
                        <div class='{$styleColumnLeft}'>{$textProductTags}</div>
                        <div class='{$styleColumnRight}'>{$htmlProductTags}</div>
                    </p>
                    {$htmlProductMeasures}
                    {$htmlProductAttributes}
                    {$htmlRating}
                    {$htmlReviews}
                    {$htmlProductShare}
                    <p class='row'>
                        <div class='{$styleColumnLeft}'>{$textPrice}</div>
                        <div class='{$styleColumnRight}'>{$htmlProductPrice}</div>
                    </p>
                    {$htmlProductAddToCart}
                </div>
            </div>
        </div>
        {$contentDescription}
        {$htmlReviewsPage}
        {$htmlProductUpSell}
        {$htmlProductsRelated}
    </div>
    {$actionSingleProductAfter}";
}