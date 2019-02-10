<?php
/**
 * Login form
 * This template can be overridden by copying it to yourtheme/woocommerce/global/form-login.php.
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.3.0
 */
defined('ABSPATH') || exit;

use wp\WPUtils;

if (is_user_logged_in() == false) {
    $actionLoginFormStart = WPUtils::doAction('woocommerce_login_form_start');
    $actionLoginForm = WPUtils::doAction('woocommerce_login_form');
    $actionLoginFormEnd = WPUtils::doAction('woocommerce_login_form_end');
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
    echo "<div class='col-xs-12 col-md-6'>
    <form method='post' class='woocommerce-form-login login'>
    {$actionLoginFormStart}
    <h2 class='text-xs-center card-title-raised'><i class='fal fa-user-lock'></i> {$textLogin}</h2>
    {$formLoginMessage}
    <fieldset>
        <div class='float-xs-right'><input id='rememberme' name='rememberme' type='checkbox' value='forever'>
        <label for='rememberme'>{$textRememberMe}</label></div>
        <label for='username' class='required'><i class='far fa-envelope'></i> {$textEmailAddress}</label>
        <input id='username' name='username' type='text' autocomplete='username' value='{$valueUserName}'>
    </fieldset>
    <fieldset>
        <a href='{$urlLostPassword}' class='float-xs-right'>{$textLostPassword}</a>
        <label for='password' class='required'><i class='far fa-key'></i> {$textPassword}</label>
        <input id='password' name='password' type='password' autocomplete='current-password'>
    </fieldset>
    {$actionLoginForm}
    <fieldset>
        {$contentFormLoginIsHidden}
        <button type='submit' class='button float-xs-right' name='login' value='{$textLogIn}'>
            <i class='far fa-sign-in-alt'></i> {$textLogIn}
        </button>
    </fieldset>
    {$nonceLogin}
    {$contentRedirect}
    {$actionLoginFormEnd}
    </form></div>";
}