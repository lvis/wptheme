<?php
/**
 * Display single product reviews (comments)
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product-reviews.php.
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.6.0
 */
defined( 'ABSPATH' ) || exit;

global $product;
$htmlReviewsPage = '';
if ( comments_open() ) {
    $textCurrentReviews = __('Reviews', 'woocommerce');
    $count = $product->get_review_count();
    if ($count && wc_review_ratings_enabled()) {
        /* translators: 1: reviews count 2: product name */
        $textCurrentReviews = esc_html(_n('%1$s review for %2$s', '%1$s reviews for %2$s', $count, 'woocommerce'));
        $textCurrentReviews = sprintf($textCurrentReviews, esc_html($count), get_the_title());
        $textCurrentReviews = apply_filters('woocommerce_reviews_title', $textCurrentReviews, $count, $product);
    }
    $textNoReviewsYet = __('There are no reviews yet.', 'woocommerce');
    $htmlComments = "<p class='woocommerce-noreviews text-xs-center'>{$textNoReviewsYet}</p>";
    $htmlCommentsPagination = '';
    if (have_comments()) {
        $argsComments = apply_filters('woocommerce_product_review_list_args', ['callback' => 'woocommerce_comments']);
        $argsComments['echo'] = false;
        $argsComments['avatar_size'] = 64;
        $htmlComments = wp_list_comments($argsComments);
        $htmlComments = "<ol class='commentlist'>{$htmlComments}</ol>";
        if (get_comment_pages_count() > 1 && get_option('page_comments')) {
            $argsCommentsPagination = apply_filters('woocommerce_comment_pagination_args', [
                'prev_text' => '&larr;',
                'next_text' => '&rarr;',
                'type' => 'list',
            ]);
            $argsCommentsPagination['echo'] = false;
            $htmlCommentsPagination = paginate_comments_links($argsCommentsPagination);
            $htmlCommentsPagination = "<nav class='woocommerce-pagination'>{$htmlCommentsPagination}</nav>";
        }
    }
    $textOnlyProductCustomer = __('Only logged in customers who have purchased this product may leave a review.',
        'woocommerce');
    $htmlReviewForm = "<p class='woocommerce-verification-required'>{$textOnlyProductCustomer}</p>";
    if (get_option('woocommerce_review_rating_verification_required') === 'no' ||
        wc_customer_bought_product('', get_current_user_id(), $product->get_id())) {
        $commenter = wp_get_current_commenter();
        $textTitleReply = sprintf(__('Be the first to review &ldquo;%s&rdquo;', 'woocommerce'), get_the_title());
        if (have_comments()) {
            $textTitleReply = __('Add a review', 'woocommerce');
        }
        $textLeaveReply = __('Leave a Reply to %s', 'woocommerce');
        $textName = __('Name', 'woocommerce');
        $reviewAuthorId = esc_attr($commenter['comment_author']);
        $htmlReviewAuthor = "<fieldset class='comment-form-author'>
        <label for='author' class='title required'><i class='fas fa-user'></i> <span>{$textName}</span></label>
        <input id='author' name='author' type='text' value='{$reviewAuthorId}' size='30' required>
        </fieldset>";
        $textEmail = __('Email', 'woocommerce');
        $reviewAuthorEmail = esc_attr($commenter['comment_author_email']);
        $htmlReviewAuthorEmail = "<fieldset class='comment-form-email'>
        <label for='email' class='title required'><i class='fas fa-envelope'></i> <span>{$textEmail}</span></label> 
        <input id='email' name='email' type='email' value='{$reviewAuthorEmail}' size='30' required>
        </fieldset>";
        $textSubmit = __('Submit', 'woocommerce');
        $argsReviewForm = [
            'title_reply' => $textTitleReply,
            'title_reply_to' => $textLeaveReply,
            'title_reply_before' => '<h4 id="reply-title" class="title text-xs-center"><i class="fa fa-comment-dots"></i> ',
            'title_reply_after' => '</h4>',
            'comment_notes_after' => '',
            'fields' => ['author' => $htmlReviewAuthor, 'email' => $htmlReviewAuthorEmail],
            'label_submit' => $textSubmit,
            'class_submit' => 'submit',
            'submit_field' => '<fieldset class="form-submit text-xs-right">%1$s %2$s</fieldset>',
            'logged_in_as' => '',
            'comment_field' => '',
        ];
        if ($account_page_url = wc_get_page_permalink('myaccount')) {
            $textYouMustBeLogged = __('You must be <a href="%s">logged in</a> to post a review.', 'woocommerce');
            $textYouMustBeLogged = sprintf($textYouMustBeLogged, esc_url($account_page_url));
            $argsReviewForm['must_log_in'] = "<p class='must-log-in'>{$textYouMustBeLogged}</p>";
        }
        if (get_option('woocommerce_enable_review_rating') === 'yes') {
            $textYourRating = __('Your rating', 'woocommerce');
            $textRate = __('Rate&hellip;', 'woocommerce');
            $textPerfect = __('Perfect', 'woocommerce');
            $textGood = __('Good', 'woocommerce');
            $textAverage = __('Average', 'woocommerce');
            $textNotThatBad = __('Not that bad', 'woocommerce');
            $textVeryPoor = __('Very poor', 'woocommerce');
            $argsReviewForm['comment_field'] = "<fieldset class='comment-form-rating'>
            <label for='rating' class='title required'><i class='fas fa-comment-exclamation'></i> <span>{$textYourRating}</span></label>
            <select name='rating' id='rating' required>
                <option value=''>{$textRate}</option>
                <option value='5'>{$textPerfect}</option>
                <option value='4'>{$textGood}</option>
                <option value='3'>{$textAverage}</option>
                <option value='2'>{$textNotThatBad}</option>
                <option value='1'>{$textVeryPoor}</option>
            </select></fieldset>";
        }

        $textYourReview = __('Your review', 'woocommerce');
        $argsReviewForm['comment_field'] .= "<fieldset class='comment-form-comment'>
            <label for='comment' class='title required'><i class='fas fa-comment-edit'></i> <span>{$textYourReview}</span></label>
            <textarea id='comment' name='comment' cols='45' rows='8' required></textarea></fieldset>";
        $argsReviewForm = apply_filters('woocommerce_product_review_comment_form_args', $argsReviewForm);
        ob_start();
        comment_form($argsReviewForm);
        $htmlReviewForm = ob_get_clean();
        $htmlReviewForm = "<div id='review_form_wrapper'>
        <div id='review_form'>{$htmlReviewForm}</div></div>";
    }
    $htmlReviewsPage = "<div id='reviews' class='woocommerce-Reviews col-md-10 offset-md-1'>
    <div id='comments'>
        <h4 class='title text-xs-center'><i class='fas fa-comments'></i> {$textCurrentReviews}</h4>
        {$htmlComments}
        {$htmlCommentsPagination}
    </div>
    {$htmlReviewForm}
    <div class='clear'></div></div>";
}
echo $htmlReviewsPage;