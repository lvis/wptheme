<?php namespace wp;
/** Author: Vitali Lupu vitaliix@gmail.com*/

use Elementor\Plugin;

class WpApp {
    const TEXT_DOMAIN = 'wptheme';
    public $uriToLibs = '/libs/';
    public $uriToVendor = '/vendor/';
    public $uriToNodeModules = '/node_modules/';
    protected static $instance = null;

    public static function i() {
        if (!self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
    }


    protected function __construct() {
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
        add_action('elementor/widgets/widgets_registered', [$this, 'handleWidgetsRegistered']);
        add_action(WPActions::USER_REGISTER, [$this, 'handleUserRegister']);
        if (is_user_logged_in()) {
            add_action(WPActions::ADMIN_BAR_MENU, [$this, 'customizeAdminBar'], 80);
            add_filter(WPActions::SHOW_ADMIN_BAR, [$this, 'handleShowAdminBar']);
        }
        if (UtilsWp::isSiteEditor() == false) {
            //Stop Users From Switching Admin Color Schemes
            remove_action('admin_color_scheme_picker', 'admin_color_scheme_picker');
            add_filter('get_user_option_admin_color', function () {
                $color_scheme = 'coffee';
                return $color_scheme;
            }, 5, 0);
        }
        //Temporary solution to show in builder in Display Conditions view users without posts
        add_action('pre_get_users', [$this, 'handlePreGetUsers']);
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
    public function setupTheme() {
        // Load text domain
         UtilsWp::loadThemeLocale(WpApp::TEXT_DOMAIN);
        // Let WordPress manage the document title. By adding theme support, we declare that this theme does not use a hard-coded <title> tag in the document head, and expect WordPress to provide it for us.
        add_theme_support('title-tag');
        // Custom Logo
        add_theme_support(WPOptions::SITE_LOGO);
        // In Customizer add Section to change the Background Color and Image*
        // add_theme_support('custom-background');
        // Refresh Widgets Functionality
        add_theme_support('customize-selective-refresh-widgets');
        // Post Thumbnails and set size
        add_theme_support('post-thumbnails');
        // Use HTML5 tag for Embedding and Formatting
        //add_theme_support( 'html5', ['comment-list', 'comment-form', 'search-form', 'gallery', 'caption']);
        register_nav_menu('Main', 'Default menu for the site');
    }

    /**
     * Load Required CSS Styles and Javascript Files
     * Docs: https://developer.wordpress.org/themes/basics/including-css-javascript/
     */
    function enqueueScriptsTheme() {
        wp_enqueue_style('general', $this->uriToLibs . 'general.css');
        wp_enqueue_style('bs-utils', $this->uriToLibs . 'bs-utils.css');
        wp_enqueue_style('bs3-grid', $this->uriToLibs . 'bs3-grid.css');
        wp_enqueue_style('card', $this->uriToLibs . 'card.css');
        wp_enqueue_style('fixes', $this->uriToLibs . 'fixes.css');
        wp_enqueue_style('menu', $this->uriToLibs . 'menu.css');
        wp_enqueue_style('fa-all', $this->uriToLibs."fonts/font-awesome/css/all.css");
        wp_enqueue_script('menu', $this->uriToLibs . 'menu.js', [], false, true);
        wp_deregister_script('jquery-migrate');
        wp_deregister_script('jquery');
        wp_enqueue_script('jquery', includes_url('/js/jquery/jquery.js'), false, null, true);
        wp_enqueue_script('knockout', $this->uriToLibs.'knockout.js', [], false, true);
    }

    /** Load Styles & Scripts for: Backend*/
    function enqueueScriptsAdmin() {
        wp_enqueue_style('wptheme-admin', $this->uriToLibs . 'admin.css');
        wp_enqueue_style('wptheme-adminbar', $this->uriToLibs . 'adminbar.css');
        if (UtilsWp::getCurrentScreenId() == 'profile' && WPUser::isSiteEditor() == false) {
            wp_enqueue_style('author', $this->uriToLibs . 'author.css');
        }
        $navMenuScriptName = 'nav-menu';
        wp_script_is($navMenuScriptName);
        // Limit max menu depth in admin panel. Override default value right after 'nav-menu' JS
        wp_add_inline_script($navMenuScriptName, 'wpNavMenu.options.globalMaxDepth = 1;', 'after');
        wp_enqueue_script('knockout', $this->uriToLibs.'knockout.js', [], false, true);
    }

    function handleWidgetsRegistered() {
        // We check if the Elementor plugin has been installed / activated.
        if (defined('ELEMENTOR_PATH') && class_exists('Elementor\Widget_Base')) {
            // We look for any theme overrides for this custom Elementor element.
            // If no theme overrides are found we use the default one in this plugin.
            Plugin::instance()->widgets_manager->register_widget_type(new BuilderPosts());
        }
    }

    function initSidebarWidgets() {
        Widget::i();
        //Default Widgets used for most of projects
        register_widget(WidgetSiteBranding::class);
        register_widget(WidgetPosts::class);
        register_widget(WidgetPost::class);
        // Sidebars, Footer and other Widget areas
        UtilsWp::registerSidebarWidget(WidgetArea::HEADER_TOP, __('Header Top', WpApp::TEXT_DOMAIN));
        UtilsWp::registerSidebarWidget(WidgetArea::HEADER_MAIN, __('Header Main', WpApp::TEXT_DOMAIN));
        UtilsWp::registerSidebarWidget(WidgetArea::HEADER_BOTTOM, __('Header Bottom', WpApp::TEXT_DOMAIN));
        UtilsWp::registerSidebarWidget(WidgetArea::CONTENT_TOP, __('Content Top', WpApp::TEXT_DOMAIN));
        UtilsWp::registerSidebarWidget(WidgetArea::CONTENT_MAIN, __('Content Main', WpApp::TEXT_DOMAIN));
        UtilsWp::registerSidebarWidget(WidgetArea::CONTENT_BOTTOM, __('Content Bottom', WpApp::TEXT_DOMAIN));
        UtilsWp::registerSidebarWidget(WidgetArea::FOOTER_TOP, __('Footer Top', WpApp::TEXT_DOMAIN));
        UtilsWp::registerSidebarWidget(WidgetArea::FOOTER_BOTTOM, __('Footer Bottom', WpApp::TEXT_DOMAIN));
        //register_widget(WidgetSlider::class);
        //Unregister not used WP Widgets
        unregister_widget(\WP_Widget_RSS::class);
        unregister_widget(\WP_Widget_Meta::class);
        unregister_widget(\WP_Widget_Archives::class);
        unregister_widget(\WP_Widget_Calendar::class);
        unregister_widget(\WP_Widget_Search::class);
        unregister_widget(\WP_Widget_Pages::class);
        unregister_widget(\WP_Widget_Text::class);
        unregister_widget(\WP_Widget_Media_Image::class);
        unregister_widget(\WP_Widget_Media_Gallery::class);
        unregister_widget(\WP_Widget_Media_Audio::class);
        unregister_widget(\WP_Widget_Media_Video::class);
    }

    function handleUserRegister($idUser) {
        wp_update_user(['ID' => $idUser, 'admin_color' => 'coffee']);
    }

    /**
     * Customize WordPress AdminBar Menu Items
     *
     * @param \WP_Admin_Bar $wpAdminBar
     */
    function customizeAdminBar(\WP_Admin_Bar $wpAdminBar) {
        $nodeMenuCustomize = $wpAdminBar->get_node('customize');
        $wpAdminBar->remove_node('customize');
        $wpAdminBar->add_node((array)$nodeMenuCustomize);
    }

    function handleShowAdminBar(bool $show) {
        wp_enqueue_style('admin', $this->uriToLibs . 'admin.css');
        wp_enqueue_style('adminbar', $this->uriToLibs . 'adminbar.css');
        return $show;
    }

    function handlePreGetUsers(\WP_User_Query $query) {
        if ($query->query_vars['who'] == 'authors') {
            $query->query_vars['has_published_posts'] = false;
        }
        return $query;
    }
}