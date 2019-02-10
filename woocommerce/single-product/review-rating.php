<?php
/**
 * The template to display the reviewers star rating in reviews
 * This template can be overridden by copying it to yourtheme/woocommerce/review-rating.php.
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.1.0
 */
defined('ABSPATH') || exit;
global $comment;
$rating = intval( get_comment_meta( $comment->comment_ID, 'rating', true ) );
if ( $rating && 'yes' === get_option( 'woocommerce_enable_review_rating' ) ) {
	echo wc_get_rating_html( $rating );
}