<?php
/**
 * Cart totals
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-totals.php.
 * @see        https://docs.woocommerce.com/document/template-structure/
 * @author     WooThemes
 * @package    WooCommerce/Templates
 * @version    2.3.6
 */
defined('ABSPATH') || exit;

use wp\UtilsWp;
use wp\UtilsWooCommerce;

$cssTitle = 'col-xs-9 title';
$contentCartContentCount = wc()->cart->get_cart_contents_count();
$contentCartContentCount = sprintf( _n( '%s item', '%s items', $contentCartContentCount ), number_format_i18n( $contentCartContentCount ) );
$contentCoupons = '';
if (wc_coupons_enabled()) {
    $cartCoupons = wc()->cart->get_coupons();
    foreach ($cartCoupons as $couponKey => $coupon) {
        if (is_string($coupon)) {
            $coupon = new WC_Coupon($coupon);
        }
        $couponCode = $coupon->get_code();
        $amount = wc()->cart->get_coupon_discount_amount($couponCode, wc()->cart->display_cart_ex_tax);
        $htmlDiscountAmount = '-' . wc_price($amount);
        if (empty($amount)) {
            $htmlDiscountAmount = '';
            if ($coupon->get_free_shipping()) {
                $htmlDiscountAmount = __('Free shipping coupon', 'woocommerce');
            }
        }
        $htmlDiscountAmount = apply_filters('woocommerce_coupon_discount_amount_html', $htmlDiscountAmount, $coupon);
        /** Remove Button*/
        $linkCouponRemove = add_query_arg('remove_coupon', rawurlencode($couponCode), wc_get_checkout_url());
        $linkCouponRemove = esc_url($linkCouponRemove);
        $textRemove = __('Remove', 'woocommerce');
        $couponCode = esc_attr($couponCode);
        $couponRemoveButton = "<p><a href='{$linkCouponRemove}' class='woocommerce-remove-coupon' 
        data-coupon='{$couponCode}' data-bind='click:handleClickCouponRemove'>
            <i class='fal fa-trash-alt'></i><span> 
            {$textRemove}</span>
        </a></p>";
        $couponCodeEsc = esc_attr(sanitize_title($couponKey));
        $couponLabel = wc_cart_totals_coupon_label($coupon, false);
        $couponDescription = $coupon->get_description();
        $contentCoupons .= "<div class='cart-discount coupon-{$couponCodeEsc}'>
        <div class='col-xs-9'><strong><i class='fal fa-ticket-alt'></i> {$couponCode}:</strong> {$couponDescription}</div>
        <div class='col-xs-3 text-xs-center'>{$htmlDiscountAmount}{$couponRemoveButton}</div></div>";
    }
    $textCoupon = __('Coupon', 'woocommerce');
    $textCoupons = __('Coupons', 'woocommerce');
    $textIfYouHaveCoupon = __('If you have a coupon code, please apply it below.', 'woocommerce');
    $textCouponCode = __('Coupon code', 'woocommerce');
    $textApplyCoupon = __('Apply coupon', 'woocommerce');
    $textAddCoupon = __('Add coupon', 'woocommerce');
    $textAdd = __('Add');
    $actionCartCoupon = UtilsWp::doAction('woocommerce_cart_coupon');
    $contentCoupons = "<div class='row coupons'>
    <div class='col-xs-12 clearfix'><span class='title'><i class='fas fa-ticket-alt'></i> {$textCoupons}:</span> {$textIfYouHaveCoupon}</div>
    {$contentCoupons}
    <div class='coupon'>
        <div class='col-xs-9'>
            <input id='coupon_code' name='coupon_code' type='text' placeholder='{$textCouponCode}'
            data-bind='textInput:couponCode, event:{keypress:handleKeyPressAddCoupon}'>
        </div>
        <div class='col-xs-3 text-xs-center'>
            <input type='submit' name='apply_coupon' value='{$textAdd}' title='{$textAddCoupon}' class='link' 
            data-bind='click:handleClickCouponAdd, enable:hasCouponCode'>
        </div>
    {$actionCartCoupon}</div>
    </div>";
}

