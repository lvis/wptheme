<?php namespace wp;
/** Author: Vitali Lupu <vitaliix@gmail.com> */
final class WPActions {
    /**
     * @const Filters the full array of plugins to list in the Plugins list table.
     * @url https://developer.wordpress.org/reference/hooks/all_plugins/
     */
    const ALL_PLUGINS = 'all_plugins';
    /**
     * @const Filters the title attribute of the header logo above login form.
     * @url https://developer.wordpress.org/reference/hooks/login_headertitle/
     */
    const ADMIN_LOGIN_TITLE = 'login_headertitle';
    const ADMIN_LOGIN_TEXT = 'login_headertext';
    /**
     * @const Fires as an admin screen or script is being initialized.
     * @url https://developer.wordpress.org/reference/hooks/admin_init/
     */
    const ADMIN_INIT = 'admin_init';
    /**
     * @const Fire the wp_footer action.
     * @url https://developer.wordpress.org/reference/functions/wp_footer/
     */
    const FOOTER = 'wp_footer';
    /**
     * @const Fires in head section for all admin pages.
     * @url https://developer.wordpress.org/reference/hooks/admin_head/
     */
    const ADMIN_HEAD = 'admin_head';
    /**
     * @const Fires before the administration menu loads in the admin.
     * @url https://developer.wordpress.org/reference/hooks/admin_head/
     */
    const ADMIN_MENU = 'admin_menu';
    /**
     * @const Fires before the admin bar is rendered.
     * @url https://developer.wordpress.org/reference/hooks/wp_before_admin_bar_render/
     */
    const ADMIN_BAR_MENU_RENDER_BEFORE = 'wp_before_admin_bar_render';
    /**
     * @const Load all necessary admin bar items.
     * @url https://developer.wordpress.org/reference/hooks/admin_bar_menu/
     */
    const ADMIN_BAR_MENU = 'admin_bar_menu';
    const SHOW_ADMIN_BAR = 'show_admin_bar';
    /**
     * @const
     */
    const ADMIN_LOGIN_URL = 'login_headerurl';
    const PERSONAL_OPTIONS = 'personal_options';
    const PROFILE_PERSONAL_OPTIONS = 'profile_personal_options';
    const PERSONAL_OPTIONS_UPDATE = 'personal_options_update';
    const USER_REGISTER = 'user_register';
    const USER_CONTACT_METHODS = 'user_contactmethods';
    /**
     * @const Fires for the URL of user’s profile editor.
     * @url https://developer.wordpress.org/reference/hooks/edit_profile_url/
     */
    const USER_EDIT_PROFILE_URL = 'edit_profile_url';
    const EDIT_USER_PROFILE_UPDATE = 'edit_user_profile_update';
    const ENQUEUE_SCRIPTS_ADMIN_LOGIN = 'login_enqueue_scripts';
    /**
     * @const Fires before determining which template to load.
     * @url https://codex.wordpress.org/Plugin_API/Action_Reference/template_redirect
     */
    const TEMPLATE_REDIRECT = 'template_redirect';
    /**
     * @const Initialise Theme
     */
    const THEME_SETUP = 'after_setup_theme';
    /**
     * @const Decorates a menu item object with the shared navigation menu item properties.
     * @url https://developer.wordpress.org/reference/functions/wp_setup_nav_menu_item/
     */
    const WP_SETUP_NAV_MENU_ITEM = 'wp_setup_nav_menu_item';
    /**
     * @const Filter the Walker class used when adding nav menu items.
     * @url https://developer.wordpress.org/reference/hooks/wp_edit_nav_menu_walker-2/
     */
    const WP_EDIT_NAV_MENU_WALKER = 'wp_edit_nav_menu_walker';
    /**
     * @const Fires after a navigation menu item has been updated.
     * @url https://developer.wordpress.org/reference/hooks/wp_update_nav_menu_item/
     */
    const WP_UPDATE_NAV_MENU_ITEM = 'wp_update_nav_menu_item';
    /**
     * @const Filters the arguments for a single nav menu item.
     * @url https://developer.wordpress.org/reference/hooks/nav_menu_item_args/
     */
    const NAV_MENU_ITEM_ARGS = 'nav_menu_item_args';
    /**
     * @const Filters the HTML list content for navigation menus.
     * @url https://developer.wordpress.org/reference/hooks/wp_nav_menu_items/
     */
    const NAV_MENU_ITEMS = 'wp_nav_menu_items';
    /**
     * @const Filters the HTML attributes applied to a menu item's anchor element.
     * @url https://developer.wordpress.org/reference/hooks/nav_menu_link_attributes/
     */
    const NAV_MENU_ITEM_LINK_ATTRIBUTES = 'nav_menu_link_attributes';
    /**
     * @const Load assets for: Frontend
     */
    const ENQUEUE_SCRIPTS_THEME = 'wp_enqueue_scripts';
    /**
     * @const Load assets for: Backend Login
     */
    const ENQUEUE_SCRIPTS_LOGIN = 'login_enqueue_scripts';
    /**
     * @const Load assets for: Backend
     */
    const ENQUEUE_SCRIPTS_ADMIN = 'admin_enqueue_scripts';
    /**
     * @const Load assets for: Customizer
     * @url https://codex.wordpress.org/Plugin_API/Action_Reference/customize_controls_enqueue_scripts
     */
    const ENQUEUE_SCRIPTS_CUSTOMIZER = 'customize_controls_enqueue_scripts';
    /**
     * @const Before Delete Post
     */
    const BEFORE_DELETE_POST = 'before_delete_post';
    /**
     * @const Before Query User Table
     */
    const PRE_USER_QUERY = 'pre_user_query';
    /**
     * @const Fires after all default WordPress widgets have been registered.
     */
    const WIDGETS_INIT = 'widgets_init';
    /**
     * Filters the settings for a particular widget instance.
     * @see https://developer.wordpress.org/reference/hooks/widget_display_callback/
     * @const
     */
    const WIDGET_DISPLAY = 'widget_display_callback';
    /**
     * @const
     */
    const WIDGET_UPDATE = 'widget_update_callback';
    /**
     * @const Before Display Widget Form
     */
    const WIDGET_FORM_BEFORE = 'widget_form_callback';
    /**
     * @const After Display Widget Form
     */
    const WIDGET_FORM_AFTER = 'in_widget_form';
    /**
     * @const Filters the list of sidebars and their widgets.
     * @url https://developer.wordpress.org/reference/hooks/sidebars_widgets/
     */
    const WIDGETS_IN_SIDEBARS = 'sidebars_widgets';
    /**
     * @const Fires after WordPress has finished loading but before any headers are sent. Use to register Post and Taxonomy
     * @url https://developer.wordpress.org/reference/hooks/init/
     */
    const INIT = 'init';
    /**
     * @const This hook is fired once WP, all plugins, and the theme are fully loaded and instantiated.
     * @url https://developer.wordpress.org/reference/hooks/wp_loaded/
     */
    const LOADED = 'wp_loaded';
    /**
     * @const Filters text with its translation.
     * @url https://developer.wordpress.org/reference/hooks/gettext/
     */
    const GET_TEXT = 'gettext';
    const GET_TEXT_WITH_CONTEXT = 'gettext_with_context';
    const LOGOUT = 'wp_logout';
    /**
     * @const Initialise Customizer
     * @url https://codex.wordpress.org/Plugin_API/Action_Reference/customize_preview_init
     */
    const CUSTOMIZER_INIT = 'customize_preview_init';
    /**
     * @const
     */
    const CUSTOMIZER_REGISTER = 'customize_register';
    /**
     * @const
     */
    const CUSTOMIZER_AFTER_SAVE = "customize_save_after";
    /**
     * @const Function is triggered when is called by wp_head() placed between <head></head> section
     */
    const WP_HEAD = 'wp_head';
    /**
     * @const Fires in each custom column in the Posts list table.
     * @url https://developer.wordpress.org/reference/hooks/manage_posts_custom_column-8/
     */
    const MANAGE_POSTS_CUSTOM_COLUMN = 'manage_posts_custom_column';
    /**
     * @const
     */
    const MANAGE_PAGES_CUSTOM_COLUMN = 'manage_pages_custom_column';
    /**
     * @const
     */
    const SAVE_POST = 'save_post';
    /**
     * @const
     */
    const RESTRICT_MANAGE_POSTS = 'restrict_manage_posts';
    const AJAX_BACKEND = "ajaxBackend";
    const AJAX_FRONTEND = "ajaxFrontend";
    const AJAX_BOTH = "ajaxBoth";

    /**
     * @param callable $callback    Server Side function handler for the ajax request
     * @param string   $restriction Set default is WPActions::AJAX_FRONTEND if you whant to handle ajax request only for logged in users
     *
     * @return true Will return true if handler was added false if not.
     * @internal param callable $function_to_add The name of the function you wish to be called.
     */
    static function addAjaxHandler(callable $callback, $restriction = WPActions::AJAX_FRONTEND) {
        $name = "";
        $isHandlerAdded = false;
        if (is_callable($callback, false, $name)) {
            if (is_array($callback) && count($callback) == 2) {
                $name = $callback[1];
            }
            if ($restriction == WPActions::AJAX_BOTH) {
                $isHandlerAdded = add_action("wp_ajax_$name", $callback) && add_action("wp_ajax_nopriv_$name",
                                                                                       $callback);
            } else {
                if ($restriction == WPActions::AJAX_BACKEND) {
                    $isHandlerAdded = add_action("wp_ajax_$name", $callback);
                } else {
                    if ($restriction == WPActions::AJAX_FRONTEND) {
                        $isHandlerAdded = add_action("wp_ajax_nopriv_$name", $callback);
                    }
                }
            }
        }
        return $isHandlerAdded;
    }
}