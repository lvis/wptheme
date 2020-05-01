<?php
/**
 * Checkout shipping information form
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-shipping.php.
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.6.0
 */
defined('ABSPATH') || exit;

use wp\UtilsWp;
use wp\UtilsWooCommerce;

/** @global WC_Checkout $checkout */
$contentFormShipping = '';
/** ---------------------------------------- Second Shipping Address*/
if (wc()->cart->needs_shipping_address() === true) {
    $fieldsShipping = $checkout->get_checkout_fields('shipping');
    foreach ($fieldsShipping as $key => $field) {
        if (isset($field['country_field'], $fieldsShipping[$field['country_field']])) {
            $field['country'] = $checkout->get_value($field['country_field']);
        }
        //woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
        $contentFormShipping .= UtilsWooCommerce::getFormField($key, $field, $checkout->get_value($key));
    }
    $textShippingAddress = __('Shipping address', 'woocommerce');
    $textShipToDiffAddress = __('Ship to a different address?', 'woocommerce');
    $checkedShipToDest = get_option('woocommerce_ship_to_destination');
    if ($checkedShipToDest === 'shipping'){
        $checkedShipToDest = 1;
    } else {
        $checkedShipToDest = 0;
    }
    $actionCheckoutShippingFormBefore = UtilsWp::doAction('woocommerce_before_checkout_shipping_form', $checkout);
    $actionCheckoutShippingFormAfter = UtilsWp::doAction('woocommerce_after_checkout_shipping_form', $checkout);
    $checkedShipToDest = apply_filters('woocommerce_ship_to_different_address_checked', $checkedShipToDest);
    $checkedShipToDiffAddress = checked($checkedShipToDest, 1, false);
    $contentFormShipping = "<label id='ship-to-different-address' for='shipToDifferentAdd' class='d-xs-block'>
    <span class='col-xs-5'><i class='fa fa-truck'></i> {$textShipToDiffAddress}</span>
    <input id='shipToDifferentAdd' name='ship_to_different_address' type='checkbox' {$checkedShipToDiffAddress} value='1' class='switch'>
    </label>
    <div class='shipping_address'>{$actionCheckoutShippingFormBefore}
    <div class='woocommerce-shipping-fields__field-wrapper'>{$contentFormShipping}</div>
    {$actionCheckoutShippingFormAfter}</div>";
}
/** ---------------------------------------- Order Notes*/
$contentNotes = '';
$enableOrderComments = get_option('woocommerce_enable_order_comments', 'yes');
$enableOrderComments = ($enableOrderComments === 'yes');
$enableOrderComments = apply_filters('woocommerce_enable_order_notes_field', $enableOrderComments);
if ($enableOrderComments) {
    $fieldsOrder = $checkout->get_checkout_fields('order');
    foreach ($fieldsOrder as $key => $field) {
        $contentNotes .= UtilsWooCommerce::getFormField($key, $field, $checkout->get_value($key));
    }
    $contentNotes = "<div class='woocommerce-additional-fields__field-wrapper'>{$contentNotes}</div>";
}
$actionOrderNotesBefore = UtilsWp::doAction('woocommerce_before_order_notes', $checkout);
$actionOrderNotesAfter = UtilsWp::doAction('woocommerce_after_order_notes', $checkout);
echo "<div class='woocommerce-shipping-fields'>
{$contentFormShipping}
</div>
<div class='woocommerce-additional-fields'>
{$actionOrderNotesBefore}
{$contentNotes}
{$actionOrderNotesAfter}
</div>";