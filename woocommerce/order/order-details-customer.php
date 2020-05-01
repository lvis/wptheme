<?php
/**
 * Order Customer Details
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-customer.php.
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.4
 */
defined( 'ABSPATH' ) || exit;
use wp\UtilsWp;

$textNA = esc_html__( 'N/A', 'woocommerce' );
$contentShippingBefore = '';
$contentShippingAfter = '';
if (wc_ship_to_billing_address_only() == false && $order->needs_shipping_address())
{
    $contentShippingBefore = "<section class='woocommerce-columns woocommerce-columns--2 woocommerce-columns--addresses col2-set addresses'>
		<div class='woocommerce-column woocommerce-column--1 woocommerce-column--billing-address col-1'>";
    //Title Shipping
    $textShippingAddress = esc_html__( 'Shipping address', 'woocommerce' );
    $textShippingAddressFormatted = $order->get_formatted_shipping_address($textNA);
    $textShippingAddressFormatted = wp_kses_post($textShippingAddressFormatted);
    $contentShippingAfter = "</div><div class='woocommerce-column woocommerce-column--2 woocommerce-column--shipping-address col-2'>
    <h2 class='woocommerce-column__title'>{$textShippingAddress}</h2>
    <address>{$textShippingAddressFormatted}</address></div></section>";
}
//Title Billing
$textBillingAddress = esc_html__( 'Billing address', 'woocommerce' );
$textBillingAddressFormatted = $order->get_formatted_billing_address($textNA);
$textBillingAddressFormatted = wp_kses_post($textBillingAddressFormatted);
//Billing Phone
$contentBillingPhone = '';
if ( $order->get_billing_phone() ){
    $textBillingPhone = esc_html( $order->get_billing_phone() );
    $contentBillingPhone = "<p class='woocommerce-customer-details--phone'>{$textBillingPhone}</p>";
}
//Billing Email
$contentBillingEmail = '';
if ( $order->get_billing_email() ){
    $textBillingEmail = esc_html( $order->get_billing_email() );
    $contentBillingEmail = "<p class='woocommerce-customer-details--email'>{$textBillingEmail}</p>";
}

$actionOrderDetailsCustomerAfter = UtilsWp::doAction( 'woocommerce_order_details_after_customer_details', $order );
echo "<section class='woocommerce-customer-details'>
{$contentShippingBefore}
<h2 class='woocommerce-column__title'>{$textBillingAddress}</h2>
<address>
{$textBillingAddressFormatted}
{$contentBillingPhone}
{$contentBillingEmail}
</address>
{$contentShippingAfter}
{$actionOrderDetailsCustomerAfter}
</section>";