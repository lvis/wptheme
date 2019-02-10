<?php
/**
 * Checkout billing information form
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-billing.php.
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.0.9
 */
defined('ABSPATH') || exit;

use wp\WPUtils;
use wp\UtilsWooCommerce;

/** @global WC_Checkout $checkout */
/** ---------------------------------------- Billing*/
$formBillingTitle = __('Billing &amp; Shipping', 'woocommerce');
$actionCheckoutBillingFormBefore = WPUtils::doAction('woocommerce_before_checkout_billing_form', $checkout);
$contentFormFields = '';
$fieldsBilling = $checkout->get_checkout_fields('billing');
foreach ($fieldsBilling as $key => $field) {
    if (isset($field['country_field'], $fieldsBilling[$field['country_field']])) {
        $field['country'] = $checkout->get_value($field['country_field']);
    }
    $field['return'] = true;
    $contentFormFields .= UtilsWooCommerce::getFormField($key, $field, $checkout->get_value($key));
}
$actionCheckoutBillingFormAfter = WPUtils::doAction('woocommerce_after_checkout_billing_form', $checkout);
$contentFormBilling = "<div class='woocommerce-billing-fields'>
<h3><i class='fa fa-receipt'></i> {$formBillingTitle}</h3>
{$actionCheckoutBillingFormBefore}
<div class='woocommerce-billing-fields__field-wrapper'>{$contentFormFields}</div>
{$actionCheckoutBillingFormAfter}
</div>";
/** ---------------------------------------- Checkout Registration*/
$contentAccountFields = '';
if (is_user_logged_in() === false && $checkout->is_registration_enabled()) {
    $contentCreateAccount = '';
    if ($checkout->is_registration_required() == false) {
        $textCreateAccount = __('Create an account?', 'woocommerce');
        $checkedCreateAccount = apply_filters('woocommerce_create_account_default_checked', false);
        $checkedCreateAccount = ($checkout->get_value('createaccount' === true) || ($checkedCreateAccount === true));
        $checkedCreateAccount = checked($checkedCreateAccount, true, false);
        $contentCreateAccount .= "<label for='createaccount'><h3>
        <i class='fa fa-user-plus'></i>
        <span>{$textCreateAccount}</span>
        <input id='createaccount' type='checkbox' name='createaccount' value='1' {$checkedCreateAccount}>
        </h3></label>";
    }
    $fieldsAccount = $checkout->get_checkout_fields('account');
    if ($fieldsAccount) {
        $contentCreateAccountFields = '';
        foreach ($fieldsAccount as $key => $field) {
            $field['return'] = true;
            $contentCreateAccountFields .= UtilsWooCommerce::getFormField($key, $field, $checkout->get_value($key));
        }
        $contentCreateAccount .= "<div class='create-account'>{$contentCreateAccountFields}</div>";
    }
    $actionCheckoutRegFormBefore = WPUtils::doAction('woocommerce_before_checkout_registration_form', $checkout);
    $actionCheckoutRegFormAfter = WPUtils::doAction('woocommerce_after_checkout_registration_form', $checkout);
    $contentAccountFields = "<div class='woocommerce-account-fields'>
    {$actionCheckoutRegFormBefore}
    {$contentCreateAccount}
    {$actionCheckoutRegFormAfter}</div>";
}
echo $contentFormBilling.$contentAccountFields;