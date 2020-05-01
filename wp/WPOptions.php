<?php
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/5/18
 * Time: 6:10 PM
 */

namespace wp;
/**
 * @url https://developer.wordpress.org/reference/functions/get_option/
 */
final class WPOptions
{
    /**
     * @const E-mail address of blog administrator
     */
    const ADMIN_EMAIL = 'admin_email';
    /**
     * @const Weblog title; set in General Options.
     */
    const SITE_NAME = 'blogname';
    /**
     * @const Weblog Logo set in General Options.
     */
    const SITE_LOGO = 'custom-logo';
    /**
     * @const Tagline for your blog; set in General Options.
     */
    const SITE_DESCRIPTION = 'blogdescription';
    /**
     * @const The blog’s home web address; set in General Options.
     */
    const SITE_HOME = 'home';
    /**
     * @const WordPress web address; set in General Options.
     * Warning: This is not the same as get_bloginfo( 'url' ) (which will return the homepage url),
     * but as get_bloginfo( 'wpurl' ).
     */
    const SITE_URL = 'siteurl';
    /**
     * @const Default upload location; set in Miscellaneous Options.
     */
    const SITE_UPLOAD_PATH = 'upload_path';
    /**
     * @const Character encoding for your blog; set in Reading Options.
     */
    const SITE_CHARSET = 'blog_charset';
    /**
     * @const Default date format; set in General Options.
     */
    const DATE_FORMAT = 'date_format';
    /**
     * @const Default post category; set in Writing Options.
     */
    const DEFAULT_CATEGORY = 'default_category';
    /**
     * @const The current theme’s name; set in Presentation.
     */
    const TEMPLATE = 'template';
    /**
     * @const Day of week calendar should start on; set in General Options.
     */
    const WEEK_START = 'start_of_week';
    /**
     * @const Whether users can register; set in General Options.
     */
    const USERS_CAN_REGISTER = 'users_can_register';
    /**
     * @const Maximum number of posts to show on a page; set in Reading Options.
     */
    const POSTS_PER_PAGE = 'posts_per_page';
    /**
     * @const Maximum number of most recent posts to show in the syndication feed; set in Reading Options.
     */
    const POSTS_PER_RSS = 'posts_per_rss';

    static function getSiteName()
    {
        return wp_specialchars_decode(get_option(WPOptions::SITE_NAME), ENT_QUOTES);
    }
}