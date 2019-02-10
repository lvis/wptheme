<?php /** Author: Vitali Lupu <vitaliix@gmail.com> */
require_once(__DIR__ . '/vendor/autoload.php');
use wp\WpApp;
use wp\WPUtils;
WpApp::i();
function woocommerce_cart_totals()
{
    wc_get_template('cart/cart-totals.php');
}
/**
 * Output the Order review table for the checkout.
 * @param bool $deprecated Deprecated param.
 */
function woocommerce_order_review($deprecated = false)
{
    $contentCart = do_shortcode('[woocommerce_cart]');
    $actionReviewOrderCartContentsBefore = WPUtils::doAction('woocommerce_review_order_before_cart_contents');
    $actionReviewOrderCartContentsAfter = WPUtils::doAction('woocommerce_review_order_after_cart_contents');
    echo "{$actionReviewOrderCartContentsBefore}{$contentCart}{$actionReviewOrderCartContentsAfter}";
}