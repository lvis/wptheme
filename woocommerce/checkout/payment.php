<?php
/**
 * Checkout Payment Section
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/payment.php.
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.3
 */
defined( 'ABSPATH' ) || exit;
use wp\UtilsWp;
if ( is_ajax() == false ) {
    $actionReviewOrderBefore = UtilsWp::doAction( 'woocommerce_review_order_before_payment' );
    $actionReviewOrderAfter = UtilsWp::doAction( 'woocommerce_review_order_after_payment' );
}
$htmlPaymentMethods = '';
$paymentRequired = WC()->cart->needs_payment();
if ( $paymentRequired){
    if ( empty($available_gateways) ) {
        $billingCountry = WC()->customer->get_billing_country();
        if ($billingCountry){
            $textNoPaymentMessage = esc_html__( 'Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' );
        } else {
            $textNoPaymentMessage = esc_html__( 'Please fill in your details above to see available payment methods.', 'woocommerce' );
        }
        $textNoPaymentMessage = apply_filters( 'woocommerce_no_available_payment_methods_message', $textNoPaymentMessage);
        $htmlPaymentMethods = "<li class='woocommerce-notice woocommerce-notice--info woocommerce-info'>{$textNoPaymentMessage}</li>";
    } else {
        ob_start();
        foreach ( $available_gateways as $gateway ) {
            wc_get_template( 'checkout/payment-method.php', ['gateway' => $gateway]);
        }
        $htmlPaymentMethods = ob_get_clean();
    }
    $htmlPaymentMethods = "{$actionReviewOrderBefore}
    <ul class='wc_payment_methods payment_methods methods'>{$htmlPaymentMethods}</ul>{$actionReviewOrderAfter}";
}
$textNoJS = esc_html__( 'Since your browser does not support JavaScript, or it is disabled, please ensure you click the %1$sUpdate Totals%2$s button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce' );
$textNoJS = sprintf($textNoJS, '<em>', '</em>' );
$textUpdateTotals = esc_attr__( 'Update totals', 'woocommerce' );
//Order: Button
$textOrderButton = __( 'Place order', 'woocommerce' );
$textOrderButton = apply_filters( 'woocommerce_order_button_text',  $textOrderButton);
$textOrderButton = esc_attr($textOrderButton);
$htmlOrderButton = "<button id='place_order' name='woocommerce_checkout_place_order' value='{$textOrderButton}' 
                    data-value='{$textOrderButton}' type='submit' class='button'>{$textOrderButton}</button>";
$htmlOrderButton = apply_filters('woocommerce_order_button_html', $htmlOrderButton);
//Order: Terms
ob_start();
wc_get_template( 'checkout/terms.php' );
$htmlTerms = ob_get_clean();
//Order: Content
$actionReviewOrderSubmitBefore = UtilsWp::doAction( 'woocommerce_review_order_before_submit' );
$actionReviewOrderSubmitAfter = UtilsWp::doAction( 'woocommerce_review_order_after_submit' );
$nonceProcessCheckout = wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' );
echo "<section id='payment' class='woocommerce-checkout-payment'>
{$htmlPaymentMethods}
<div class='form-row place-order'>
{$htmlTerms}
{$actionReviewOrderSubmitBefore}
<section class='text-xs-center'>
    <noscript>
    {$textNoJS}<br/>
    <button name='woocommerce_checkout_update_totals' value='{$textUpdateTotals}' type='submit' class='button'>{$textUpdateTotals}</button>
    </noscript>
    {$htmlOrderButton}
</section>
{$actionReviewOrderSubmitAfter}
{$nonceProcessCheckout}
</div>
</section>";