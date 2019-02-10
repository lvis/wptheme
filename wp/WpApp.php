<?php namespace wp;

use WP_Customize_Manager;
use WP_Customize_Control;

class WpApp
{
    const TEXT_DOMAIN = 'wptheme';
    public $uriToLibs = '/libs/';
    public $uriToVendor = '/vendor/';
    public $uriToNodeModules = '/node_modules/';
    protected static $instance = null;
    public static function i()
    {
        if (!self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
    }
    protected function __construct()
    {
        $this->uriToLibs = get_template_directory_uri() . $this->uriToLibs;
        $this->uriToNodeModules = get_template_directory_uri() . $this->uriToNodeModules;
        $this->uriToVendor = get_template_directory_uri() . $this->uriToVendor;
        Customizer::i();
        UtilsTheme::i();
        UtilsWooCommerce::i();
        add_action(WPActions::THEME_SETUP, [$this, 'setupTheme']);
        add_action(WPActions::ENQUEUE_SCRIPTS_THEME, [$this, 'enqueueScriptsTheme']);
        add_action(WPActions::ENQUEUE_SCRIPTS_ADMIN, [$this, 'enqueueScriptsAdmin']);
        add_action(WPActions::WIDGETS_INIT, [$this, 'initSidebarWidgets']);
        add_filter(WPActions::NAV_MENU_ITEM_LINK_ATTRIBUTES, [$this, 'handleNavMenuItemLinkAttributes'], 10, 4);
        add_action(WPActions::USER_REGISTER, [$this, 'handleUserRegister']);
        if (is_user_logged_in()) {
            add_action(WPActions::ADMIN_BAR_MENU, [$this, 'customizeAdminBar'], 80);
            add_filter(WPActions::SHOW_ADMIN_BAR, [$this, 'handleShowAdminBar']);
        }
        if (WPUtils::isSiteEditor() == false) {
            //Stop Users From Switching Admin Color Schemes
            remove_action('admin_color_scheme_picker', 'admin_color_scheme_picker');
            add_filter('get_user_option_admin_color', function () {
                $color_scheme = 'coffee';
                return $color_scheme;
            }, 5, 0);
        }
    }
    /**
     *  WordPress Setup Theme
     * - Load text domain
     * - Add title tag support
     * - Add custom background support
     * - Add specific post formats support
     * - Add custom menu support and register a custom menu
     * - Register required image sizes
     */
    public function setupTheme()
    {
        /** Load text domain */
        //WPUtils::loadThemeLocale(self::TEXT_DOMAIN);
        /** Custom Logo */
        add_theme_support(WPOptions::SITE_LOGO);
        /** In Customizer add Section to change the Background Color and Image*/
        //add_theme_support('custom-background');
        /** Refresh Widgets Functionality*/
        add_theme_support('customize-selective-refresh-widgets');
        // Post Thumbnails and set size
        add_theme_support('post-thumbnails');
        // Use HTML5 tag for Embedding and Formatting
        //add_theme_support( 'html5', ['comment-list', 'comment-form', 'search-form', 'gallery', 'caption']);

        update_option(WPImages::THUMB_WIDTH, 360);
        update_option(WPImages::THUMB_HEIGHT, 240);
        update_option(WPImages::THUMB_CROP, 1);
        update_option(WPImages::MEDIUM_WIDTH, 0);
        update_option(WPImages::MEDIUM_HEIGHT, 0);
        update_option(WPImages::MEDIUM_LARGE_WIDTH, 0);
        update_option(WPImages::MEDIUM_LARGE_HEIGHT, 0);
        update_option(WPImages::LARGE_WIDTH, 0);
        update_option(WPImages::LARGE_HEIGHT, 0);
        set_post_thumbnail_size(360, 360, true);
//        add_image_size(WPImages::THUMB, 360, 203, true);
        add_image_size(WPImages::THUMB, 360, 240);
        add_image_size(WPImages::MEDIUM, 0, 0);
        add_image_size(WPImages::MEDIUM_LARGE, 0, 0);
        add_image_size(WPImages::LARGE, 0, 0);
        add_image_size(WPImages::FULL, 1920, 1080);
    }
    /**
     * Load Required CSS Styles and Javascript Files
     * Docs: https://developer.wordpress.org/themes/basics/including-css-javascript/
     */
    function enqueueScriptsTheme()
    {
        wp_enqueue_style('fa', $this->uriToVendor . 'lvis/wplib/src/libs/font-awesome/css/all.css');
        wp_enqueue_style('bs-utils', $this->uriToLibs . 'bs-utils.css');
        wp_enqueue_style('bs3-grid', $this->uriToLibs . 'bs3-grid.css');
        wp_enqueue_style('tab', $this->uriToLibs . 'tab.css');
        wp_enqueue_style('input', $this->uriToLibs . 'input.css');
        wp_enqueue_style('radio', $this->uriToLibs . 'radio.css');
        wp_enqueue_style('modal', $this->uriToLibs . 'modal.css');
        wp_enqueue_style('checkbox', $this->uriToLibs . 'checkbox.css');
        wp_enqueue_style('button', $this->uriToLibs . 'button.css');
        wp_enqueue_style('button-shop', $this->uriToLibs . 'button-shop.css');
        wp_enqueue_style('card', $this->uriToLibs . 'card.css');
        wp_enqueue_style('mkit', $this->uriToLibs . 'mkit.css');
        wp_enqueue_style('fixes', $this->uriToLibs . 'fixes.css');
        wp_enqueue_style('menu', $this->uriToLibs . 'menu.css');
        wp_enqueue_script('menu', $this->uriToLibs . 'menu.js', [], false, true);
        wp_deregister_script('jquery-migrate');
        wp_deregister_script('jquery');
        wp_enqueue_script('jquery', includes_url('/js/jquery/jquery.js'), false, null, true);
    }
    /** Load Styles & Scripts for: Backend*/
    function enqueueScriptsAdmin()
    {
        wp_enqueue_style('wptheme-admin', $this->uriToLibs . 'admin.css');
        wp_enqueue_style('wptheme-adminbar', $this->uriToLibs . 'adminbar.css');
        $screen = get_current_screen();
        if ($screen->id == 'profile' && WPUser::isSiteEditor() == false) {
            wp_enqueue_style('author', $this->uriToLibs . 'author.css');
        }
        $navMenuScriptName = 'nav-menu';
        wp_script_is($navMenuScriptName);
        // Limit max menu depth in admin panel. Override default value right after 'nav-menu' JS
        wp_add_inline_script($navMenuScriptName, 'wpNavMenu.options.globalMaxDepth = 1;', 'after');
    }
    function initSidebarWidgets()
    {
        Widget::i();
        register_widget(WidgetSiteBranding::class);
        register_widget(WidgetPosts::class);
        register_widget(WidgetPost::class);
        //register_widget(WidgetSlider::class);
        //WP
        unregister_widget(\WP_Widget_Archives::class);
        unregister_widget(\WP_Widget_Calendar::class);
        unregister_widget(\WP_Widget_Meta::class);
        unregister_widget(\WP_Widget_Recent_Comments::class);
        unregister_widget(\WP_Widget_Recent_Posts::class);
        unregister_widget(\WP_Widget_RSS::class);
        unregister_widget(\WP_Widget_Tag_Cloud::class);
        unregister_widget(\WP_Widget_Text::class);
        unregister_widget(\WP_Widget_Pages::class);
        unregister_widget(\WP_Widget_Media_Image::class);
        unregister_widget(\WP_Widget_Media_Gallery::class);
        unregister_widget(\WP_Widget_Media_Audio::class);
        unregister_widget(\WP_Widget_Media_Video::class);
        unregister_widget(\WP_Widget_Categories::class);
        unregister_widget(\WP_Widget_Search::class);
        /** Sidebars, Footer and other Widget areas */
        WPUtils::registerSidebarWidget(WidgetArea::HEADER_TOP, __('Header Top', 'wptheme'));
        WPUtils::registerSidebarWidget(WidgetArea::HEADER_MAIN, __('Header Main', 'wptheme'));
        WPUtils::registerSidebarWidget(WidgetArea::HEADER_BOTTOM, __('Header Bottom', 'wptheme'));
        WPUtils::registerSidebarWidget(WidgetArea::CONTENT_TOP, __('Content Top', 'wptheme'));
        WPUtils::registerSidebarWidget(WidgetArea::CONTENT_MAIN, __('Content Main', 'wptheme'));
        WPUtils::registerSidebarWidget(WidgetArea::CONTENT_BOTTOM, __('Content Bottom', 'wptheme'));
        WPUtils::registerSidebarWidget(WidgetArea::FOOTER_TOP, __('Footer Top', 'wptheme'));
        WPUtils::registerSidebarWidget(WidgetArea::FOOTER_BOTTOM, __('Footer Bottom', 'wptheme'));
    }
    /**
     * Filters the HTML attributes applied to a menu item's anchor element.
     * @param array $attributes The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
     * @param \WP_Post $item The current menu item.
     * @param \stdClass $args An object of wp_nav_menu() arguments.
     * @param int $depth Depth of menu item. Used for padding.
     * @return array
     */
    function handleNavMenuItemLinkAttributes(array $attributes, \WP_Post $item, \stdClass $args, int $depth)
    {
        if ($depth == 0) {
            $attributes['tabindex'] = (string)$item->menu_order;
        }
        return $attributes;
    }
    function handleUserRegister($idUser)
    {
        wp_update_user(['ID' => $idUser, 'admin_color' => 'coffee']);
    }
    /**
     * Customize WordPress AdminBar Menu Items
     * @param \WP_Admin_Bar $wpAdminBar
     */
    function customizeAdminBar(\WP_Admin_Bar $wpAdminBar)
    {
        $nodeMenuCustomize = $wpAdminBar->get_node('customize');
        $wpAdminBar->remove_node('customize');
        $wpAdminBar->add_node((array)$nodeMenuCustomize);
    }
    function handleShowAdminBar(bool $show)
    {
        wp_enqueue_style('admin', $this->uriToLibs . 'admin.css');
        wp_enqueue_style('adminbar', $this->uriToLibs . 'adminbar.css');
        return $show;
    }
}