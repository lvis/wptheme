<?php
/**
 * Order again button
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-again.php.
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;
$urlOrderAgain = esc_url($order_again_url);
$textOrderAgain = esc_html__( 'Order again', 'woocommerce' );
echo "<p class='order-again'><a href='{$urlOrderAgain}' class='button'>{$textOrderAgain}</a></p>";