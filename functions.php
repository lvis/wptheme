<?php /** Author: Vitali Lupu <vitaliix@gmail.com> */
define('RWMB_URL', get_template_directory_uri() . '/vendor/wpmetabox/meta-box/');
require_once(__DIR__ . '/vendor/autoload.php');
if (defined('PROJECT'))
{
    switch (PROJECT){
        case 'etnikwines': wp\EtnikWines::i(); break;
        case 'luckyagency': wp\PartyMaker::i(); break;
        case 'mayfairclub': wp\MayFairClub::i(); break;
        case 'demolition': wp\Demolition::i(); break;
        default: wp\WpApp::i(); break;
    }
} else {
    wp\WpApp::i();
}

/**
 * Output the Order review table for the checkout.
 * @param bool $deprecated Deprecated param.
 */
function woocommerce_order_review($deprecated = false)
{
    $contentCart = do_shortcode('[woocommerce_cart]');
    $actionReviewOrderCartContentsBefore = wp\WPUtils::doAction('woocommerce_review_order_before_cart_contents');
    $actionReviewOrderCartContentsAfter = wp\WPUtils::doAction('woocommerce_review_order_after_cart_contents');
    echo "{$actionReviewOrderCartContentsBefore}{$contentCart}{$actionReviewOrderCartContentsAfter}";
}
function woocommerce_cart_totals()
{
    wc_get_template('cart/cart-totals.php');
}