$contentShipping = '';
if (wc()->cart->needs_shipping()) {
    if (wc()->cart->show_shipping()) {
        $packages = wc()->shipping()->get_packages();
        $showPackageDetails = (count($packages) > 1);
        foreach ($packages as $index => $package) {
            $shippingMethodChosen = '';
            if (isset(wc()->session->chosen_shipping_methods[$index])) {
                $shippingMethodChosen = wc()->session->chosen_shipping_methods[$index];
            }
            $shippingMethods = $package['rates'];
            $countAvailableMethods = count($shippingMethods);
            $contentShippingMethod = '';
            $textNoShipping = '';
            if ($countAvailableMethods >= 1) {
                $contentShippingMethods = '';
                foreach ($shippingMethods as $shippingMethod) {
                    /**@var $shippingMethod WC_Shipping_Rate */
                    $actionShippingRateAfter = UtilsWp::doAction('woocommerce_after_shipping_rate', $shippingMethod, $index);
                    $methodId = "shipping_method_{$index}_" . sanitize_title($shippingMethod->id);
                    $methodValue = esc_attr($shippingMethod->id);
                    $methodLabel = UtilsWooCommerce::getShippingMethodLabel($shippingMethod);
                    $methodPrice = UtilsWooCommerce::getShippingMethodPrice($shippingMethod);
                    $methodChecked = checked($shippingMethod->id, $shippingMethodChosen, false);
                    $contentShippingMethods .= "<div class='col-xs-9'>
                    <input id='{$methodId}' name='shipping_method[{$index}]' data-index='{$index}' 
                    type='radio' value='{$methodValue}' class='shipping_method' {$methodChecked}>
                    <label for='{$methodId}'>{$methodLabel}</label>{$actionShippingRateAfter}</div>
                    <div class='col-xs-3 text-xs-center'>{$methodPrice}</div>";
                }
                $contentShippingMethod = "<div id='shipping_method'>{$contentShippingMethods}</div>";
            } else if (wc()->customer->has_calculated_shipping()) {
//                $textNoShipping = wpautop(__('There are no shipping methods available. Please ensure that your address has been entered correctly, or contact us if you need any help.', 'woocommerce'));
                $textNoShipping = __('There are no shipping methods available. Please ensure that your address has been entered correctly, or contact us if you need any help.', 'woocommerce');
                if (is_cart()) {
                    $textNoShipping = apply_filters('woocommerce_cart_no_shipping_available_html', $textNoShipping);
                } else {
                    $textNoShipping = apply_filters('woocommerce_no_shipping_available_html', $textNoShipping);
                }
                $textNoShipping = " {$textNoShipping}";
            } else if (!is_cart()) {
                $textNoShipping = __('Enter your full address to see shipping costs.', 'woocommerce');
            }
            $packageName = __('Shipping', 'woocommerce');
            $nextIndex = $index + 1;
            if ($nextIndex > 1) {
                $packageName = sprintf(_x('Shipping %d', 'shipping packages', 'woocommerce'), $nextIndex);
            }
            $packageName = apply_filters('woocommerce_shipping_package_name', $packageName, $index, $package);
            $packageName = wp_kses_post($packageName);
            if ($showPackageDetails) {
                $productNames = [];
                if (count($packages) > 1) {
                    foreach ($package['contents'] as $item_id => $values) {
                        $productNames[$item_id] = $values['data']->get_name() . ' &times;' . $values['quantity'];
                    }
                    $productNames = apply_filters('woocommerce_shipping_package_details_array', $productNames, $package);
                }
                $package_details = implode(', ', $productNames);
                $contentShippingMethod .= "<p class='woocommerce-shipping-contents'>{$package_details}</p>";
            }

            $contentShipping .= "<div class='row shipping'>
            <div class='col-xs-12 clearfix'>
                <i class='fas fa-shipping-fast'></i> 
                <span class='title'>{$packageName}:</span>{$textNoShipping}
            </div>
            {$contentShippingMethod}</div>";
        }
        $actionReviewOrderShippingBefore = UtilsWp::doAction('woocommerce_review_order_before_shipping');
        $actionReviewOrderShippingAfter = UtilsWp::doAction('woocommerce_review_order_after_shipping');
        $actionCartTotalsShippingBefore = UtilsWp::doAction('woocommerce_cart_totals_before_shipping');
        $actionCartTotalsShippingAfter = UtilsWp::doAction('woocommerce_cart_totals_after_shipping');
        $contentShipping = "{$actionReviewOrderShippingBefore}{$contentShipping}{$actionReviewOrderShippingAfter}";
    } else if (get_option('woocommerce_enable_shipping_calc') === 'yes') {
        //yourtheme/woocommerce/cart/shipping-calculator.php.
        wp_enqueue_script('wc-country-select');
        $contentCountryOptions = '';
        foreach (wc()->countries->get_shipping_countries() as $key => $value) {
            $key = esc_attr($key);
            $countrySelected = selected(wc()->customer->get_shipping_country(), $key, false);
            $value = esc_html($value);
            $contentCountryOptions .= "<option value='{$key}' {$countrySelected}>{$value}</option>";
        }
        $contentShippingState = '';
        if (apply_filters('woocommerce_shipping_calculator_enable_state', true)) {
            $currentShippingCountry = wc()->customer->get_shipping_country();
            $states = wc()->countries->get_states($currentShippingCountry);
            $currentShippingState = wc()->customer->get_shipping_state();
            $textStateCountry = __('State / County', 'woocommerce');
            if (is_array($states) && empty($states)) {
                $contentShippingState = "<input id='calc_shipping_state' name='calc_shipping_state' type='hidden' 
                placeholder='{$textStateCountry}'>";
            } elseif (is_array($states)) {
                $textSelectState = __('Select a state&hellip;', 'woocommerce');
                $contentShippingStateOptions = '';
                foreach ($states as $stateId => $stateName) {
                    $stateSelected = selected($currentShippingState, $stateId, false);
                    $stateId = esc_attr($stateId);
                    $stateName = esc_html($stateName);
                    $contentShippingStateOptions .= "<option value='{$stateId}' {$stateSelected}>{$stateName}</option>";
                }
                $contentShippingState = "<select id='calc_shipping_state' name='calc_shipping_state' 
            class='state_select' placeholder='{$textStateCountry}'>
            <option value=''>{$textSelectState}</option>{$contentShippingStateOptions}</select>";
            } else {
                $currentShippingState = esc_attr($currentShippingState);
                $contentShippingState = "<input id='calc_shipping_state' name='calc_shipping_state' type='text' 
                value='{$currentShippingState}' placeholder='{$textStateCountry}'>";
            }
            $contentShippingState = "<p id='calc_shipping_state_field' class='form-row form-row-wide'>{$contentShippingState}</p>";
        }
        $contentShippingCity = '';
        if (apply_filters('woocommerce_shipping_calculator_enable_city', true)) {
            $textCity = __('City', 'woocommerce');
            $valueShippingCity = esc_attr(wc()->customer->get_shipping_city());
            $contentShippingCity = "<p id='calc_shipping_city_field' class='form-row form-row-wide'>
            <input id='calc_shipping_city' name='calc_shipping_city' type='text' class='input-text' 
            value='{$valueShippingCity}' placeholder='{$textCity}'></p>";
        }
        $contentShippingPostCode = '';
        if (apply_filters('woocommerce_shipping_calculator_enable_postcode', true)) {
            $textPostCode = __('Postcode / ZIP', 'woocommerce');
            $valueShippingPostCode = esc_attr(wc()->customer->get_shipping_postcode());
            $contentShippingPostCode = "<p id='calc_shipping_postcode_field' class='form-row form-row-wide'>
            <input id='calc_shipping_postcode' name='calc_shipping_postcode' type='text' class='input-text' 
            value='{$valueShippingPostCode}' placeholder='{$textPostCode}'></p>";
        }
        $textUpdateTotals = __('Update totals', 'woocommerce');
        $textCalculateShipping = __('Calculate shipping', 'woocommerce');
        $textSelectCountry = __('Select a country&hellip;', 'woocommerce');
        $actionShippingCalculatorBefore = UtilsWp::doAction('woocommerce_before_shipping_calculator');
        $actionShippingCalculatorAfter = UtilsWp::doAction('woocommerce_after_shipping_calculator');
        $urlCart = esc_url(wc_get_cart_url());
        $nonceShippingCalculator = wp_nonce_field('woocommerce-shipping-calculator',
            'woocommerce-shipping-calculator-nonce', true, false);
        $textShipping = __('Shipping', 'woocommerce');
        $contentShipping = "<div class='row shipping'>
        <div class='col-xs-8 title'><i class='fas fa-truck'></i> {$textShipping}</div>
        <div class='col-xs-4'>{$actionShippingCalculatorBefore}
        <form class='woocommerce-shipping-calculator' action='{$urlCart}' method='post'>
        <p id='calc_shipping_country_field' class='form-row form-row-wide'>
            <select id='calc_shipping_country' name='calc_shipping_country' rel='calc_shipping_state' 
            class='country_to_state country_select'>
                <option value=''>{$textSelectCountry}</option>
                {$contentCountryOptions}
            </select>
        </p>
        {$contentShippingState}
        {$contentShippingCity}
        {$contentShippingPostCode}
        <button type='submit' name='calc_shipping' value='1'>{$textUpdateTotals}</button>
        {$nonceShippingCalculator}</form>{$actionShippingCalculatorAfter}</div></div>";
    }
}
$contentFees = '';
$cartFees = wc()->cart->get_fees();
foreach ($cartFees as $fee) {
    $feeAmount = wc_price($fee->total);
    if (wc()->cart->display_prices_including_tax()) {
        $feeAmount = wc_price($fee->total + $fee->tax);
    }
    $feeAmount = apply_filters('woocommerce_cart_totals_fee_html', $feeAmount, $fee);
    $feeLabel = esc_html($fee->name);
    $contentFees .= "<div class='row fee'>
    <div class='col-xs-4 title'>{$feeLabel}</div>
    <div class='col-xs-8'>{$feeAmount}</div></div>";
}
$contentTax = '';
if (wc_tax_enabled() && !wc()->cart->display_prices_including_tax()) {
    $taxable_address = wc()->customer->get_taxable_address();
    $textEstimated = '';
    if (wc()->customer->is_customer_outside_base() && !wc()->customer->has_calculated_shipping()) {
        $textEstimated = wc()->countries->estimated_for_prefix($taxable_address[0]);
        $textEstimated .= wc()->countries->countries[$taxable_address[0]];
        $textEstimated = sprintf(__('(estimated for %s)', 'woocommerce'), $textEstimated);
    }
    if ('itemized' === get_option('woocommerce_tax_total_display')) {
        foreach (wc()->cart->get_tax_totals() as $code => $tax) {
            $code = sanitize_title($code);
            $taxLabel = esc_html($tax->label);
            $taxTotal = wp_kses_post($tax->formatted_amount);
            $contentTax = "<div class='row tax-rate tax-rate-{$code}'>
            <div class='col-xs-4 title'>{$taxLabel}{$textEstimated}</div>
            <div class='col-xs-8'>{$taxTotal}</div></div>";
        }
    } else {
        $taxLabel = wc()->countries->tax_or_vat();
        $taxLabel = esc_html($taxLabel);
        $taxTotal = wc()->cart->get_taxes_total();
        $taxTotal = wc_price($taxTotal);
        $taxTotal = apply_filters('woocommerce_cart_totals_taxes_total_html', $taxTotal);
        $contentTax = "<div class='row tax-total'>
        <div class='col-xs-4 title'>{$taxLabel}{$textEstimated}</div>
        <div class='col-xs-8'>{$taxTotal}</div></div>";
    }
}
//$textTotal = __('Cart totals', 'woocommerce');
$textTotal = __('Order Total', 'woocommerce');
$htmlCartOrderTotal = wc()->cart->get_total();
$htmlCartOrderTotal = "<strong>{$htmlCartOrderTotal}</strong>";
if (wc_tax_enabled() && wc()->cart->display_prices_including_tax()) {
    // If prices are tax inclusive, show taxes here.
    $taxValues = [];
    $cartTaxTotal = wc()->cart->get_tax_totals();
    if (get_option('woocommerce_tax_total_display') === 'itemized') {
        foreach ($cartTaxTotal as $code => $tax) {
            $taxValues[] = sprintf('%s %s', $tax->formatted_amount, $tax->label);
        }
    } elseif (!empty($cartTaxTotal)) {
        $cartTaxesTotal = wc()->cart->get_taxes_total(true, true);
        $cartTaxesTotal = wc_price($cartTaxesTotal);
        $taxValues[] = sprintf('%s %s', $cartTaxesTotal, wc()->countries->tax_or_vat());
    }
    if (!empty($taxValues)) {
        $taxable_address = wc()->customer->get_taxable_address();
        /* translators: %s: country name */
        $textEstimated = '';
        if (wc()->customer->is_customer_outside_base() && !wc()->customer->has_calculated_shipping()) {
            $taxableAddress = $taxable_address[0];
            $estimatedValue = wc()->countries->estimated_for_prefix($taxableAddress) .
                wc()->countries->countries[$taxableAddress];
            $textEstimated = sprintf(' ' . __('estimated for %s', 'woocommerce'), $estimatedValue);
        };
        /* translators: %s: tax information */
        $includeTaxValue = implode(', ', $taxValues) . $textEstimated;
        $contentIncludeTax = sprintf(__('(includes %s)', 'woocommerce'), $includeTaxValue);
        $htmlCartOrderTotal .= "<small class='includes_tax'>{$contentIncludeTax}</small>";
    }
}
$contentCheckoutButton = '';
$hasCheckoutShortCode = wc_post_content_has_shortcode('woocommerce_checkout');
$hadCheckoutDefined = defined('WOOCOMMERCE_CHECKOUT');
$isCheckoutFilter = apply_filters('woocommerce_is_checkout', false);
$checkoutLink = wc_get_page_permalink('checkout');
//$_GET['wc-ajax'] == 'update_order_review';
if (is_checkout()) {
    $actionOrderTotalBefore = UtilsWp::doAction('woocommerce_review_order_before_order_total');
    $actionOrderTotalAfter = UtilsWp::doAction('woocommerce_review_order_after_order_total');
} else if (is_cart()) {
    $actionOrderTotalBefore = UtilsWp::doAction('woocommerce_cart_totals_before_order_total');
    $actionOrderTotalAfter = UtilsWp::doAction('woocommerce_cart_totals_after_order_total');
    $urlCheckout = esc_url(wc_get_checkout_url());
    $textProceedToCheckout = __('Proceed to checkout', 'woocommerce');
    $contentCheckoutButton = "<div class='row'><div class='col-xs-12 text-xs-right'>
    <a href='{$urlCheckout}' class='button'>{$textProceedToCheckout}</a></div></div>";
}
$actionCartTotalsBefore = UtilsWp::doAction('woocommerce_before_cart_totals');
$actionCartTotalsAfter = UtilsWp::doAction('woocommerce_after_cart_totals');
$htmlCartOrderTotal = apply_filters('woocommerce_cart_totals_order_total_html', $htmlCartOrderTotal);
$textSubtotal = __('Subtotal', 'woocommerce');
$contentCartSubtotal = wc()->cart->get_cart_subtotal();
echo "<div class='col-xs-12 col-lg-6'>
{$actionCartTotalsBefore}
<section class='card cart_totals' data-bind='css:cssForm'><div class='card-content'>
    <div class='row cart-subtotal'>
        <div class='{$cssTitle}'><i class='fas fa-chart-pie'></i> {$textSubtotal}:</div>
        <div class='col-xs-3 text-xs-center'>{$contentCartSubtotal}</div>
    </div>
    {$contentShipping}
    {$contentCoupons}
    {$contentFees}
    {$contentTax}
    {$actionOrderTotalBefore}
    <div class='row order-total'>
        <div class='{$cssTitle}'><i class='fas fa-hand-holding-usd'></i> {$textTotal}:</div>
        <div class='col-xs-3 text-xs-center'>{$htmlCartOrderTotal}</div>
    </div>
    {$contentCheckoutButton}
    {$actionOrderTotalAfter}
</div></section>
{$actionCartTotalsAfter}</div>";