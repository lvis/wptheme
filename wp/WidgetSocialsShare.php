<?php
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/5/18
 * Time: 6:32 PM
 */

namespace wp;
final class WidgetSocialsShare extends Widget
{
    const SOCIAL_NETWORKS = "socialNetworks";
    const FACEBOOK = "facebook";
    const TWITTER = "twitter";
    const ODNOKLASSNIKI = "odnoklassniki";
    const VK = "vk";
    const INSTAGRAM = "instagram";
    const GOOGLE_PLUS = "google-plus";
    const PINTEREST = "pinterest";

    private $linksToSocialNetworks = [
        self::FACEBOOK => "https://www.facebook.com/sharer/sharer.php?u=",
        self::TWITTER => "https://twitter.com/share?url=",
        self::ODNOKLASSNIKI => "https://connect.ok.ru/dk?cmd=WidgetSharePreview&st.cmd=WidgetSharePreview&st.client_id=-1&st.shareUrl=",
        self::VK => "http://vkontakte.ru/share.php?&url=",
        self::GOOGLE_PLUS => "https://plus.google.com/share?url=",
    ];

    function __construct()
    {
        parent::__construct(__('Share on Social Networks', WpApp::TEXT_DOMAIN));
    }

    function initFields()
    {
        $this->addField(new WidgetField(WidgetField::SELECT_MULTIPLE, self::SOCIAL_NETWORKS,
            __("Social Networks", WpApp::TEXT_DOMAIN), [
                self::FACEBOOK => __('Facebook', WpApp::TEXT_DOMAIN),
                self::TWITTER => __('Twitter', WpApp::TEXT_DOMAIN),
                self::ODNOKLASSNIKI => __('Odnoklassniki', WpApp::TEXT_DOMAIN),
                self::VK => __('VKontakte', WpApp::TEXT_DOMAIN),
                self::GOOGLE_PLUS => __('Google Plus', WpApp::TEXT_DOMAIN),
            ]));
        parent::initFields();
    }

    function widget($args, $instance)
    {
        $content = "";
        $socialNetworks = self::getInstanceValue($instance, self::SOCIAL_NETWORKS, $this);
        $postId = get_the_ID();
        $linkToPost = home_url("/?p=$postId");
        get_permalink();
        if (is_array($socialNetworks)) {
            foreach ($socialNetworks as $key) {
                $linkToNetwork = $this->linksToSocialNetworks[$key];
                $content .= sprintf('<a target="_blank" href="%s%s" class="btn btn-link"><i class="fa fa-%s fa-lg"></i></a>',
                    $linkToNetwork, $linkToPost, $key);
            }
        }
        $args[WPSidebar::CONTENT] = $content;
        parent::widget($args, $instance);
    }
}