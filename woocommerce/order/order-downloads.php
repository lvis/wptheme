<?php
/**
 * Order Downloads.
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-downloads.php.
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.3.0
 */
defined( 'ABSPATH' ) || exit;
use wp\UtilsWp;
$contentTitle = '';
if ( isset( $show_title ) ){
    $textDownloads = esc_html__( 'Downloads', 'woocommerce' );
    $contentTitle = "<h2 class='woocommerce-order-downloads__title'>{$textDownloads}</h2>";
}
$contentAccountDownloads = '';
foreach ( wc_get_account_downloads_columns() as $column_id => $column_name ){
    $escColumnId = esc_attr( $column_id );
    $escColumnName = esc_html( $column_name );
    $contentAccountDownloads .= "<th class='{$escColumnId}'><span class='nobr'>{$escColumnName}</span></th>";
}
$contentDownloads = '';
foreach ( $downloads as $download ){
    $contentDownloadsItems = '';
    foreach ( wc_get_account_downloads_columns() as $column_id => $column_name ){
        $escColumnId = esc_attr( $column_id );
        $escColumnName = esc_attr( $column_name );
        $contentColumn = '';
        if ( has_action( 'woocommerce_account_downloads_column_' . $column_id ) ) {
            $contentColumn = UtilsWp::doAction( 'woocommerce_account_downloads_column_' . $column_id, $download );
        } else {
            switch ( $column_id ) {
                case 'download-product':
                    if ( $download['product_url'] ) {
                        $escDownloadProductName = esc_html( $download['product_name'] );
                        $escDownloadProductUrl = esc_url( $download['product_url'] );
                        $contentColumn = "<a href='{$escDownloadProductUrl}' class='button'>{$escDownloadProductName}</a>";
                    } else {
                        $contentColumn = esc_html( $download['product_name'] );
                    }
                    break;
                case 'download-file':
                    $escDownloadName = esc_html( $download['product_name'] );
                    $escDownloadUrl = esc_url( $download['download_url'] );
                    $contentColumn = "<a href='{$escDownloadUrl}' class='button'>{$escDownloadName}</a>";
                    break;
                case 'download-remaining':
                    if(is_numeric( $download['downloads_remaining'] )){
                        $contentColumn = esc_html($download['downloads_remaining']);
                    } else {
                        $contentColumn = esc_html__('&infin;','woocommerce');
                    }
                    break;
                case 'download-expires':
                    if ( empty( $download['access_expires'] ) == false) {
                        $downloadExpirationTime = strtotime($download['access_expires']);
                        $escDownloadExpirationTime =  esc_attr($downloadExpirationTime);
                        $escDownloadExpirationDate = date('Y-m-d', $downloadExpirationTime);
                        $escDownloadExpirationDate = esc_attr($escDownloadExpirationDate);
                        $escDownloadExpirationDateFormatted = esc_html(date_i18n( get_option( 'date_format' ), $downloadExpirationTime) );
                        $contentColumn = "<time datetime='{$escDownloadExpirationDate}' title='{$escDownloadExpirationTime}'>
                        {$escDownloadExpirationDateFormatted}</time>";
                    } else {
                        $contentColumn = esc_html__( 'Never', 'woocommerce' );
                    }
                    break;
            }
        }
        $contentDownloadsItems.="<td class='{$escColumnId}' data-title='{$escColumnName}'>{$contentColumn}</td>";
    }
    $contentDownloads .= "<tr>{$contentDownloadsItems}</tr>";
}
echo "<section class='woocommerce-order-downloads'>
{$contentTitle}
<table class='woocommerce-table woocommerce-table--order-downloads shop_table shop_table_responsive order_details'>
<thead><tr>{$contentAccountDownloads}</tr></thead>
{$contentDownloads}
</table>
</section>";