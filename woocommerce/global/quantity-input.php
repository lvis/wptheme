<?php
/**
 * Product quantity inputs
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/quantity-input.php.
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 4.0.0
 */

defined('ABSPATH') || exit;

use wp\UtilsWp;

$content = '';
$inputIdEsc = esc_attr($input_id);
$inputNameEsc = esc_attr($input_name);
if ($max_value && $min_value === $max_value) {
    $inputMinValueEsc = esc_attr($min_value);
    $content = "<div class='quantity hidden'>
    <input id='{$inputIdEsc}' name='{$inputNameEsc}' value='{$inputMinValueEsc}' class='qty' type='hidden'></div>";
} else {
    if (empty($step)) {
        $step = 1;
    }
    if (empty($min_value)) {
        $min_value = 1;
    }
    if (empty($max_value)) {
        $max_value = 10;
    }
    $contentQunatity = '';
    for ($count = $min_value; $count <= $max_value; $count = $count + $step) {
        $currentSelected = selected($input_value, $count, false);
        $contentQunatity .= "<option value='{$count}' {$currentSelected}>{$count}</option>";
    }
    $textQuantity = __('Quantity', 'woocommerce');
    $content = UtilsWp::doAction('woocommerce_before_quantity_input_field');
    $content .= "<div class='quantity'>
    <label class='screen-reader-text' for='{$inputIdEsc}'>{$textQuantity}</label>
    <select id='{$inputIdEsc}' name='{$inputNameEsc}' class='qty' >{$contentQunatity}</select></div>";
    $content .= UtilsWp::doAction('woocommerce_after_quantity_input_field');
}
echo $content;
