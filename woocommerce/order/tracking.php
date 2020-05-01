<?php
/**
 * Order tracking
 * This template can be overridden by copying it to yourtheme/woocommerce/order/tracking.php.
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 2.2.0
 */
defined( 'ABSPATH' ) || exit;
use wp\UtilsWp;

$notes = $order->get_customer_order_notes();
//Order: Number
$orderNumber = $order->get_order_number();
$orderNumber = "<mark class='order-number'>{$orderNumber}</mark>";
//Order: Date
$orderDate = $order->get_date_created();
$orderDate = wc_format_datetime($orderDate);
$orderDate = "<mark class='order-date'>{$orderDate}</mark>";
//Order: Status
$orderStatus = $order->get_status();
$orderStatus = wc_get_order_status_name($orderStatus);
$orderStatus = "<mark class='order-status'>{$orderStatus}</mark>";
//Order: Info
$textOrderInfo = __( 'Order #%1$s was placed on %2$s and is currently %3$s.', 'woocommerce' );
$contentOrderInfo = apply_filters('woocommerce_order_tracking_status', sprintf($textOrderInfo, $orderNumber, $orderDate, $orderStatus));
$contentOrderInfo = wp_kses_post($contentOrderInfo);
//Order: Note
$contentOrderNotes = '';
if ( $notes ){
    $textOrderUpdates = esc_html__( 'Order updates', 'woocommerce' );
    $contentOrderNotesItems = '';
    foreach ( $notes as $note ){
        $noteDate = esc_html__( 'l jS \o\f F Y, h:ia', 'woocommerce' );
        $noteDate = date_i18n($noteDate, strtotime( $note->comment_date ) );
        $noteDescription = wpautop( wptexturize( $note->comment_content ) );
        $contentOrderNotesItems .= "<li class='comment note'><div class='comment_container'>
        <div class='comment-text'>
            <p class='meta'>{$noteDate}</p>
            <div class='description'>{$noteDescription}</div>
            <div class='clear'></div>
        </div>
        <div class='clear'></div>
        </div></li>";
    }
    $contentOrderNotes = "<h2>{$textOrderUpdates}</h2><ol class='commentlist notes'>{$contentOrderNotesItems}</ol>";
}
//Order: View Button
$actionViewOrder = UtilsWp::doAction('woocommerce_view_order', $order->get_id() );
echo "<p class='order-info'>{$contentOrderInfo}</p>
{$contentOrderNotes}
{$actionViewOrder}";








