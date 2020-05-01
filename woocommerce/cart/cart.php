<?php
/**
 * Cart Page
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.7.0
 */
defined('ABSPATH') || exit;

use wp\UtilsWp;
use wp\UtilsWooCommerce;

$wc = WooCommerce::instance();
$actionCartBefore = '';
if (is_checkout() == false){
    /**
     * Trigger Action: Before Cart
     * @hooked woocommerce_output_all_notices - 10
     */
    $actionCartBefore = UtilsWp::doAction('woocommerce_before_cart');
}
$cartFormActionUrl = esc_url(wc_get_cart_url());
$contentCartItems .= UtilsWooCommerce::i()->getContentCartItems($wc);
$actionCartContentsBefore = UtilsWp::doAction('woocommerce_before_cart_contents');
$actionCartContents = UtilsWp::doAction('woocommerce_cart_contents');
$actionCartActions = UtilsWp::doAction('woocommerce_cart_actions');
$cartNonce = wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce', true, false);
$actionCartContentAfter = UtilsWp::doAction('woocommerce_after_cart_contents');
$actionCartTableBefore = UtilsWp::doAction('woocommerce_before_cart_table');
$actionCartTableAfter = UtilsWp::doAction('woocommerce_after_cart_table');
$actionCartCollateralsBefore = UtilsWp::doAction('woocommerce_before_cart_collaterals');
/**
 * Trigger Action: Cart Totals and Cross Sells
 * @hooked woocommerce_cross_sell_display
 * @hooked woocommerce_cart_totals - 10
 */
$actionCartCollaterals = UtilsWp::doAction('woocommerce_cart_collaterals');
$actionCartAfter = UtilsWp::doAction('woocommerce_after_cart');
$textYourCart = __('Your Cart', 'woocommerce');
$textProduct = __('Product', 'woocommerce');
$textQty = __('Qty', 'woocommerce');
$textTotal = __('Total', 'woocommerce');
$textUpdateCart = __('Update cart', 'woocommerce');
echo "{$actionCartBefore}<form action='{$cartFormActionUrl}' method='post' class='woocommerce-cart-form'>
<fieldset>
<legend><i class='fa fa-shopping-cart'></i> {$textYourCart}</legend>
{$actionCartTableBefore}
<div class='shop_table cart woocommerce-cart-form__contents'>
    <section>
        <div class='col-xs-6'><i class='fa fa-wine-bottle'></i> {$textProduct}</div>
        <div class='col-xs-3'><i class='fa fa-box'></i> {$textQty}</div>
        <div class='col-xs-3 text-xs-center'><i class='fa fa-file-invoice'></i> {$textTotal}</div>
    </section>
    <hr>
    {$actionCartContentsBefore}
    {$contentCartItems}
    {$actionCartContents}
    {$actionCartActions}
    {$cartNonce}
    <input type='submit' name='update_cart' value='{$textUpdateCart}' class='d-xs-none' disabled=''>
    {$actionCartContentAfter}
</div>
{$actionCartTableAfter}
<hr>
{$actionCartCollateralsBefore}
{$actionCartCollaterals}
{$actionCartAfter}
</fieldset></form>";