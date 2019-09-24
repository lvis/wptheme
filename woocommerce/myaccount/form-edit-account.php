<?php
/**
 * Edit account form
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-edit-account.php.
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */
defined('ABSPATH') || exit;

use wp\UtilsWp;

$textFirstName = __('First name', 'woocommerce');
$valueFirstName = esc_attr($user->first_name);
$textLastName = __('Last name', 'woocommerce');
$valueLastName = esc_attr($user->last_name);
$textDisplayName = __('Display name', 'woocommerce');
$textDisplayNameInfo = __('This will be how your name will be displayed in the account section and in reviews', 'woocommerce');
$valueDisplayName = esc_attr($user->display_name);
$textEmailAddress = __('Email address', 'woocommerce');
$valueEmailAddress = esc_attr($user->user_email);
$textEditAccount = __('Edit account', 'woocommerce');
$textPasswordChange = __('Password change', 'woocommerce');
$textPasswordCurrent = __('Current password (leave blank to leave unchanged)', 'woocommerce');
$textPasswordNew = __('New password (leave blank to leave unchanged)', 'woocommerce');
$textPasswordConfirm = __('Confirm new password', 'woocommerce');
$textSaveChanges = __('Save changes', 'woocommerce');
$nonceSaveAccountDetails = wp_nonce_field('save_account_details', 'save-account-details-nonce', true, false);
$actionBeforeEditAccountForm = UtilsWp::doAction('x');
$actionEditAccountFormTag = UtilsWp::doAction('woocommerce_edit_account_form_tag');
$actionAfterEditAccountForm = UtilsWp::doAction('woocommerce_after_edit_account_form');
$actionEditAccountFormStart = UtilsWp::doAction('woocommerce_edit_account_form_start');
$actionEditAccountForm = UtilsWp::doAction('woocommerce_edit_account_form');
$actionEditAccountFormEnd = UtilsWp::doAction('woocommerce_edit_account_form_end');
echo "{$actionBeforeEditAccountForm}
<form class='woocommerce-EditAccountForm edit-account' action='' method='post' {$actionEditAccountFormTag}>
{$actionEditAccountFormStart}
<div class='col-xs-12 col-md-6'>
    <div class='card'>
        <div class='card-content'>
            <h4 class='text-xs-center'><i class='fas fa-user-edit'></i> {$textEditAccount}</h4>
            <fieldset>
                <label for='account_first_name' class='required'><i class='fas fa-user'></i> {$textFirstName}</label>
                <input id='account_first_name' name='account_first_name' type='text' autocomplete='given-name' value='{$valueFirstName}'>
            </fieldset>
            <fieldset>
                <label for='account_last_name' class='required'><i class='fas fa-user'></i> {$textLastName}</label>
                <input id='account_last_name' name='account_last_name' type='text' autocomplete='family-name' value='{$valueLastName}'>
            </fieldset>
            <fieldset>
                <label for='account_display_name' class='required'><i class='fas fa-eye'></i> {$textDisplayName}</label>
                <input id='account_display_name' name='account_display_name' type='text' value='{$valueDisplayName}'>
                <small>{$textDisplayNameInfo}</small>
            </fieldset>
            <fieldset>
                <label for='account_email' class='required'><i class='fas fa-envelope'></i> {$textEmailAddress}</label>
                <input id='account_email' name='account_email' type='email' autocomplete='email' value='{$valueEmailAddress}'>
            </fieldset>
        </div>
    </div>
</div>
<div class='col-xs-12 col-md-6'>
    <div class='card'>
        <div class='card-content'>
            <h4 class='text-xs-center'><i class='fas fa-user-lock'></i> {$textPasswordChange}</h4>
            <fieldset>
                <label for='password_current'>{$textPasswordCurrent}</label>
                <input id='password_current' name='password_current' type='password' autocomplete='off'>
            </fieldset>
            <fieldset>
                <label for='password_1'>{$textPasswordNew}</label>
                <input id='password_1' name='password_1' type='password' autocomplete='off'>
            </fieldset>
            <fieldset>
                <label for='password_2'>{$textPasswordConfirm}</label>
                <input id='password_2' name='password_2' type='password' autocomplete='off'>
            </fieldset>
        </div>
    </div>
</div>{$actionEditAccountForm}
<p class='text-xs-center'>
    <button name='save_account_details' type='submit' class='button'>{$textSaveChanges}</button>
    <input value='save_account_details' type='hidden' name='action'>
    {$nonceSaveAccountDetails}
</p>{$actionEditAccountFormEnd}</form>{$actionAfterEditAccountForm}";