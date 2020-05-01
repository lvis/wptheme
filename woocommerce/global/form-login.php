<?php
/**
 * Login form
 * This template can be overridden by copying it to yourtheme/woocommerce/global/form-login.php.
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */
defined('ABSPATH') || exit;

use wp\UtilsWp;

if (is_user_logged_in() == false) {
    $actionLoginFormStart = UtilsWp::doAction('woocommerce_login_form_start');
    $actionLoginForm = UtilsWp::doAction('woocommerce_login_form');
    $actionLoginFormEnd = UtilsWp::doAction('woocommerce_login_form_end');
    $textLogin = __('Login', 'woocommerce');
    $textLogIn = __('Log in', 'woocommerce');
    $textEmailAddress = __('Email address', 'woocommerce');
    $textPassword = __('Password', 'woocommerce');
    $textLostPassword = __('Lost your password?', 'woocommerce');
    $textRememberMe = __('Remember me', 'woocommerce');
    $textClose = __('Close');
    $valueUserName = '';
    if (!empty($_POST['username'])) {
        $valueUserName = esc_attr(wp_unslash($_POST['username']));
    }
    $contentFormLoginIsHidden = '';
    if (isset($hidden) && $hidden) {
        $contentFormLoginIsHidden = "<a class='button' href='#'>{$textClose}</a>";
    }
    $formLoginMessage = '';
    if (isset($message) && $message) {
        $formLoginMessage = wpautop(wptexturize($message));
    }
    $contentRedirect = '';
    if (isset($redirect) && $redirect) {
        $urlRedirect = esc_url($redirect);
        $contentRedirect = "<input type='hidden' name='redirect' value='{$urlRedirect}'>";
    }
    $urlLostPassword = esc_url(wp_lostpassword_url());
    $nonceLogin = wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce', true, false);
    $cssClass = '';
    if (is_checkout()){
        $cssClass = 'col-xs-10 col-sm-8 col-md-6 col-lg-4';
    }
    echo "<form method='post' class='woocommerce-form woocommerce-form-login login {$cssClass}'>
        <fieldset>
        {$actionLoginFormStart}
        <legend><i class='fas fa-user-lock'></i> {$textLogin}</legend>
        {$formLoginMessage}
        <p>
            <span class='float-xs-right'>
                <input id='rememberme' name='rememberme' type='checkbox' value='forever'>
                <label for='rememberme'>{$textRememberMe}</label>
            </span>
            <label for='username' class='required d-xs-inline-block'>
                <i class='fas fa-envelope'></i> {$textEmailAddress}
            </label>
            <input id='username' name='username' type='text' autocomplete='username' value='{$valueUserName}'>
        </p>
        <p>
            <a class='float-xs-right' href='{$urlLostPassword}'>{$textLostPassword}</a>
            <label for='password' class='required d-xs-inline-block'>
                <i class='fas fa-key'></i> {$textPassword}
            </label>
            <input id='password' name='password' type='password' autocomplete='current-password'>
        </p>
        {$actionLoginForm}
        <p class='text-xs-center'>
            {$contentFormLoginIsHidden}
            <button type='submit' name='login' value='{$textLogIn}' class='button'>
                <i class='fas fa-sign-in-alt'></i> {$textLogIn}
            </button>
        </p>
        {$nonceLogin}
        {$contentRedirect}
        {$actionLoginFormEnd}
    </fieldset></form>";
}