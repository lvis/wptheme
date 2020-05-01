<?php
/**
 * Lost password form
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-lost-password.php.
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.2
 */

defined('ABSPATH') || exit;

use wp\UtilsWp;

$actionFormLostPasswordBefore = UtilsWp::doAction('woocommerce_before_lost_password_form');
$actionFormLostPasswordAfter = UtilsWp::doAction('woocommerce_after_lost_password_form');
$actionFormLostPassword = UtilsWp::doAction('woocommerce_lostpassword_form');
$nonceFormLostPassword = wp_nonce_field('lost_password', 'woocommerce-lost-password-nonce', true, false);
$textLostYourPass = __('Lost your password?', 'woocommerce');
$textLostYourPassDesc = esc_html__('Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'woocommerce');
$textLostYourPassDesc = str_replace($textLostYourPass, '',$textLostYourPassDesc);
$textLostYourPass = apply_filters('woocommerce_lost_password_message', $textLostYourPass);
$textUserEmail = esc_html__('Username or email', 'woocommerce');
$textResetPassword = esc_html__('Reset password', 'woocommerce');
$valueResetPassword = esc_attr__('Reset password', 'woocommerce');
echo "<div class='container text-xs-center'>{$actionFormLostPasswordBefore}
<form method='post' class='woocommerce-ResetPassword lost_reset_password col-sm-12 col-md-8 col-lg-6'><fieldset>
<legend><i class='fas fa-user-edit'></i> {$textLostYourPass}</legend>
<p>{$textLostYourPassDesc}</p>
<p class='woocommerce-form-row woocommerce-form-row--first form-row form-row-first'>
    <label for='user_login'><i class='fas fa-envelope'></i> {$textUserEmail}</label>
    <input type='text' id='user_login' name='user_login' autocomplete='username'>
</p>
{$actionFormLostPassword}
<p class='text-xs-center'>
    <input type='hidden' name='wc_reset_password' value='true'>
    <button type='submit' value='{$valueResetPassword}' class='button'>
        <i class='fas fa-unlock'></i> {$textResetPassword}
    </button>
</p>
{$nonceFormLostPassword}
</fieldset></form>{$actionFormLostPasswordAfter}</div>";