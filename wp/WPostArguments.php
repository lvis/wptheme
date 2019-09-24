<?php
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/5/18
 * Time: 6:54 PM
 */

namespace wp;
/**
 * https://codex.wordpress.org/Function_Reference/register_post_type
 */
final class WPostArguments extends WPTaxonomyArguments
{
    /**
     * @const
     */
    const SUPPORTS = 'supports';
    /**
     * @const
     */
    const IS_PUBLIC = 'public';
    /**
     * @const
     */
    const IS_PUBLIC_QUERY = 'publicly_queryable';
    /**
     * @const
     */
    const HAS_ARCHIVE = 'has_archive';
    /**
     * @const
     */
    const CAPABILITY_TYPE = 'capability_type';
    /**
     * @const
     */
    const CAPABILITIES = 'capabilities';
    /**
     * @const
     */
    const MENU_ICON = 'menu_icon';
    /**
     * @const
     */
    const MENU_POSITION = 'menu_position';
    /**
     * @const
     */
    const EXCLUDE_FROM_SEARCH = 'exclude_from_search';
    /**
     * @const Whether post_type is available for selection in navigation menus.
     */
    const SHOW_IN_NAV_MENU = 'show_in_nav_menus';
    /**
     * @const Where to show the post type in the admin menu. show_ui must be true.
     */
    const SHOW_IN_MENU = 'show_in_menu';
    /**
     * @const Whether to make this post type available in the WordPress admin bar.
     */
    const SHOW_IN_ADMIN_BAR = 'show_in_admin_bar';
}