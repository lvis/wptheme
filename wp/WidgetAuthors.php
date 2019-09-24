<?php
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/5/18
 * Time: 6:32 PM
 */

namespace wp;

use WP_User;

final class WidgetAuthors extends Widget
{
    const SHOW_POST_AUTHOR = "showPostAuthor";
    const SHOW_CALL_BUTTON = "showCallButton";
    const LAYOUT = "layout";
    const LAYOUT_LIST = "layoutList";
    const LAYOUT_GRID = "layoutGrid";

    function __construct()
    {
        parent::__construct(__('Authors'));
    }

    //Назначенный агент, Assigned agent
    function initFields()
    {
        $this->addField(new WidgetField(WidgetField::CHECKBOX, self::SHOW_POST_AUTHOR,
            __('Show Only Post Author', WpApp::TEXT_DOMAIN), [], false));
        $this->addField(new WidgetField(WidgetField::CHECKBOX, self::SHOW_CALL_BUTTON,
            __('Show Call Button', WpApp::TEXT_DOMAIN), [], false));
        $this->addField(new WidgetField(WidgetField::NUMBER, QueryUsers::NUMBER,
            __('Number of Agents', WpApp::TEXT_DOMAIN), [], 3));
        $field = new WidgetField(WidgetField::SELECT, self::LAYOUT, __('Layout'), [
            self::LAYOUT_LIST => __('List'),
            self::LAYOUT_GRID => __('Grid'),
        ], self::LAYOUT_LIST);
        $this->addField($field);
        parent::initFields();
    }

    /**
     * @var WP_User $author
     *
     * @param string $columns
     *
     * @return string
     */
    function getCardForAuthor(WP_User $author, string $columns = "12")
    {
        $authorName = $author->display_name;
        $authorId = $author->ID;
        $authorPageUrl = get_author_posts_url($authorId);
        /** Post Count */
        //$authorPostCount = count_user_posts($authorId, POST_PROPERTY);
        //$authorPostCount = sprintf(_n('%s property', '%s properties', $authorPostCount, WpApp::TEXT_DOMAIN), $authorPostCount);
        /** Mobile */
        $authorMobile = get_the_author_meta('mobile_number', $authorId);
        if (!empty($authorMobile)) {
            $authorMobile = sprintf('<p><a href="tel:%1$s" title="%2$s" target="_blank"><i class="fa fa-phone"></i> <span>%1$s</span></a></p>',
                esc_html($authorMobile),
                __('Mobile', WpApp::TEXT_DOMAIN));
        }
        /** Skype */
        $skypeUser = get_the_author_meta('skype_name', $authorId);
        if (!empty($skypeUser)) {
            $skypeUser = sprintf('<p><a href="skype:%1$s?add" title="%2$s" target="_blank"><i class="fa fa-skype"></i> <span>%1$s</span></a></p>',
                esc_attr($skypeUser),
                __('Skype', WpApp::TEXT_DOMAIN));
        }
        /** Social */
        $authorSocials = "";
        foreach (['odnoklassniki', 'vkontakte', 'facebook', 'twitter'] as $item) {
            $itemURL = $item . '_url';
            $itemMeta = get_the_author_meta($itemURL, $authorId);
            if (!empty($itemMeta)) {
                if ($item == 'vkontakte') {
                    $item = "vk";
                }
                $authorSocials .= sprintf('<a target="_blank" href="%1$s"><i class="fa fa-%2$s"></i></a>',
                    esc_url($itemMeta), $item);
            }
        }
        /** Email */
        $authorEmail = sprintf('<p><a href="mailto:%1$s" class="nowrap"><i class="fa fa-envelope-o"></i> <span>%1$s</span></a></p>', $author->user_email);
        /** Contact Form */
        $contactForm = "";
        $contactFormInline = "";
        if (is_singular() || (!$skypeUser && !$authorMobile)) {
            ob_start();
            /** Contact Form*/
            $widgetInstance = [
                WidgetContactForms::RECIPIENT => WidgetContactForms::RECIPIENT_AUTHOR,
                WidgetContactForms::FORM_TYPE => WidgetContactForms::DIALOG,
                WidgetContactForms::MODAL_TOGGLE_NAME => __("Write a letter", WpApp::TEXT_DOMAIN),
                WidgetContactForms::MODAL_TOGGLE_ICON => "fa-pencil-square-o",
            ];
            the_widget("WidgetContactForms", $widgetInstance, [
                WPSidebar::BEFORE_WIDGET => '<section id="%s" class="widget">',
                WPSidebar::AFTER_WIDGET => '</section>',
            ]);
            $contactForm = ob_get_clean();
            $authorEmail = "";
        } else if (is_author()) {
            ob_start();
            /** Contact Form*/
            $widgetInstance = [
                WidgetContactForms::RECIPIENT => WidgetContactForms::RECIPIENT_AUTHOR,
                WidgetContactForms::FORM_TYPE => WidgetContactForms::INLINE,
                Widget::CUSTOM_TITLE => __("Write a letter", WpApp::TEXT_DOMAIN),
            ];
            the_widget("WidgetContactForms", $widgetInstance, [
                WPSidebar::BEFORE_WIDGET => '<section id="%s" class="widget">',
                WPSidebar::AFTER_WIDGET => '</section>',
            ]);
            $contactFormInline = ob_get_clean();
        }
        /** Avatar */
        $avatarImageId = get_the_author_meta('profile_image_id', $authorId);
        $avatarImageUrl = wp_get_attachment_url($avatarImageId);
        /*$content = sprintf('<div class="col-sm-%10$s clearfix"><article class="media media-author">
		         <div class="media-left text-center">
		         	<a href="%1$s"><img src="%2$s" class="avatar-72 img-circle" title="%4$s" alt="%4$s" width="72" height="72"></a>
		         	<aside>%3$s</aside>
	            </div>
		         <div class="media-body text-left">
		         <h4 class="media-heading">
		         	<a href="%1$s"><span>%4$s</span></a>
	            </h4>%5$s %6$s %7$s %8$s </div></article>%9$s</div>',
            $authorPageUrl,
            $avatarImageUrl,
            $authorSocials,
            $authorName,
            $authorMobile,
            $skypeUser,
            $authorEmail,
            $contactForm,
            $contactFormInline,
            $columns
        );*/

        return "<div class='col-12 col-sm-6 col-lg-4 col-xl-3 text-xs-center clearfix'>
        <a href='$authorPageUrl'>
            <img src='$avatarImageUrl' title='$authorName' alt='$authorName' class='img-responsive'>
            <h5>$authorName</h5>
        </a>
        $authorSocials $authorMobile $skypeUser $authorEmail $contactForm $contactFormInline
        </div>";
    }

