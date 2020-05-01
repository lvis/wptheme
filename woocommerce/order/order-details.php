<?php
/**
 * Order details
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details.php.
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;
use wp\UtilsWp;
$order = wc_get_order( $order_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited
if ( $order ) {
    //Table Items
    $contentOrderDetailsTableItems = '';
    $orderItemTypes = apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' );
    $orderItems = $order->get_items($orderItemTypes);
    $orderStatuses = ['completed', 'processing'];
    $orderStatusesNotes = apply_filters( 'woocommerce_purchase_note_order_statuses', $orderStatuses);
    $show_purchase_note = $order->has_status($orderStatusesNotes);
    foreach ($orderItems as $item_id => $item ) {
        /**
         * Order Item Details
         * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-item.php.
         * @see     https://docs.woocommerce.com/document/template-structure/
         * @package WooCommerce/Templates
         * @version 3.7.0
         * wc_get_template('order/order-details-item.php', [
            'order'              => $order,
            'item_id'            => $item_id,
            'item'               => $item,
            'show_purchase_note' => $show_purchase_note,
            'purchase_note'      => $product ? $product->get_purchase_note() : '',
            'product'            => $product,
            ]);
         */
        $showOrderItem = apply_filters( 'woocommerce_order_item_visible', true, $item );
        if ($showOrderItem) {
            $product = $item->get_product();
            $is_visible = $product && $product->is_visible();
            //Product: Link
            $linkToProduct = '';
            if ($is_visible){
                $linkToProduct = $product->get_permalink($item);
            }
            $linkToProduct = apply_filters( 'woocommerce_order_item_permalink', $linkToProduct, $item, $order );
            //Product: Name
            $orderItemName = $item->get_name();
            if ($linkToProduct){
                $orderItemName =  sprintf( '<a href="%s">%s</a>', $linkToProduct, $orderItemName);
            }
            $orderItemName = apply_filters( 'woocommerce_order_item_name', $orderItemName, $item, $is_visible );
            //Quantity
            $orderItemQty = esc_html($item->get_quantity());
            $orderItemQtyRefunded = $order->get_qty_refunded_for_item( $item_id );
            if ($orderItemQtyRefunded) {
                $orderItemQtyWithoutRefund = $item->get_quantity() - ($orderItemQtyRefunded * -1);
                $orderItemQtyWithoutRefund = esc_html($orderItemQtyWithoutRefund);
                $orderItemQty = "<del>{$orderItemQty}</del><ins>{$orderItemQtyWithoutRefund}</ins>";
            }
            $contentOrderItemQty ="<strong class='product-quantity'>&times;&nbsp;{$orderItemQty}</strong>";
            $contentOrderItemQty = apply_filters( 'woocommerce_order_item_quantity_html', $contentOrderItemQty , $item );
            //Meta
            $actionOrderItemMetaStart = UtilsWp::doAction('woocommerce_order_item_meta_start', $item_id, $item, $order, false );
            $contentOrderItemMeta = wc_display_item_meta($item, ['echo' => false]);
            $actionOrderItemMetaEnd = UtilsWp::doAction('woocommerce_order_item_meta_end', $item_id, $item, $order, false );
            //Subtotal
            $contentSubTotal = $order->get_formatted_line_subtotal( $item );
            //Purchase Note
            $contentPurchaseNote = '';
            if ( $show_purchase_note && $product ){
                $textPurchaseNote = $product->get_purchase_note();
                if (empty($textPurchaseNote) == false){
                    $textPurchaseNote = wp_kses_post($textPurchaseNote);
                    $textPurchaseNote = wpautop(do_shortcode($textPurchaseNote));
                    $contentPurchaseNote = "<tr class='woocommerce-table__product-purchase-note product-purchase-note'><td colspan='2'>{$textPurchaseNote}</td></tr>";
                }
            }
            //Style
            $cssOrderItem = apply_filters( 'woocommerce_order_item_class', 'woocommerce-table__line-item order_item', $item, $order );
            $cssOrderItem = esc_attr($cssOrderItem);
            $contentOrderDetailsTableItems .= "<tr class='{$cssOrderItem}'>
            <td class='woocommerce-table__product-name product-name'>
                {$orderItemName}
                {$contentOrderItemQty}
                {$actionOrderItemMetaStart}
                {$contentOrderItemMeta}
                {$actionOrderItemMetaEnd}
            </td>
            <td class='woocommerce-table__product-total product-total'>{$contentSubTotal}</td></tr>
            {$contentPurchaseNote}";
        }
    }
    //Items Total
    $contentOrderDetailsTableItemsTotal = '';
    foreach ( $order->get_order_item_totals() as $key => $total ) {
        $textTotalLabel = esc_html( $total['label'] );
        if ($key === 'payment_method'){
            $textTotalValue = esc_html( $total['value'] );
        } else {
            $textTotalValue = wp_kses_post( $total['value'] );
        }
        $contentOrderDetailsTableItemsTotal .= "<tr><th scope='row'>{$textTotalLabel}</th><td>{$textTotalValue}</td></tr>";
    }
    //Customer: Note
    $contentOrderDetailsTableCustomerNote = '';
    if ( $order->get_customer_note() ){
        $textNote = esc_html__( 'Note:', 'woocommerce' );
        $textNoteValue = wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) );
        $contentOrderDetailsTableCustomerNote = "<tr><th>{$textNote}</th><td>{$textNoteValue}</td></tr>";
    }
    //Customer: Details
    $contentCustomerDetails = '';
    if ( is_user_logged_in() && $order->get_user_id() === get_current_user_id() ) {
        ob_start();
        wc_get_template( 'order/order-details-customer.php', ['order' => $order]);
        $contentCustomerDetails = ob_get_clean();
    }
    //Downloads
    $contentDownloads = '';
    if ( $order->has_downloadable_item() && $order->is_download_permitted() ) {
        $downloads = $order->get_downloadable_items();
        ob_start();
        wc_get_template('order/order-downloads.php', ['downloads' => $downloads, 'show_title' => true]);
        $contentDownloads = ob_get_clean();
    }
    //Content
    $actionOrderDetailsTableBefore = UtilsWp::doAction('woocommerce_order_details_before_order_table', $order);
    $actionOrderDetailsTableAfter = UtilsWp::doAction('woocommerce_order_details_after_order_table', $order);
    $textOrderDetails = esc_html__( 'Order details', 'woocommerce' );
    $textProduct = esc_html__( 'Product', 'woocommerce' );
    $textTotal = esc_html__( 'Total', 'woocommerce' );
    $actionOrderDetailsTableItemsBefore = UtilsWp::doAction('woocommerce_order_details_before_order_table_items', $order);
    $actionOrderDetailsTableItemAfter = UtilsWp::doAction('woocommerce_order_details_after_order_table_items', $order);
    echo "{$contentDownloads}
    <section class='woocommerce-order-details'>
    <h2 class='woocommerce-order-details__title'>{$textOrderDetails}</h2>
    {$actionOrderDetailsTableBefore}
    <table class='woocommerce-table woocommerce-table--order-details shop_table order_details'>
		<thead>
			<tr>
				<th class='woocommerce-table__product-name product-name'>{$textProduct}</th>
				<th class='woocommerce-table__product-table product-total'>{$textTotal}</th>
			</tr>
		</thead>
		<tbody>
            {$actionOrderDetailsTableItemsBefore}
			{$contentOrderDetailsTableItems}
            {$actionOrderDetailsTableItemAfter}
		</tbody>
		<tfoot>
            {$contentOrderDetailsTableItemsTotal}
			{$contentOrderDetailsTableCustomerNote}
		</tfoot>
	</table>
	{$actionOrderDetailsTableAfter}
	</section>
	{$contentCustomerDetails}";
}