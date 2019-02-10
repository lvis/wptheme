<?php
/**
 * Checkout Form
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */
defined('ABSPATH') || exit;

use wp\WPUtils;

/** @global WC_Checkout $checkout */
if (WC()->cart->is_empty()) {
    wc_get_template('cart/cart-empty.php');
} else {
    $contentCheckoutForm = '';
    if ($checkout->is_registration_enabled() == false &&
        $checkout->is_registration_required() == true &&
        is_user_logged_in() == false) {
        $textYouMustBeLoggedIn = __('You must be logged in to checkout.', 'woocommerce');
        $contentCheckoutForm = apply_filters('woocommerce_checkout_must_be_logged_in_message', $textYouMustBeLoggedIn);
    } else {
        /**---------------------------------- Form: Login*/
        $contentLoginForm = '';
        if (is_user_logged_in() == false && get_option('woocommerce_enable_checkout_login_reminder') !== 'no') {
            $textReturningCustomer = __('Returning customer?', 'woocommerce');
            $textClickHereToLogin = __('Click here to login', 'woocommerce');
            $textYouOurClient = __('If you have shopped with us before, please enter your details below. If you are a new customer, please proceed to the Billing &amp; Shipping section.',
                'woocommerce');
            $linkCheckout = wc_get_page_permalink('checkout');
            $textReturningCustomer = apply_filters('woocommerce_checkout_login_message', $textReturningCustomer);
            //TODO Change In tabb with customer details in same Navigator
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
            $cssOrderReview .= ' col-lg-6';
            $actionCheckoutCustomerDetailsBefore = WPUtils::doAction('woocommerce_checkout_before_customer_details');
            $actionCheckoutCustomerDetailsAfter = WPUtils::doAction('woocommerce_checkout_after_customer_details');
            $actionCheckoutBilling = WPUtils::doAction('woocommerce_checkout_billing');
            $actionCheckoutShipping = WPUtils::doAction('woocommerce_checkout_shipping');
            $textBillingAndShipping = __('Billing &amp; Shipping', 'woocommerce');
            $contentCheckoutFormFields = "{$actionCheckoutCustomerDetailsBefore}
            <div id='customer_details' class='{$cssOrderReview}'>
            {$actionCheckoutBilling}
            {$actionCheckoutShipping}
            </div>{$actionCheckoutCustomerDetailsAfter}";
        }

        $textPaymentForOrder = __('Pay for order', 'woocommerce');
        /**
         * Trigger Action: Before Checkout Form
         * @hooked woocommerce_checkout_login_form - 10
         * @hooked woocommerce_checkout_coupon_form - 10
         */
        remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10);
        remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);
        $actionCheckoutFormBefore = WPUtils::doAction('woocommerce_before_checkout_form', $checkout);
        $actionCheckoutOrderReviewBefore = WPUtils::doAction('woocommerce_checkout_before_order_review');
        $actionCheckoutOrderReview = WPUtils::doAction('woocommerce_checkout_order_review');
        $actionCheckoutOrderReviewAfter = WPUtils::doAction('woocommerce_checkout_after_order_review');
        $actionCheckoutFormAfter = WPUtils::doAction('woocommerce_after_checkout_form', $checkout);
        ob_start();
        WC_Shortcode_Cart::output([]);
        $contentCart = ob_get_clean();
        $urlCheckoutForm = esc_url(wc_get_checkout_url());
        $contentCheckoutForm = "{$actionCheckoutFormBefore}
        {$contentCart}
        {$contentLoginForm}
        <form action='{$urlCheckoutForm}' method='post' enctype='multipart/form-data' name='checkout' class='row woocommerce-checkout checkout'>
        {$contentCheckoutFormFields}
        <div class='{$cssOrderReview}'>
            <h3 id='order_review_heading'>
                <i class='fas fa-hand-holding-usd'></i> 
                <span>{$textPaymentForOrder}</span>
            </h3>
            {$actionCheckoutOrderReviewBefore}
            <div id='order_review' class='woocommerce-checkout-review-order'>
                {$actionCheckoutOrderReview}
            </div>
            {$actionCheckoutOrderReviewAfter}
        </div>
        </form>
        {$actionCheckoutFormAfter}";
    }
    echo $contentCheckoutForm;
}