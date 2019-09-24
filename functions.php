<?php /** Author: Vitali Lupu vitaliix@gmail.com*/
require_once(ABSPATH . 'wp-admin/includes/screen.php');
if (function_exists('handleMetaBoxPath') == false) {
    function handleMetaBoxPath($url) {
        $path = '/vendor/wpmetabox/meta-box';
        if (strpos($url, $path)) {
            $templateDirUri = get_template_directory_uri();
            $url =  $templateDirUri . $path.'/';
            remove_filter('plugins_url', 'handleMetaBoxPath');
        }
        return $url;
    }

    add_filter('plugins_url', 'handleMetaBoxPath');
}
require_once(__DIR__ . '/vendor/autoload.php');
if (defined('PROJECT')) {
    switch (PROJECT) {
    case 'realestate':
        wp\RealEstate::i();
        break;
    case 'etnikwines':
        wp\EtnikWines::i();
        break;
    case 'luckyagency':
        wp\PartyMaker::i();
        break;
    case 'mayfair':
        wp\MayFair::i();
        break;
    case 'demolition':
        wp\Demolition::i();
        break;
    default:
        wp\WpApp::i();
        break;
    }
}
else {
    wp\WpApp::i();
}
/**
 * Output the Order review table for the checkout.
 *
 * @param bool $deprecated Deprecated param.
 */
function woocommerce_order_review($deprecated = false) {
    $contentCart = do_shortcode('[woocommerce_cart]');
    $actionReviewOrderCartContentsBefore = wp\UtilsWp::doAction('woocommerce_review_order_before_cart_contents');
    $actionReviewOrderCartContentsAfter = wp\UtilsWp::doAction('woocommerce_review_order_after_cart_contents');
    echo "{$actionReviewOrderCartContentsBefore}{$contentCart}{$actionReviewOrderCartContentsAfter}";
}

function woocommerce_cart_totals() {
    wc_get_template('cart/cart-totals.php');
}