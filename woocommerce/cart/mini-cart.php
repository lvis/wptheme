<?php
/**
 * Mini-cart
 * Contains the markup for the mini-cart, used by the cart widget.
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/mini-cart.php.
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */
defined('ABSPATH') || exit;

use wp\WPUtils;
use wp\UtilsWooCommerce;

$contentMiniCart = '';
if (wc()->cart->is_empty()) {
    $textNoProductsInTheCart = __('No products in the cart.', 'woocommerce');
    $contentMiniCart = "<p class='woocommerce-mini-cart__empty-message'>{$textNoProductsInTheCart}</p>";
} else {
    $actionMiniCartContentsBefore = WPUtils::doAction('woocommerce_before_mini_cart_contents');
    $actionMiniCartContents = WPUtils::doAction('woocommerce_mini_cart_contents');
    $actionShoppingCartButtonsBefore = WPUtils::doAction('woocommerce_widget_shopping_cart_before_buttons');
    //$actionShoppingCartButtons = WPUtils::doAction('woocommerce_widget_shopping_cart_buttons');
    $textCheckout = __('Checkout', 'woocommerce');
    $urlCheckout = wc_get_checkout_url();
    $contentMiniCart = UtilsWooCommerce::getCartContents();
    $cssMiniCart = esc_attr($args['list_class']);
    $contentMiniCart = "<div class='woocommerce-mini-cart cart_list product_list_widget {$cssMiniCart}'>
        {$actionMiniCartContentsBefore}
        {$contentMiniCart}
        {$actionMiniCartContents}
    </div>
    {$actionShoppingCartButtonsBefore}
    <div class='text-xs-center'><a href='{$urlCheckout}' class='button'>{$textCheckout}</a></div>";
}
$actionMiniCartAfter = WPUtils::doAction('woocommerce_after_mini_cart');
echo "{$contentMiniCart}{$actionMiniCartAfter}";