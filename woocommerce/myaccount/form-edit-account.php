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
$idFormEditAccount = 'edit-account';
$idFieldFirstName = 'account_first_name';
$idFieldLastName = 'account_last_name';
$idFieldDisplayName = 'account_display_name';
$idFieldEmail = 'account_email';
$idFieldPassCurrent = 'password_current';
$idFieldPassNew = 'password_1';
$idFieldPassConfirm = 'password_2';
$idFieldSave = 'save_account_details';
echo "{$actionBeforeEditAccountForm}
<form id='{$idFormEditAccount}' class='woocommerce-EditAccountForm edit-account row' action='' method='post' {$actionEditAccountFormTag}>
{$actionEditAccountFormStart}
<div class='col-sm-12 col-md-6'>
<fieldset>
    <legend><i class='fas fa-user-edit'></i> {$textEditAccount}</legend>
    <p>
        <label for='{$idFieldFirstName}' class='required'><i class='fas fa-user'></i> {$textFirstName}</label>
        <input type='text' id='{$idFieldFirstName}' name='{$idFieldFirstName}' autocomplete='given-name' data-bind='textInput:firstName'>
    </p>
    <p>
        <label for='{$idFieldLastName}' class='required'><i class='fas fa-user'></i> {$textLastName}</label>
        <input type='text' id='{$idFieldLastName}' name='{$idFieldLastName}' autocomplete='family-name' data-bind='textInput:lastName'>
    </p>
    <p class='d-xs-none'>
        <label for='{$idFieldDisplayName}' class='required'><i class='fas fa-eye'></i> {$textDisplayName}</label>
        <input type='text' id='{$idFieldDisplayName}' name='{$idFieldDisplayName}'   data-bind='textInput:displayName'>
        <small>{$textDisplayNameInfo}</small>
    </p>
    <p>
        <label for='{$idFieldEmail}' class='required'><i class='fas fa-envelope'></i> {$textEmailAddress}</label>
        <input type='email' id='{$idFieldEmail}' name='{$idFieldEmail}'  autocomplete='email'  data-bind='textInput:email'>
    </p>
    <p class='text-xs-center'>
        <button type='submit' name='{$idFieldSave}' class='button' data-bind='enable:isFormCompleted'>
            <i class='fas fa-user-check'></i> {$textSaveChanges}
        </button>
    </p>
</fieldset>
</div><div class='col-sm-12 col-md-6'>
<fieldset>
    <legend><i class='fas fa-user-lock'></i> {$textPasswordChange}</legend>
    <p>
        <label for='{$idFieldPassCurrent}' class='text-truncate'>
            <i class='fas fa-user-lock'></i> {$textPasswordCurrent}
        </label>
        <input type='password' id='{$idFieldPassCurrent}' name='{$idFieldPassCurrent}' autocomplete='off'>
    </p>
    <p>
        <label for='{$idFieldPassNew}' class='text-truncate'>
            <i class='fas fa-key'></i> {$textPasswordNew}
        </label>
        <input type='password' id='{$idFieldPassNew}' name='{$idFieldPassNew}' autocomplete='off'>
    </p>
    <p>
        <label for='{$idFieldPassConfirm}'><i class='fas fa-key'></i> {$textPasswordConfirm}</label>
        <input type='password' id='{$idFieldPassConfirm}' name='{$idFieldPassConfirm}' autocomplete='off'>
    </p>
    <p class='text-xs-center'>
        <button type='submit' name='{$idFieldSave}' class='button' data-bind='enable:isFormCompleted'>
            <i class='fas fa-user-check'></i> {$textSaveChanges}
        </button>
    </p>
</fieldset></div>{$actionEditAccountForm}
{$nonceSaveAccountDetails}
<input type='hidden' name='action' value='{$idFieldSave}'>
{$actionEditAccountFormEnd}</form>
{$actionAfterEditAccountForm}";
wp_add_inline_script('knockout', /**@lang JavaScript */"function ViewModelAccountEdit(){
    var self = this;
    self.firstName = ko.observable('{$valueFirstName}');
    self.lastName = ko.observable('{$valueLastName}');
    self.email = ko.observable('{$valueEmailAddress}');
    self.displayName = ko.pureComputed(function(){
        return self.firstName() + ' ' + self.lastName();
    });
    self.isFormCompleted = ko.pureComputed(function(){
       //https://www.w3resource.com/javascript/form/javascript-sample-registration-form-validation.php
       var patternName = /^[A-Za-z]+$/;
       var isValidFirstName = patternName.test(self.firstName());  
       var isValidLastName = patternName.test(self.lastName());  
       var patternEmail = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
       var isValidEmail = patternEmail.test(self.email());  
       return (isValidFirstName && isValidLastName && isValidEmail);
    });
}
ko.applyBindings(new ViewModelAccountEdit(), document.getElementById('{$idFormEditAccount}'));");