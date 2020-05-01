<?php
/**
 * Lost password reset form.
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-reset-password.php.
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.5
 */
defined('ABSPATH') || exit;

use wp\UtilsWp;

$actionFormResetBefore = UtilsWp::doAction('woocommerce_before_reset_password_form');
$actionFormResetAfter = UtilsWp::doAction('woocommerce_after_reset_password_form');
$actionFormResetPassword = UtilsWp::doAction('woocommerce_resetpassword_form');
$nonceFormReset = wp_nonce_field('reset_password', 'woocommerce-reset-password-nonce', true, false);
$textEnterNewPasswordBelow = esc_html__('Enter a new password below.', 'woocommerce');
$textEnterNewPasswordBelow = apply_filters('woocommerce_reset_password_message', $textEnterNewPasswordBelow);
$textPasswordNew = esc_html__('New password', 'woocommerce');
$textPasswordReEnter = esc_html__('Re-enter new password', 'woocommerce');
$valueResetKey = esc_attr($args['key']);
$valueResetLogin = esc_attr($args['login']);
$valueSave = esc_attr__('Save', 'woocommerce');
$textSave = esc_html__('Save', 'woocommerce');
echo "{$actionFormResetBefore}<form method='post' class='woocommerce-ResetPassword lost_reset_password'>
<fieldset>
<p>{$textEnterNewPasswordBelow}</p>
<p>
    <label for='password_1'>{$textPasswordNew}<span class='required'>*</span></label>
    <input type='password' id='password_1' name='password_1' autocomplete='new-password'>
</p>
<p>
    <label for='password_2'>{$textPasswordReEnter}<span class='required'>*</span></label>
    <input type='password' id='password_2' name='password_2' autocomplete='new-password'>
</p>
<input type='hidden' name='reset_key' value='{$valueResetKey}'>
<input type='hidden' name='reset_login' value='{$valueResetLogin}'>
{$actionFormResetPassword}
<p>
    <input type='hidden' name='wc_reset_password' value='true'>
    <button type='submit' value='{$textSave}' class='button float-xs-right'>
        <i class='fas fa-save'></i> 
        <span>{$textSave}</span>
    </button>
</p>
{$nonceFormReset}
</fieldset></form>{$actionFormResetAfter}";