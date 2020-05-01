<?php
/**
 * Checkout terms and conditions area.
 * @package WooCommerce/Templates
 * @version 3.4.0
 */
defined('ABSPATH') || exit;

use wp\UtilsWp;

$showCheckoutTerms = apply_filters('woocommerce_checkout_show_terms', true);
if ($showCheckoutTerms && function_exists('wc_terms_and_conditions_checkbox_enabled')) {
    $actionCheckoutTermsConditionsBefore = UtilsWp::doAction('woocommerce_checkout_before_terms_and_conditions');
    $actionCheckoutTermsConditionsAfter = UtilsWp::doAction('woocommerce_checkout_after_terms_and_conditions');
    /**
     * Terms and conditions hook used to inject content.
     * @since 3.4.0.
     * @hooked wc_checkout_privacy_policy_text() Shows custom privacy policy text. Priority 20.
     * @hooked wc_terms_and_conditions_page_content() Shows t&c page content. Priority 30.
     */
    $actionCheckoutTermsConditions = UtilsWp::doAction('woocommerce_checkout_terms_and_conditions');
    $htmlTermConditionValidate = '';
    if (wc_terms_and_conditions_checkbox_enabled()) {
        $valueFromPostTerms = apply_filters('woocommerce_terms_is_checked_default', isset($_POST['terms']));
        $valueCheckoutTermsConditionsChecked = checked($valueFromPostTerms, true);
        $textTermsAndConditions = wc_get_terms_and_conditions_checkbox_text();
        if ( $textTermsAndConditions ) {
            $textTermsAndConditions = wp_kses_post( wc_replace_policy_page_link_placeholders( $textTermsAndConditions ) );
        }
        $htmlTermConditionValidate = "<p class='form-row validate-required'>
        <input id='terms' name='terms' {$valueCheckoutTermsConditionsChecked} type='checkbox' required>
        <label for='terms' class='woocommerce-terms-and-conditions-checkbox-text'>{$textTermsAndConditions}</label>
        <input name='terms-field' value='1' type='hidden'></p>";
    }
    echo "{$actionCheckoutTermsConditionsBefore}
    <div class='woocommerce-terms-and-conditions-wrapper'>
    {$actionCheckoutTermsConditions}
    {$htmlTermConditionValidate}
    </div>{$actionCheckoutTermsConditionsAfter}";
}