<?php
/**
 * Cart Page
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */
defined('ABSPATH') || exit;

use wp\WPUtils;
use wp\UtilsWooCommerce;

$contentCartItems = '';
$contentCartItems .= UtilsWooCommerce::getCartContents();
$actionCartActions = WPUtils::doAction('woocommerce_cart_actions');
$actionCartContentsBefore = WPUtils::doAction('woocommerce_before_cart_contents');
$actionCartContents = WPUtils::doAction('woocommerce_cart_contents');
$actionCartContentAfter = WPUtils::doAction('woocommerce_after_cart_contents');
$actionCartTableBefore = WPUtils::doAction('woocommerce_before_cart_table');
$actionCartTableAfter = WPUtils::doAction('woocommerce_after_cart_table');
/**
 * Trigger Action: Cart Totals and Cross Sells
 * @hooked woocommerce_cross_sell_display
 * @hooked woocommerce_cart_totals - 10
 */
$actionCartCollaterals = WPUtils::doAction('woocommerce_cart_collaterals');
/**
 * Trigger Action: Before Cart
 * @hooked woocommerce_output_all_notices - 10
 */
$actionCartBefore = WPUtils::doAction('woocommerce_before_cart');
$actionCartAfter = WPUtils::doAction('woocommerce_after_cart');
$textProduct = __('Product', 'woocommerce');
$textPrice = __('Price', 'woocommerce');
$textQuantity = __('Quantity', 'woocommerce');
$textQty = __('Qty', 'woocommerce');
$textTotal = __('Total', 'woocommerce');
$textActions = __('Actions', 'woocommerce');
$textUpdateCart = __('Update cart', 'woocommerce');
$cartFormActionUrl = esc_url(wc_get_cart_url());
$cartNonce = wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce', true, false);
if (wc_coupons_enabled()) {
    $actionCartCoupon = WPUtils::doAction('woocommerce_cart_coupon');
    $textCoupon = __('Coupon:', 'woocommerce');
    $textCouponCode = __('Coupon code', 'woocommerce');
    $textApplyCoupon = __('Apply coupon', 'woocommerce');
    $contentCartCoupon = "<div class='coupon'><label for='coupon_code'>{$textCoupon}</label> 
    <input type='text' name='coupon_code' class='input-text' id='coupon_code' value='' placeholder='{$textCouponCode}'> 
    <button type='submit' class='button' name='apply_coupon' value='{$textApplyCoupon}'>{$textApplyCoupon}</button>
    {$actionCartCoupon}</div>";
}
echo "{$actionCartBefore}
<form action='{$cartFormActionUrl}' method='post' class='row woocommerce-cart-form'>
<div class='col-xs-12 col-lg-6'>
{$actionCartTableBefore}
<section class='card shop_table cart woocommerce-cart-form__contents' data-bind='css:cssForm'><div class='card-content'>
    <div class='row title'>
        <div class='col-xs-6'>
            <i class='fal fa-wine-bottle'></i>
            <span class='d-md-inline-block'>{$textProduct}</span>
        </div>
        <div class='col-xs-3 text-xs-center text-truncate'>
            <i class='fal fa-box-full'></i>
            <span class='d-xs-none d-md-inline-block'>{$textQty}</span>
        </div>
        <div class='col-xs-3 text-xs-center'>
            <i class='fal fa-file-invoice-dollar'></i>
            <span class='d-xs-none d-md-inline-block'>{$textTotal}</span>
        </div>
    </div>
    <hr>
    {$actionCartContentsBefore}
    {$contentCartItems}
    {$actionCartContents}
    {$actionCartActions}
    {$cartNonce}
    <input type='hidden' name='update_cart' value='{$textUpdateCart}'>
    <input type='submit' name='update_cart' value='{$textUpdateCart}' style='display:none !important' disabled=''>
    {$actionCartContentAfter}
</div></section>
{$actionCartTableAfter}
</div>
{$actionCartCollaterals}
</form>
{$actionCartAfter}";