<?php
/**
 * Login Form
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */
defined('ABSPATH') || exit;

use wp\WPUtils;

$content = '';
$textEmailAddress = __('Email address', 'woocommerce');
$textUserName = __('Username', 'woocommerce');
$textPassword = __('Password', 'woocommerce');
$contentFormRegister = '';
/**--------------------------------------[Register]*/
if (get_option('woocommerce_enable_myaccount_registration') === 'yes') {
    $contentUserName = '';
    if (get_option('woocommerce_registration_generate_username') === 'no') {
        $valueUserName = '';
        if (!empty($_POST['username'])) {
            $valueUserName = esc_attr(wp_unslash($_POST['username']));
        }
        $contentUserName = "<fieldset>
            <label for='reg_username' class='required'><i class='far fa-user'></i> {$textUserName}</label>
            <input id='reg_username' name='username' type='text' autocomplete='username' value='{$valueUserName}'>
        </fieldset>";
    }
    $contentPassword = '';
    if ('no' === get_option('woocommerce_registration_generate_password')) {
        $contentPassword = "<fieldset>
            <label for='reg_password' class='required'><i class='far fa-key'></i> {$textPassword}</label>
            <input id='reg_password' name='password' type='password' autocomplete='new-password'>
        </fieldset>";
    }
    $valueEmail = '';
    if (!empty($_POST['email'])) {
        $valueEmail = esc_attr(wp_unslash($_POST['email']));
    }
    $textRegister = __('Register', 'woocommerce');
    $nonceRegister = wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce', true,
        false);
    $actionRegisterFormTag = WPUtils::doAction( 'woocommerce_register_form_tag' );
    $actionRegisterFormStart = WPUtils::doAction('woocommerce_register_form_start');
    $actionRegisterForm = WPUtils::doAction('woocommerce_register_form');
    $actionRegisterFormEnd = WPUtils::doAction('woocommerce_register_form_end');
    $contentFormRegister = "<div class='col-xs-12 col-md-6'>
    <form method='post' class='woocommerce-form-register register' {$actionRegisterFormTag}>
    {$actionRegisterFormStart}
    <h2 class='text-xs-center card-title-raised'><i class='fal fa-user-edit'></i> {$textRegister}</h2>
    {$contentUserName}
    <fieldset>
        <label for='reg_email' class='required'><i class='far fa-envelope'></i> {$textEmailAddress}</label>
        <input id='reg_email' name='email' type='email' autocomplete='email' value='{$valueEmail}'>
    </fieldset>
    {$contentPassword} 
    {$actionRegisterForm}
    <fieldset>
        <button type='submit' class='button float-xs-right' name='register' value='{$textRegister}'>
            <i class='far fa-user-plus'></i> 
            {$textRegister}
        </button>
    </fieldset>
    {$nonceRegister}
    {$actionRegisterFormEnd}
    </form></div>";
}
/**--------------------------------------[LOGIN]*/
ob_start();
woocommerce_login_form();
$contentFormLogin = ob_get_clean();
$actionBeforeCustomerLoginForm = WPUtils::doAction('woocommerce_before_customer_login_form');
$actionAfterCustomerLoginForm = WPUtils::doAction('woocommerce_after_customer_login_form');
echo "{$actionBeforeCustomerLoginForm}
<div id='customer_login' class='row text-xs-center'>
{$contentFormLogin}
{$contentFormRegister}
</div>
{$actionAfterCustomerLoginForm}";