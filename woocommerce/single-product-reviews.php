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
$htmlReviewsPage = '';
if ( comments_open() ) {
    $hasReviews = have_comments();
    /**@global $product WC_Product*/
    global $product;
    //Reviews: Form
    $htmlReviewForm = '';
    $customerIsAllowedToReview = wc_customer_bought_product('', get_current_user_id(), $product->get_id());
    if (get_option('woocommerce_review_rating_verification_required') === 'no' || $customerIsAllowedToReview) {
        $argsReviewForm = [];
        //Title
        if ($hasReviews) {
            $argsReviewForm['title_reply'] = __('Add a review', 'woocommerce');
        } else {
            $postTitle = get_the_title();
            $textBeFirstToReview = __('Be the first to review &ldquo;%s&rdquo;', 'woocommerce');
            $argsReviewForm['title_reply'] = sprintf($textBeFirstToReview, $postTitle);
        }
        $argsReviewForm['title_reply_to'] = __('Leave a Reply to %s', 'woocommerce');
        $argsReviewForm['title_reply_before'] = '<h4 id="reply-title" class="comment-reply-title"><i class="fa fa-comment-edit"></i> ';
        $argsReviewForm['title_reply_after'] = '</h4>';
        //Field: Login
        $urlAccountPage = wc_get_page_permalink('myaccount');
        if ($urlAccountPage) {
            $urlAccountPage = esc_url($urlAccountPage);
            $textYouMustBeLogged = __('You must be <a href="%s">logged in</a> to post a review.', 'woocommerce');
            $textYouMustBeLogged = sprintf($textYouMustBeLogged, $urlAccountPage);
            $argsReviewForm['must_log_in'] = "<p class='must-log-in'>{$textYouMustBeLogged}</p>";
        }
        $argsReviewForm['fields'] = [];
        $commenter = wp_get_current_commenter();
        //Field: Name
        $textName = __('Name', 'woocommerce');
        $valueName = esc_attr($commenter['comment_author']);
        $argsReviewForm['fields']['author'] =  "<fieldset class='comment-form-author'>
        <label for='author' class='title required'><i class='fa fa-user'></i> <span>{$textName}</span></label>
        <input id='author' name='author' type='text' value='{$valueName}' size='30' required></fieldset>";
        //Field: Email
        $textEmail = __('Email', 'woocommerce');
        $valueEmail = esc_attr($commenter['comment_author_email']);
        $argsReviewForm['fields']['email'] = "<fieldset class='comment-form-email'>
        <label for='email' class='title required'><i class='fa fa-envelope'></i> <span>{$textEmail}</span></label> 
        <input id='email' name='email' type='email' value='{$valueEmail}' size='30' required></fieldset>";
        //Field: Rating
        if (wc_review_ratings_enabled()) {
            $textYourRating = __('Your rating', 'woocommerce');
            $textRate = __('Rate&hellip;', 'woocommerce');
            $textPerfect = __('Perfect', 'woocommerce');
            $textGood = __('Good', 'woocommerce');
            $textAverage = __('Average', 'woocommerce');
            $textNotThatBad = __('Not that bad', 'woocommerce');
            $textVeryPoor = __('Very poor', 'woocommerce');
            $argsReviewForm['comment_field'] = "<fieldset class='comment-form-rating'>
            <label for='rating'><i class='fal fa-comment-smile'></i> <span>{$textYourRating}</span></label>
            <select name='rating' id='rating' required>
                <option value=''>{$textRate}</option>
                <option value='5'>{$textPerfect}</option>
                <option value='4'>{$textGood}</option>
                <option value='3'>{$textAverage}</option>
                <option value='2'>{$textNotThatBad}</option>
                <option value='1'>{$textVeryPoor}</option>
            </select></fieldset>";
        }
        //Field: Comment
        $textYourReview = __('Your review', 'woocommerce');
        $argsReviewForm['comment_field'] .= "<fieldset class='comment-form-comment'>
            <label for='comment'><i class='fal fa-comment-dots'></i> <span>{$textYourReview}</span></label>
            <textarea id='comment' name='comment' cols='45' rows='8' required></textarea></fieldset>";
        $argsReviewForm = apply_filters('woocommerce_product_review_comment_form_args', $argsReviewForm);
        //Field: Submit
        $argsReviewForm['label_submit'] = __('Submit', 'woocommerce');
        $argsReviewForm['class_submit'] = 'submit';
        $argsReviewForm['submit_field'] = '<fieldset class="form-submit text-xs-center">%1$s %2$s</fieldset>';
        ob_start();
        comment_form($argsReviewForm);
        $htmlReviewForm = ob_get_clean();
        $htmlReviewForm = "<div id='review_form'>{$htmlReviewForm}</div>";
    } else {
        $textOnlyLogged = __('Only logged in customers who have purchased this product may leave a review.', 'woocommerce');
        $htmlReviewForm = "<p class='woocommerce-verification-required'>{$textOnlyLogged}</p>";
    }
    //Reviews: List
    $textCurrentReviews = '';
    $count = $product->get_review_count();
    if ($count && wc_review_ratings_enabled()) {
        /* translators: 1: reviews count 2: product name */
        $textCurrentReviews = esc_html(_n('%1$s review for %2$s', '%1$s reviews for %2$s', $count, 'woocommerce'));
        $textCurrentReviews = sprintf($textCurrentReviews, esc_html($count), get_the_title());
        $textCurrentReviews = apply_filters('woocommerce_reviews_title', $textCurrentReviews, $count, $product);
    }


    $textNoReviewsYet = __('There are no reviews yet.', 'woocommerce');
    $htmlComments = "<p class='woocommerce-noreviews text-xs-center'>{$textNoReviewsYet}</p>";
    //Reviews: Pagination
    $htmlReviewsPagination = '';
    if ($hasReviews) {
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
            $htmlReviewsPagination = paginate_comments_links($argsCommentsPagination);
            $htmlReviewsPagination = "<nav class='woocommerce-pagination'>{$htmlReviewsPagination}</nav>";
        }
    }
    //Reviews: Section
    $textReviews = __('Reviews', 'woocommerce');
    $htmlReviewsPage = "<div id='reviews' class='woocommerce-Reviews col-sm-12 col-md-8 col-md-push-2 text-xs-center'>
    {$htmlReviewForm}
    <div id='comments'>
        <h4><i class='fa fa-comments'></i> {$textReviews}</h4>
        <p>{$textCurrentReviews}</p>
        {$htmlComments}
    {$htmlReviewsPagination}</div></div>";
}
echo $htmlReviewsPage;