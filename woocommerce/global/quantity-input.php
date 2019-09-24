<?php
/**
 * Product quantity inputs
 * This template can be overridden by copying it to yourtheme/woocommerce/global/quantity-input.php.
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */
defined('ABSPATH') || exit;

$content = '';
$inputIdEsc = esc_attr($input_id);
$inputNameEsc = esc_attr($input_name);
$textQuantity = __('Quantity', 'woocommerce');
if ($max_value && $min_value === $max_value) {
    $inputMinValueEsc = esc_attr($min_value);
    $content = "<div class='quantity hidden'>
    <input id='{$inputIdEsc}' name='{$inputNameEsc}' value='{$inputMinValueEsc}' class='qty' type='hidden'></div>";
} else {
    if (empty($min_value)) {
        $min_value = 1;
    }
    if (empty($max_value)) {
        $max_value = 100;
    }
    if (empty($step)) {
        $step = 1;
    }
    for ($count = $min_value; $count <= $max_value; $count = $count + $step) {
        $currentSelected = selected( $input_value, $count, false);
        $content .= "<option value='{$count}' {$currentSelected}>{$count}</option>";
    }
    $content = "<div class='quantity align-bottom'>
    <select name='{$inputNameEsc}' class='qty' data-bind='event:{change:handleChangeCartProductQuantity}'>{$content}</select></div>";
}
echo $content;