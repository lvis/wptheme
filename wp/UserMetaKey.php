<?php
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/5/18
 * Time: 6:43 PM
 */

namespace wp;
/** @url https://codex.wordpress.org/Database_Description#Table:_wp_users */
class UserMetaKey
{
    const NICKNAME = 'nickname';
    const FIRST_NAME = 'first_name';
    const LAST_NAME = 'last_name';
    const DESCRIPTION = 'description';
    const LOCALE = 'locale';
    const ADMIN_COLOR = 'admin_color';
    const RICH_EDITING = 'rich_editing';
    const COMMENT_SHORTCUTS = 'comment_shortcuts';
    const SYNTAX_HIGHLIGHTING = 'syntax_highlighting';
    const USE_SSL = 'use_ssl';
    const SESSION_TOKENS = 'session_tokens';
    const DISMISSED_WP_POINTERS = 'dismissed_wp_pointers';
    const SHOW_ADMIN_BAR_FRONT = 'show_admin_bar_front';
    const SHOW_WELCOME_PANEL = 'show_welcome_panel';
    const USER_LEVEL = 'mf_user_level';
    const USER_SETTINGS = 'mf_user-settings';
    const USER_SETTINGS_TIME = 'mf_user-settings-time';
    const CAPABILITIES = 'mf_capabilities';
    const MEDIA_LIBRARY_MODE = 'mf_media_library_mode';
    const DASHBOARD_QUICK_PRESS_LAST_POST_ID = 'mf_dashboard_quick_press_last_post_id';
}