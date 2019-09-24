<?php
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/5/18
 * Time: 6:43 PM
 */

namespace wp;
/**
 * @url https://codex.wordpress.org/Database_Description#Table:_wp_users
 */
final class WPUser
{
    /**
     * @const
     */
    const ID = 'ID';
    /**
     * @const
     */
    const LOGIN = 'user_login';
    /**
     * @const
     */
    const PASS = 'user_pass';
    /**
     * @const
     */
    const NICK = 'user_nicename';
    /**
     * @const
     */
    const EMAIL = 'user_email';
    /**
     * @const
     */
    const URL = 'user_url';
    /**
     * @const
     */
    const REGISTERED = 'user_registered';
    /**
     * @const
     */
    const ACTIVATION_KEY = 'user_activation_key';
    /**
     * @const
     */
    const STATUS = 'user_status';
    /**
     * @const
     */
    const NAME = 'display_name';

    static function isLoggedAuthorPage()
    {
        return is_user_logged_in() && is_author(get_current_user_id());
    }

    static function isSiteEditor()
    {
        return (current_user_can('editor') || current_user_can('administrator'));
    }
}