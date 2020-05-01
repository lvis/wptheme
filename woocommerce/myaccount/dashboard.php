<?php
/**
 * My Account Dashboard
 * Shows the first intro screen on the account dashboard.
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     2.6.0
 */
defined( 'ABSPATH' ) || exit;
/** My Account dashboard. */
do_action('woocommerce_account_dashboard');
/** Instead of Message Display Acount Details*/
//woocommerce_account_edit_account();
woocommerce_account_orders(1);