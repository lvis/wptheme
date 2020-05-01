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
$wc = WooCommerce::instance();
//------------------------------------------------ Subtotal
$contentCartSubtotal =  UtilsWooCommerce::i()->getContentCartSubtotal($wc);
//------------------------------------------------ Shipping
$contentCartShipping = UtilsWooCommerce::i()->getContentCartShipping($wc);
//------------------------------------------------ Coupons
$contentCartCoupons = UtilsWooCommerce::i()->getContentCartCoupons($wc);
//------------------------------------------------ Fees
$contentCartFees = UtilsWooCommerce::i()->getContentCartFees($wc);
//------------------------------------------------ Taxes
$contentCartTax = UtilsWooCommerce::i()->getContentCartTaxes($wc);
//------------------------------------------------ Totals
$contentCartTotal = UtilsWooCommerce::i()->getContentCartTotal($wc);
//------------------------------------------------ Checkout Button
$contentCheckoutButton = '';
$actionCartTotalsBefore = '';
$actionCartTotalsAfter = '';
if (is_checkout()) {
    $textTotal = __('Order Total', 'woocommerce');
    $actionOrderTotalBefore = UtilsWp::doAction('woocommerce_review_order_before_order_total');
    $actionOrderTotalAfter = UtilsWp::doAction('woocommerce_review_order_after_order_total');
} else if (is_cart()) {
    $textTotal = __('Cart totals', 'woocommerce');
    $actionOrderTotalBefore = UtilsWp::doAction('woocommerce_cart_totals_before_order_total');
    $actionOrderTotalAfter = UtilsWp::doAction('woocommerce_cart_totals_after_order_total');
    $actionCartTotalsBefore = UtilsWp::doAction('woocommerce_before_cart_totals');
    $actionCartTotalsAfter = UtilsWp::doAction('woocommerce_after_cart_totals');
    $textProceedToCheckout = __('Proceed to checkout', 'woocommerce');
    $urlCheckout = esc_url(wc_get_checkout_url());
    $contentCheckoutButton = "<p class='col-xs-12 text-xs-center'><a href='{$urlCheckout}' class='button'>{$textProceedToCheckout}</a></p>";
}
echo "{$actionCartTotalsBefore}<div class='cart_totals'>
{$contentCartSubtotal}
{$contentCartShipping}
{$contentCartCoupons}
{$contentCartFees}
{$contentCartTax}
{$actionOrderTotalBefore}
<section class='order-total'>
    <div class='col-xs-9'><i class='fa fa-credit-card'></i> {$textTotal}</div>
    <div class='col-xs-3 text-xs-center font-weight-bold'>{$contentCartTotal}</div>
</section>
{$contentCheckoutButton}
{$actionOrderTotalAfter}
</div>{$actionCartTotalsAfter}";