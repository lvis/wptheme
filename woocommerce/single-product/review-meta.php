<?php
/**
 * The template to display the reviewers meta data (name, verified owner, review date)
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/review-meta.php.
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */
defined('ABSPATH') || exit;
global $comment;
$verified = wc_review_is_from_verified_owner($comment->comment_ID);
$htmlReviewMeta = '';
if ($comment->comment_approved === '0') {
    $textReviewWaitApprove = __('Your review is awaiting approval', 'woocommerce');
    $htmlReviewMeta = "<p class='meta'><em class='woocommerce-review__awaiting-approval'>{$textReviewWaitApprove}</em></p>";
} else {
    $commentAuthor = get_comment_author();
    $commentDate = esc_attr(get_comment_date('c'));
    $commentDateFormatted = esc_html(get_comment_date(wc_date_format()));
    if ('yes' === get_option('woocommerce_review_rating_verification_label') && $verified) {
        $textVerifiedOwner = __('verified owner', 'woocommerce');
        $htmlReviewMeta = "<em class='woocommerce-review__verified verified'>({$textVerifiedOwner})</em>";
    }
    $htmlReviewMeta = "<p class='meta'>
        <strong class='woocommerce-review__author'>{$commentAuthor}</strong>
        {$htmlReviewMeta}
        <span class='woocommerce-review__dash'>&ndash;</span> 
        <time class='woocommerce-review__published-date' datetime='{$commentDate}'>{$commentDateFormatted}</time>
    </p>";
}
echo $htmlReviewMeta;