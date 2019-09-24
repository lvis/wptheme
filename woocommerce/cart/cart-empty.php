<?php
/**
 * Empty cart page
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-empty.php.
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */
defined('ABSPATH') || exit;
use wp\UtilsWp;
$actionCartBefore = UtilsWp::doAction('woocommerce_before_cart');
$textYourCartIsCurrentlyEmpty = __('Your cart is currently empty.', 'woocommerce' );
$textYourCartIsCurrentlyEmpty = wp_kses_post(apply_filters( 'wc_empty_cart_message', $textYourCartIsCurrentlyEmpty));
$contentCartEmpty = "{$actionCartBefore}<div class='text-xs-center'><h1 class='fas fa-shopping-cart fa-5x'></h1>
<p class='cart-empty'>{$textYourCartIsCurrentlyEmpty}</p></div>";
if (wc_get_page_id('shop') > 0) {
    $textReturnToShop = __('Return to shop', 'woocommerce');
    $linkReturnToShop = apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'));
    $linkReturnToShop = esc_url($linkReturnToShop);
    $contentCartEmpty .="<p class='return-to-shop text-xs-center'>
    <a class='button wc-backward' href='{$linkReturnToShop}'><i class='fas fa-store'></i> {$textReturnToShop}</a></p>";
}
echo $contentCartEmpty;