    function widget($args, $instance)
    {
        $content = "";
        $showPostAuthor = self::getInstanceValue($instance, WidgetAuthors::SHOW_POST_AUTHOR, $this);
        if ($showPostAuthor) {
            $currentAuthor = UtilsWp::getCurrentAuthor();
            if ($currentAuthor) {
                $authors = [$currentAuthor];
            } else {
                $authors = [];
            }
        } else {
            //TODO Add Order options to Widget config
            /*$authors = get_users([
                QueryUsers::ROLE_IN => [WPUserRoles::AUTHOR, WPUserRoles::EDITOR],
                QueryUsers::ORDER_BY => 'post_count',
                QueryUsers::ORDER => WPOrder::DESC,
                QueryPost::TYPE => POST_PROPERTY,
                QueryUsers::NUMBER => intval(self::getInstanceValue($instance, QueryUsers::NUMBER, $this)),
            ]);*/
            $authors = get_users([
                QueryUsers::ROLE_IN => [WPUserRoles::AUTHOR, WPUserRoles::EDITOR],
                QueryUsers::NUMBER => intval(self::getInstanceValue($instance, QueryUsers::NUMBER, $this)),
            ]);
        }
        $layout = self::getInstanceValue($instance, self::LAYOUT, $this);
        $columns = 12;
        if ($layout == self::LAYOUT_GRID) {
            $columns = 4;
        }
        foreach ($authors as $author) {
            $content .= $this->getCardForAuthor($author, $columns);
        }
        $showCallButton = self::getInstanceValue($instance, self::SHOW_CALL_BUTTON, $this);
        if ($showCallButton) {
            $authorNumberContent = "";
            /** @var WP_User $author */
            foreach ($authors as $author) {
                $authorId = $author->ID;
                $authorAvatar = get_avatar($author->ID, 48, "", "", ["class" => "img-circle"]);
                $authorMobile = esc_html(get_the_author_meta('mobile_number', $authorId));
                $authorNumberContent .= sprintf('<li><a href="tel:%1$s">%2$s <dl><dt>%3$s</dt><dd>%1$s</dd></dl></a></li>', $authorMobile, $authorAvatar, $author->display_name);
            }
            $content .= sprintf('<div class="dropup WidgetAuthorCall">
    			<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        		<i class="fa fa-phone"></i></button>
    			<ul class="dropdown-menu" aria-labelledby="dropdownMenu2">%s</ul></div>', $authorNumberContent);
        }
        $args[WPSidebar::CONTENT] = $content;
        parent::widget($args, $instance);
    }
}