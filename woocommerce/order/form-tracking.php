<?php
/**
 * Order tracking form
 * This template can be overridden by copying it to yourtheme/woocommerce/order/form-tracking.php.
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */
defined( 'ABSPATH' ) || exit;
global $post;
$urlPost = esc_url( get_permalink( $post->ID ) );
$textToTrackDescription = esc_html__( 'To track your order please enter your Order ID in the box below and press the "Track" button. This was given to you on your receipt and in the confirmation email you should have received.', 'woocommerce' );
//Order: Id
$idOrderId = 'orderid';
$textOrderId = esc_html__( 'Order ID', 'woocommerce' );
$textPlaceHolderOrderId = esc_attr__( 'Found in your order confirmation email.', 'woocommerce' );
$valueOrderId = isset( $_REQUEST['orderid'] ) ? esc_attr( wp_unslash( $_REQUEST['orderid'] ) ) : '';
//Order: Email
$idOrderEmail = 'order_email';
$textBillingEmail = esc_html__( 'Billing email', 'woocommerce' );
$textPlaceHolderOrderEmail = esc_attr__( 'Email you used during checkout.', 'woocommerce' );
$valueOrderEmail =  '';
if (isset( $_REQUEST['order_email'] )){
    $valueOrderEmail = $_REQUEST['order_email'];
    $valueOrderEmail = esc_attr(wp_unslash($valueOrderEmail));
}
//Button
$textTrack = esc_attr__('Track', 'woocommerce');
$nonceOrderTracking =  wp_nonce_field( 'woocommerce-order_tracking', 'woocommerce-order-tracking-nonce', true, false);
echo "<form action='{$urlPost}' method='post' class='woocommerce-form woocommerce-form-track-order track_order'>
	<p>{$textToTrackDescription}</p>
	<p class='form-row form-row-first'>
        <label for='{$idOrderId}'>{$textOrderId}</label>
        <input type='text' id='{$idOrderId}'  name='{$idOrderId}' value='{$valueOrderId}' placeholder='{$textPlaceHolderOrderId}' class='input-text'>
    </p>
	<p class='form-row form-row-last'>
        <label for='{$idOrderEmail}'>{$textBillingEmail}</label>
        <input type='text' id='{$idOrderEmail}' name='{$idOrderEmail}' value='{$valueOrderEmail}' placeholder='{$textPlaceHolderOrderEmail}' class='input-text'>
    </p>
	<div class='clear'></div>
	<p class='form-row'>
        <button type='submit' class='button' name='track' value='{$textTrack}'>{$textTrack}</button>
    </p>
	{$nonceOrderTracking}
</form>";