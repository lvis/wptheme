<?php /** Author: Vitalie Lupu <vitaliix@gmail.com>*/

namespace wp;
final class WidgetPost extends Widget
{
    const HIDE_TITLE = 'widgetPostHideTitle';

    function __construct()
    {
        parent::__construct(__('Post'));
    }

    function initFields()
    {
        $this->addField(new WidgetField(WidgetField::CHECKBOX, self::HIDE_TITLE,
            __('Hide Title'), [], false));
        parent::initFields();
    }

    /**
     * Custom comment template
     *
     * @param $comment
     * @param $args
     * @param $depth
     */
    function renderCommentTemplate($comment, $args, $depth)
    {
        $idComment = get_comment_ID();
        $commentAuthorLink = get_comment_author_link($idComment);
        $linkCommentEdit = '';
        if (current_user_can('edit_comment', $idComment)) {
            $textEditThis = __('Edit This');
            $link = esc_url(get_edit_comment_link($comment));
            $link = "<a class='comment-edit-link' href='{$link}'>{$textEditThis}</a>";
            $linkCommentEdit = apply_filters('edit_comment_link', $link, $idComment, $textEditThis);
        }
        $content = '';
        switch ($comment->comment_type) {
            case 'pingback' :
                $textTrackBack = __('Trackback:');
                $content .= "<li class='trackback'><p>{$textTrackBack}{$commentAuthorLink}{$linkCommentEdit}</p></li>";
                break;
            case 'trackback' :
                $textPingBack = __('Pingback:');
                $content .= "<li class='pingback'><p>{$textPingBack}{$commentAuthorLink}{$linkCommentEdit}</p></li>";
                break;
            default :
                $cssComment = comment_class('', $comment, $idComment, false);
                $commentAuthorName = get_comment_author($idComment);
                $commentAuthorImg = get_avatar($comment, 110);
                $textAt = sprintf(__('%1$s at %2$s'), get_comment_date(), get_comment_time());
                $commentLink = get_comment_link($idComment);
                $commentText = apply_filters('comment_text', get_comment_text($comment), $comment, $args);
                $commentReplyLink = get_comment_reply_link(['before' => '', 'depth' => $depth, 'max_depth' => $args['max_depth']]);
                $content .= "<li id='comment-{$idComment}' $cssComment>
                <article id='comment-article-{$idComment}' class='comment-body'>
                    <div class='comment-author vcard'>
                        {$commentAuthorImg}
                        <cite class='fn'>{$commentAuthorName}</cite>
                    </div>
                    <div class='comment-meta commentmetadata'>
                        <a href='{$commentLink}'>{$textAt}</a>
				    </div>
				    <div class='comment-body'>{$commentText} {$linkCommentEdit} {$commentReplyLink}</div>
                </article></li>";
                break;
        }
        echo $content;
    }

    private function getPostCommentContent()
    {
        $content = '';
        if (post_password_required()) {
            $textPasswordProtected = __('This post is password protected. Enter the password to view comments.');
            $content .= "<p class='nopassword'>{$textPasswordProtected}</p>";
        } else if (comments_open()) {
            $htmlCommentsClosed = '';
            if (comments_open() == false && get_comments_number() != '0' &&
                post_type_supports(get_post_type(), 'comments')) {
                $textCommentsClosed = __("Comments are closed.");
                $htmlCommentsClosed = "<p class='nocomments'>{$textCommentsClosed}</p>";
            }
            $htmlCommentsPaging = '';
            if (get_comment_pages_count() > 1 && get_option('page_comments')) {
                $paginateCommentsLinks = paginate_comments_links();
                $htmlCommentsPaging = "<nav class='pagination comments-pagination'>$paginateCommentsLinks</nav>";
            }
            //\WP_Comment_Query::__construct()
            $comments = get_comments(['post_id' => get_the_ID()]);
            $htmlCommentsList = wp_list_comments(['avatar_size' => 64, 'echo' => false], $comments);
//            $htmlCommentsList = wp_list_comments(['callback' => [$this, 'renderCommentTemplate'], 'echo' => false],$comments);
            $textCommentsNumber = get_comments_number_text();
            $content .= "<section id='comments'><h2 id='comments-title'><span>{$textCommentsNumber}</span></h2>
            <ul class='comments-list'>{$htmlCommentsList}</ul>{$htmlCommentsClosed}{$htmlCommentsPaging}</section>";
            ob_start();
            comment_form(['format' => 'html5']);
            $content .= ob_get_clean();
        }
        return "{$content}";
    }

    private function getPostContent()
    {
        $content = '';
        if (have_posts()) {
            while (have_posts()) {
                the_post();
                $pageContent = get_the_content();
                $pageContent = apply_filters('the_content', $pageContent);
                $pageContent = str_replace(']]>', ']]&gt;', $pageContent);
                $pageClass = implode(' ', get_post_class('', get_the_ID()));
                $content .= "<div class='$pageClass'>$pageContent</div>";
                $content .= $this->getPostCommentContent();
            }
        }
        return $content;
    }

    function widget($args, $instance)
    {
        $content = '';
        $customTitle = '';
        $titleAddition = '';
        //TODO Add Tags, Next /Previous Post, Featured Image, Gallery Image, Options to choose what to display
        if (is_archive() || is_tax()) {

        } else if (is_front_page() || is_page()) {
            if ($customTitle == '') {
                $customTitle = get_the_title();
            }
            $content = $this->getPostContent();
        } else if (is_single() && !is_home()) {
            if ($customTitle == '') {
                $customTitle = get_the_title();
            }
            $textPublishDate = UtilsWp::getPostAuthorAndDate(false);
            $textCategoryList = get_the_category_list(', ');
            $textCategoryList = "<span class='text-info'>{$textCategoryList}</span>";
            $textCategory = '';
            if ($textCategoryList) {
                $textCategory = sprintf(__('Category: %s'), $textCategoryList);
            }
            //$titleAddition = "<p><small>{$textCategory} {$textPublishDate}</small></p>";
            $content = $this->getPostContent();
            //previous_post_link(); next_post_link();
        }
        $hideTitle = intval(self::getInstanceValue($instance, self::HIDE_TITLE, $this));
        if (!$hideTitle) {
            $instance[Widget::CUSTOM_TITLE] = $customTitle;
        }
        $args[WPSidebar::AFTER_TITLE_ADDITION] = $titleAddition;
        $args[WPSidebar::CONTENT] = $content;
        parent::widget($args, $instance);
    }
}