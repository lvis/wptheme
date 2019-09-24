<?php
if (!function_exists('theme_comment')) {
    /**
     * Custom comment template
     *
     * @param $comment
     * @param $args
     * @param $depth
     */
    function theme_comment($comment, $args, $depth)
    {
        $GLOBALS['comment'] = $comment;
        switch ($comment->comment_type) :
            case 'pingback' :
            case 'trackback' : ?>
                <li class="pingback">
                    <p><?php _e('Pingback:', WpApp::TEXT_DOMAIN); ?><?php comment_author_link(); ?><?php edit_comment_link(__('(Edit)', WpApp::TEXT_DOMAIN), ' '); ?></p>
                </li>
                <?php
                break;
            default : ?>
            <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
                <article id="comment-<?php comment_ID(); ?>">
                    <a href="<?php comment_author_url(); ?>">
                        <?php echo get_avatar($comment, 110); ?>
                    </a>
                    <div class="comment-detail-wrap">
                        <span class="comment-detail-wrap-arrow"></span>
                        <div class="comment-meta">
                            <h5 class="author">
                                <cite class="fn"><?php printf(__('%s', WpApp::TEXT_DOMAIN), sprintf('<cite class="fn">%s</cite>', get_comment_author_link())); ?></cite>
                            </h5>
                            <p>
                                <?php _e('on', WpApp::TEXT_DOMAIN); ?>&nbsp;
                                <a href="<?php echo esc_url(get_comment_link($comment->comment_ID)); ?>">
                                    <time datetime="<?php comment_time('c'); ?>">
                                        <?php printf(__('%1$s at %2$s', WpApp::TEXT_DOMAIN), get_comment_date(), get_comment_time()); ?>
                                    </time>
                                </a>
                                &nbsp;<?php _e('said', WpApp::TEXT_DOMAIN); ?>&nbsp;
                            </p>
                        </div>

                        <div class="comment-body">
                            <?php comment_text(); ?>
                            <?php comment_reply_link(array_merge(array('before' => ''), array('depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
                        </div>
                    </div>
                </article>
                <?php
                break;
        endswitch;
    }
}

if (post_password_required()) {
    printf('<section id="comments"><p class="nopassword">%s</p></section>',
        __('This post is password protected. Enter the password to view comments.', WpApp::TEXT_DOMAIN));
} else {
    if (have_comments()) {
        $commentsNumber = get_comments_number(__('No Comment', WpApp::TEXT_DOMAIN), __('One Comment', WpApp::TEXT_DOMAIN), __('(%) Comments', WpApp::TEXT_DOMAIN));
        $commentsList = wp_list_comments(array('callback' => 'theme_comment', 'echo' => false));
        $commentsPaging = "";
        if (get_comment_pages_count() > 1 && get_option('page_comments')) {
            $commentsPaging = sprintf('<nav class="pagination comments-pagination"></nav>', paginate_comments_links());
        }
        $commentsClosed = "";
        if (!comments_open() && get_comments_number() != '0' && post_type_supports(get_post_type(), 'comments')) {
            $commentsClosed = sprintf('<p class="nocomments">%s</p>', __("Comments are closed.", WpApp::TEXT_DOMAIN));
        }
        printf('<section id="comments"><h3 id="comments-title">%s</h3><ol class="commentlist">%s</ol>%s %s</section>',
            $commentsNumber, $commentsList, $commentsPaging, $commentsClosed);
        comment_form();
    }
}