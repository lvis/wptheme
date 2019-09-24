<?php
/**
 * Review Comments Template
 * Closing li is left out on purpose!.
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/review.php.
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 2.6.0
 */
defined('ABSPATH') || exit;
use wp\UtilsWp;

$commentClass = comment_class('', null, null, false);
$commentId = get_comment_ID();
/**
 * The woocommerce_review_before hook
 * @hooked woocommerce_review_display_gravatar - 10
 */
$htmlReviewBefore = UtilsWp::doAction('woocommerce_review_before', $comment);
/**
 * The woocommerce_review_before_comment_meta hook.
 * @hooked woocommerce_review_display_rating - 10
 */
$htmlReviewCommentMetaBefore = UtilsWp::doAction('woocommerce_review_before_comment_meta', $comment);
/**
 * The woocommerce_review_meta hook.
 * @hooked woocommerce_review_display_meta - 10
 * @hooked WC_Structured_Data::generate_review_data() - 20
 */
$htmlReviewMeta = UtilsWp::doAction('woocommerce_review_meta', $comment);
/**
 * The woocommerce_review_comment_text hook
 * @hooked woocommerce_review_display_comment_text - 10
 */
$htmlReviewCommentText = UtilsWp::doAction('woocommerce_review_comment_text', $comment);
$htmlReviewCommentTextBefore = UtilsWp::doAction('woocommerce_review_before_comment_text', $comment);
$htmlReviewCommentTextAfter = UtilsWp::doAction('woocommerce_review_after_comment_text', $comment);
echo "<li id='{$commentId}' $commentClass>
    <div id='comment-{$commentId}' class='comment_container'>
        {$htmlReviewBefore}
        <div class='comment-text'>
            {$htmlReviewMeta}
            {$htmlReviewCommentMetaBefore}
            {$htmlReviewCommentTextBefore}
            {$htmlReviewCommentText}
            {$htmlReviewCommentTextAfter}
        </div>
    </div>
</li>";