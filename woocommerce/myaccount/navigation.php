<?php
/**
 * My Account navigation
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/navigation.php.
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 2.6.0
 */
defined( 'ABSPATH' ) || exit;
do_action('woocommerce_before_account_navigation');
global $wp;
$content = '';
$items = wc_get_account_menu_items();
unset($items['dashboard']);
foreach ($items as $endpoint => $label) {
    switch ($endpoint) {
        case 'edit-account':
            $menuItemIcon = 'user-cog';
            break;
        case 'downloads':
            $menuItemIcon = 'cloud-download-alt';
            break;
        case 'orders':
            $menuItemIcon = 'cart-arrow-down';
            break;
        case 'edit-address':
            $menuItemIcon = 'map-signs';
            break;
        default:
            $menuItemIcon = 'sign-out-alt';
    }
    $anchorLink = esc_url(wc_get_account_endpoint_url($endpoint));
    $anchorLabel = esc_html($label);
    $contentActiveItem = '';

    $current = isset( $wp->query_vars[ $endpoint ] );
    if ( $endpoint === 'orders' && ( isset( $wp->query_vars['page'] ) || empty( $wp->query_vars ) ) ) {
        $current = true; // Dashboard is not an endpoint, so needs a custom check.
    }
    if ($current) {
        $contentActiveItem = " class='active'";
    }
    $content .= "<li{$contentActiveItem}><a href='{$anchorLink}'><i class='fas fa-{$menuItemIcon}'></i> {$anchorLabel}</a></li>";
}
echo "<div class='navigation'><ul>$content</ul></div>";
do_action('woocommerce_after_account_navigation');