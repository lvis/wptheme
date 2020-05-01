<?php
/**
 * Checkout Form
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */
defined('ABSPATH') || exit;

use wp\UtilsWp;

/** @global WC_Checkout $checkout */
if (WC()->cart->is_empty()) {
    wc_get_template('cart/cart-empty.php');
} else {
    $contentCheckoutForm = '';
    if ($checkout->is_registration_enabled() == false && $checkout->is_registration_required() && is_user_logged_in() == false){
        $textYouMustBeLoggedIn = __('You must be logged in to checkout.', 'woocommerce');
        $contentCheckoutForm = apply_filters('woocommerce_checkout_must_be_logged_in_message', $textYouMustBeLoggedIn);
    } else {
        /**---------------------------------- Form: Login*/
        $contentLoginForm = '';
        $optionEnableCheckoutLoginReminder = get_option('woocommerce_enable_checkout_login_reminder');
        if ($optionEnableCheckoutLoginReminder !== 'no' && is_user_logged_in() == false) {
            $textReturningCustomer = __('Returning customer?', 'woocommerce');
            $textClickHereToLogin = __('Click here to login', 'woocommerce');
            $textYouOurClient = __('If you have shopped with us before, please enter your details below. If you are a new customer, please proceed to the Billing section.', 'woocommerce');
            $linkCheckout = wc_get_page_permalink('checkout');
            $textReturningCustomer = apply_filters('woocommerce_checkout_login_message', $textReturningCustomer);
            //TODO Change In tab with customer details in same Navigator
            ob_start();
            woocommerce_login_form(['redirect' => $linkCheckout, 'hidden' => false, 'checkout' => true]);
            $contentLoginForm = ob_get_clean();
            $contentLoginForm = "<div class='text-xs-center'><p>$textYouOurClient</p>
            <a href='#modalLogin' class='text-xs-center button'>{$textClickHereToLogin}</a>
            <div id='modalLogin' class='modal'>{$contentLoginForm}<a class='modal-backdrop' href='#'></a></div></div>";
        }
        /**---------------------------------- Form: Billing And Shipping*/
        $cssOrderReview = 'col-sm-12';
        $contentCheckoutFormFields = '';
        if ($checkout->get_checkout_fields()) {
            $cssOrderReview .= ' col-md-6';
            $actionCheckoutCustomerDetailsBefore = UtilsWp::doAction('woocommerce_checkout_before_customer_details');
            $actionCheckoutCustomerDetailsAfter = UtilsWp::doAction('woocommerce_checkout_after_customer_details');
            $actionCheckoutBilling = UtilsWp::doAction('woocommerce_checkout_billing');
            $actionCheckoutShipping = UtilsWp::doAction('woocommerce_checkout_shipping');
            $contentCheckoutFormFields = "{$actionCheckoutCustomerDetailsBefore}<div class='{$cssOrderReview}'>
            <fieldset id='customer_details'>
            {$actionCheckoutBilling}
            {$actionCheckoutShipping}
            </fieldset></div>{$actionCheckoutCustomerDetailsAfter}";
        }

        $textPaymentForOrder = __('Pay for order', 'woocommerce');
        /**
         * Trigger Action: Before Checkout Form
         * @hooked woocommerce_checkout_login_form - 10
         * @hooked woocommerce_checkout_coupon_form - 10
         */
        remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10);
        remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);
        $actionCheckoutFormBefore = UtilsWp::doAction('woocommerce_before_checkout_form', $checkout);
        $actionCheckoutOrderReviewBefore = UtilsWp::doAction('woocommerce_checkout_before_order_review');
        $actionCheckoutOrderReview = UtilsWp::doAction('woocommerce_checkout_order_review');
        $actionCheckoutOrderReviewAfter = UtilsWp::doAction('woocommerce_checkout_after_order_review');
        $actionCheckoutFormAfter = UtilsWp::doAction('woocommerce_after_checkout_form', $checkout);
        ob_start();
        WC_Shortcode_Cart::output([]);
        $contentCart = ob_get_clean();
        $urlCheckoutForm = esc_url(wc_get_checkout_url());
        $contentCheckoutForm = "{$actionCheckoutFormBefore}
        {$contentCart}
        {$contentLoginForm}
        <form action='{$urlCheckoutForm}' method='post' enctype='multipart/form-data' name='checkout' class='woocommerce-checkout checkout row'>
        {$contentCheckoutFormFields}
        <div class='{$cssOrderReview}'>
        {$actionCheckoutOrderReviewBefore}
        <fieldset id='order_review' class='woocommerce-checkout-review-order'>
            <legend id='order_review_heading'><i class='fas fa-credit-card'></i> {$textPaymentForOrder}</legend>
            {$actionCheckoutOrderReview}
        </fieldset>
        {$actionCheckoutOrderReviewAfter}
        </div></form>{$actionCheckoutFormAfter}";
    }
    echo $contentCheckoutForm;
}