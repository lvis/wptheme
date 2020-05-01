<?php
/**
 * Login Form
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */
defined('ABSPATH') || exit;

use wp\UtilsWp;


/**--------------------------------------[LOGIN]*/
ob_start();
woocommerce_login_form();
$contentFormLogin = ob_get_clean();
/**--------------------------------------[Register]*/
$contentFormRegister = '';
if (get_option('woocommerce_enable_myaccount_registration') === 'yes') {
    //Username
    $contentUserName = '';
    if (get_option('woocommerce_registration_generate_username') === 'no') {
        $textUserName = __('Username', 'woocommerce');
        $valueUserName = '';
        if (empty($_POST['username']) != false) {
            $valueUserName = esc_attr(wp_unslash($_POST['username']));
        }
        $contentUserName = "<p>
        <label for='reg_username' class='required'><i class='fas fa-user'></i> {$textUserName}</label>
        <input id='reg_username' name='username' type='text' autocomplete='username' value='{$valueUserName}'></p>";
    }
    //Password
    $contentPassword = esc_html__( 'A password will be sent to your email address.', 'woocommerce' );
    if (get_option('woocommerce_registration_generate_password') === 'no') {
        $textPassword = __('Password', 'woocommerce');
        $contentPassword = "<p>
        <label for='reg_password' class='required'><i class='fas fa-key'></i> {$textPassword}</label>
        <input id='reg_password' name='password' type='password' autocomplete='new-password'></p>";
    }
    //Email
    $textEmailAddress = __('Email address', 'woocommerce');
    $valueEmail = '';
    if (empty($_POST['email']) != false) {
        $valueEmail = esc_attr(wp_unslash($_POST['email']));
    }
    $actionRegisterFormTag = UtilsWp::doAction('woocommerce_register_form_tag' );
    $actionRegisterFormStart = UtilsWp::doAction('woocommerce_register_form_start');
    $actionRegisterForm = UtilsWp::doAction('woocommerce_register_form');
    $actionRegisterFormEnd = UtilsWp::doAction('woocommerce_register_form_end');
    $textRegister = __('Register', 'woocommerce');
    $nonceRegister = wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce', true, false);
    $contentFormRegister = "<form method='post' {$actionRegisterFormTag} class='woocommerce-form woocommerce-form-register register'>
    <fieldset>
    {$actionRegisterFormStart}
    <legend><i class='fas fa-user-edit'></i> {$textRegister}</legend>
    {$contentUserName}
    <p>
        <label for='reg_email' class='required'><i class='fas fa-envelope'></i> {$textEmailAddress}</label>
        <input id='reg_email' name='email' type='email' autocomplete='email' value='{$valueEmail}'>
    </p>
    {$contentPassword} 
    {$actionRegisterForm}
    <p class='text-xs-center'>
        <button type='submit' name='register' value='{$textRegister}' class='button'>
            <i class='fas fa-user-plus'></i> {$textRegister}
        </button>
    </p>
    {$nonceRegister}
    {$actionRegisterFormEnd}
    </fieldset></form>";
}
$actionBeforeCustomerLoginForm = UtilsWp::doAction('woocommerce_before_customer_login_form');
$actionAfterCustomerLoginForm = UtilsWp::doAction('woocommerce_after_customer_login_form');
echo "{$actionBeforeCustomerLoginForm}<div id='customer_login' class='text-xs-center'>
<div class='col-sm-12 col-md-6'>{$contentFormLogin}</div>
<div class='col-sm-12 col-md-6'>{$contentFormRegister}</div>
</div>{$actionAfterCustomerLoginForm}";