<?php
/**
 * My Addresses
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-address.php.
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 2.6.0
 */
defined('ABSPATH') || exit;

use wp\WPUtils;
use wp\UtilsWooCommerce;

$customerId = get_current_user_id();
$textBillingAddress = __('Billing address', 'woocommerce');
$textShippingAddress = __('Shipping address', 'woocommerce');
if (!wc_ship_to_billing_address_only() && wc_shipping_enabled()) {
    $customerAddresses = apply_filters('woocommerce_my_account_get_addresses', ['billing' => $textBillingAddress,
        'shipping' => $textShippingAddress], $customerId);
} else {
    $customerAddresses = apply_filters('woocommerce_my_account_get_addresses', ['billing' => $textBillingAddress],
        $customerId);
}

$textDescription = __('The following addresses will be used on the checkout page by default.', 'woocommerce');
$textDescription = apply_filters('woocommerce_my_account_my_address_description', $textDescription);
$content = '';
$current_user = wp_get_current_user();
foreach ($customerAddresses as $name => $title) {
    $load_address = sanitize_key($name);
    $userMetaName = $load_address . '_country';
    $userMetaCountryValue = get_user_meta(get_current_user_id(), $userMetaName, true);
    $address = wc()->countries->get_address_fields($userMetaCountryValue, $load_address . '_');
    // Enqueue scripts.
    wp_enqueue_script('wc-country-select');
    wp_enqueue_script('wc-address-i18n');
    foreach ($address as $key => $field) {
        $value = get_user_meta(get_current_user_id(), $key, true);
        if (!$value) {
            switch ($key) {
                case 'billing_email':
                case 'shipping_email':
                    $value = $current_user->user_email;
                    break;
                case 'billing_country':
                case 'shipping_country':
                    $value = WC()->countries->get_base_country();
                    break;
                case 'billing_state':
                case 'shipping_state':
                    $value = WC()->countries->get_base_state();
                    break;
            }
        }
        $address[$key]['value'] = apply_filters('woocommerce_my_account_edit_address_field_value', $value, $key, $load_address);
    }
    $address = apply_filters('woocommerce_address_to_edit', $address, $load_address);
    if ($load_address) {
        $pageTitle = $textShippingAddress;
        $iconAddress = 'truck';
        if ($load_address === 'billing') {
            $iconAddress = 'receipt';
            $pageTitle = $textBillingAddress;
        }
        $pageTitle = apply_filters('woocommerce_my_account_edit_address_title', $pageTitle, $load_address);

        /** Form Content*/
        $contentAddress = '';
        foreach ($address as $key => $field) {
            if (isset($field['country_field'], $address[$field['country_field']])) {
                $defaultAddress = $address[$field['country_field']]['value'];
                $field['country'] = wc_get_post_data_by_key($field['country_field'], $defaultAddress);
            }
            $field['return'] = true;
            $contentAddress .= UtilsWooCommerce::getFormField($key, $field, wc_get_post_data_by_key($key, $field['value']));
        }
        $actionEditAddressFormBefore = WPUtils::doAction("woocommerce_before_edit_address_form_{$load_address}");
        $actionEditAddressFormAfter = WPUtils::doAction("woocommerce_after_edit_address_form_{$load_address}");
        $actionEditAccountAddressFormBefore = WPUtils::doAction('woocommerce_before_edit_account_address_form');
        $actionEditAccountAddressFormAfter = WPUtils::doAction('woocommerce_after_edit_account_address_form');
        $nonceEditAddress = wp_nonce_field('woocommerce-edit_address', 'woocommerce-edit-address-nonce',
            true, false);
        $textSaveAddress = __('Save address', 'woocommerce');
        $contentAddress = "{$actionEditAccountAddressFormBefore}
        <form method='post'>
        <h3 class='text-xs-center'><i class='fa fa-{$iconAddress}'></i> {$pageTitle}</h3>
        <div class='woocommerce-address-fields'>
            {$actionEditAddressFormBefore}
            <div class='woocommerce-address-fields__field-wrapper'>
                {$contentAddress}
            </div>
            {$actionEditAddressFormAfter}
            <p class='text-xs-center'>
                <button name='save_address' type='submit'><i class='fa fa-edit'></i> {$textSaveAddress}</button>
                {$nonceEditAddress}
                <input type='hidden' name='action' value='edit_address_{$load_address}'>
            </p>
        </div>
        </form>{$actionEditAccountAddressFormAfter}";
    } else {
        $address = __('You have not set up this type of address yet.', 'woocommerce');
    }
    $urlEditAddress = esc_url(wc_get_endpoint_url('edit-address', $name));
    $textEditAddress = __('Edit', 'woocommerce');
    $content .= "<div class='col-xs-12 col-md-6'><div class='card'>
    <div class='card-content'>{$contentAddress}</div></div></div>";
}
echo "<h5 class='text-xs-center'>{$textDescription}</h5><div class='row text-xs-center'>{$content}</div>";