<?php
/**
 * My Account page
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-account.php.
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */
defined( 'ABSPATH' ) || exit;
/** My Account navigation. */
do_action('woocommerce_account_navigation');
/** My Account content. */
do_action('woocommerce_account_content');