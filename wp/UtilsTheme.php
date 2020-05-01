<?php /** Author: Vitali Lupu <vitaliix@gmail.com> */

namespace wp;

use DOMDocument;
use DOMElement;
use stdClass;
use WP_Admin_Bar;
use WP_Post;
use WP_Query;
use WP_Screen;

final class UtilsTheme
{
    const DEFAULT_AVATAR = "data:image/jpeg;base64,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";
    const OPTION_ADMIN_COLOR_STYLES = "adminColorStyles";
    const OPTION_ADMIN_COLOR = 'fresh';//'coffee'
    const META_AVATAR = "avatar";
    const META_PHONE = 'phone';

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
        //Set the content width based on the theme styles.css. MetaBox: can break UI in the admin without this parameters
        global $content_width;
        if (empty($content_width)) {
            $content_width = 1200;
        }
        update_option('uploads_use_yearmonth_folders', false);
        //Add: Remove handler fo attached Images from Deleted Post
        add_action(WPActions::BEFORE_DELETE_POST, [UtilsWp::class, 'deletePostAttachments']);
        //Add: Random OrderBy Support
        add_action(WPActions::PRE_USER_QUERY, [$this, 'handleUserQuery']);
        //Add: Mail From Name the Site Name
        add_filter('wp_mail_from_name', [WPOptions::class, "getSiteName"]);
        //-------------------------------[BACKEND]
        // [LOGIN]
        if (version_compare($GLOBALS['wp_version'], '5.2', '<')) {
            add_filter(WPActions::ADMIN_LOGIN_TITLE, [$this, 'getLoginLogoTitle']);
        } else {
            add_filter(WPActions::ADMIN_LOGIN_TEXT, [$this, 'getLoginLogoTitle']);
        }
        add_filter(WPActions::ADMIN_LOGIN_URL, [$this, 'getLoginLogoUrl']);
        //add_action(WPActions::ENQUEUE_SCRIPTS_ADMIN_LOGIN, [$this, 'handleScriptsAdminLoginLogo']);
        // [USER RELATED]
        add_filter('pre_get_avatar_data', [$this, 'getAvatarData'], 1, 2);
        //TODO Find a better way to replace src because of esc_url( $url ) in pluggable.php > get_avatar function
        add_filter('get_avatar', [$this, 'getAvatar'], 1);
        if (is_admin()) {
            $this->addHandlersForBackend();
        } else {
            $this->addHandlersForFrontend();
        }

    }

    function addHandlersForBackend()
    {
        //SVG
        add_filter('upload_mimes', [$this, 'handleUploadMimeTypes']);
        //Notifications
        //add_action( 'after_setup_theme', 'disableCoreUpdatesNotifications'); //Disable: WP CoreUpdates Notifications
        add_action(WPActions::ADMIN_INIT, [$this, 'handleAdminInit']);
        add_filter('contextual_help', [$this, 'handleContextualHelp'], 999, 3);
        //User
        add_filter(WPActions::USER_CONTACT_METHODS, [$this, 'addContactFields'], 0, 1);
        add_action(WPActions::ENQUEUE_SCRIPTS_ADMIN, [$this, 'enqueueScriptsAdmin']);
        add_action('enqueue_block_editor_assets', [$this, 'enqueueBlockEditorAssets']);
        add_action(WPActions::PERSONAL_OPTIONS, [$this, 'renderAfterPersonalOptions']);
        add_action(WPActions::PERSONAL_OPTIONS_UPDATE, [$this, 'handlePersonalOptionsUpdate']);
        add_action(WPActions::EDIT_USER_PROFILE_UPDATE, [$this, 'handlePersonalOptionsUpdate']);
        //add_action(WPActions::USER_REGISTER, [$this, "handleUserRegister"], 20);
        //add_filter(WPActions::USER_EDIT_PROFILE_URL, [$this, 'handleEditProfileUrl'], 10, 2);
        //add_action(WPActions::INIT, [$this, 'preventUserAdminBarUsage'], 9);
        //add_action(WPActions::ADMIN_INIT, [$this, 'preventUserAdminAccess']);
        if (UtilsWp::isSiteEditor() == false) {
            //Show Only User related Posts
            add_action('pre_get_posts', [$this, 'showOnlyRelatedPosts']);
        }
        //Admin Color Schemes: Stop Users From Switching
        remove_action('admin_color_scheme_picker', 'admin_color_scheme_picker');
        //Admin Color Schemes: Set Default
        add_filter('get_user_option_admin_color', [$this, 'handleGetUserAdminColor'], 5, 0);
        // [LOGOUT]
        //add_action(WPActions::LOGOUT, [UtilsTheme::class, 'handleLogout']);
    }
    function enqueueBlockEditorAssets(){
        $script = 'document.addEventListener("DOMContentLoaded", function(){
        const isFullscreenMode = wp.data.select("core/edit-post").isFeatureActive("fullscreenMode");
        if (isFullscreenMode) { wp.data.dispatch("core/edit-post").toggleFeature("fullscreenMode"); }
        const isActiveWelcomeGuide = wp.data.select("core/edit-post").isFeatureActive("welcomeGuide");
        if (isActiveWelcomeGuide) { wp.data.dispatch("core/edit-post").toggleFeature("welcomeGuide"); } });';
        wp_add_inline_script('wp-data', $script);
    }
    function handleGetUserAdminColor()
    {
        return self::OPTION_ADMIN_COLOR;
    }

    function addHandlersForFrontend()
    {
        //Admin Menu Bar
        if (is_user_logged_in()) {
            add_action(WPActions::ADMIN_BAR_MENU_RENDER_BEFORE, [$this, 'handleAdminBarRenderBefore']);
            add_action(WPActions::ENQUEUE_SCRIPTS_THEME, [$this, 'enqueueScriptsTheme']);
            add_action(WPActions::ADMIN_BAR_MENU, [$this, 'handleAdminBarMenu'], 999);
        }
        //https://bhoover.com/remove-unnecessary-code-from-your-wordpress-blog-header/
        // Add: OpenGraph Meta to Header
        add_action(WPActions::WP_HEAD, [$this, 'handleWpHead'], 5);
        // Replace Home MenuItem with LOGO markup
        //add_filter(WPActions::NAV_MENU_ITEM_LINK_ATTRIBUTES, [$this, 'handleNavMenuItemLinkAttributes'], 10, 4);
        // Remove: oEmbed Discovery Links
        remove_action(WPActions::WP_HEAD, WPActionsHandlers::ADD_LINKS_OF_oEMBED, 10);
        // Remove: Blog Version
        remove_action(WPActions::WP_HEAD, WPActionsHandlers::ADD_GENERATOR);
        // Remove: REST API Link Tag
        remove_action(WPActions::WP_HEAD, WPActionsHandlers::ADD_LINK_OF_REST, 10);
        // Remove: REST API Link in HTTP headers
        remove_action(WPActions::TEMPLATE_REDIRECT, WPActionsHandlers::ADD_LINK_OF_REST_HEADER, 11);
        //Remove: WordPress Generator (with version information)
        remove_action(WPActions::WP_HEAD, 'wp_generator');
        // Remove: Canonical Link
        remove_action(WPActions::WP_HEAD, 'rel_canonical');
        //Remove: RSD Link - Weblog Client Link
        remove_action(WPActions::WP_HEAD, 'rsd_link');
        //Remove: Windows Live Writer Manifest Link
        remove_action(WPActions::WP_HEAD, 'wlwmanifest_link');
        // Remove: Short Link
        remove_action(WPActions::WP_HEAD, 'wp_shortlink_wp_head');
        add_filter('the_generator', '__return_false');
        //Disable: XMLRPC
        add_filter('xmlrpc_enabled', '__return_false');
        // Remove: WP Version From Styles
        add_filter('style_loader_src', [$this, 'removeWpVersion'], 9999);
        // Remove: WP Version From Scripts
        add_filter('script_loader_src', [$this, 'removeWpVersion'], 9999);
        // DISABLE: Emoticons
        $this->disableEmotionIcons();
        //add_filter('rest_url', '__return_false');
        //Disable: Image src set Calculation
        add_filter('wp_calculate_image_srcset', '__return_false');
        //add_filter('show_admin_bar','__return_false');
        // [FORM: COMMENT]
        add_filter('comment_form_fields', [$this, 'handleCommentFormFields']);
    }

    function handleContextualHelp($old_help, $screen_id, WP_Screen $screen)
    {
        $screen->remove_help_tabs();
        return $old_help;
    }

    /**
     * Filters the HTML attributes applied to a menu item's anchor element.
     *
     * @param array $attributes The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
     * @param WP_Post $item The current menu item.
     * @param stdClass $args An object of wp_nav_menu() arguments.
     * @param int $depth Depth of menu item. Used for padding.
     *
     * @return array
     */
    function handleNavMenuItemLinkAttributes($attributes, $item, $args, $depth)
    {
        if ($depth == 0) {
            $attributes['tabindex'] = (string)$item->menu_order;
        }
        $siteHomeUrl = home_url('');
        if (isset($attributes['href']) && $attributes['href'][0] == '#') {
            $attributes['href'] = $siteHomeUrl . '/' . $attributes['href'];
        }
        $siteLogoId = get_theme_mod('custom_logo');
        if ($args->theme_location == '' && $depth == 0 && $siteLogoId && $attributes['href'] == "{$siteHomeUrl}/") {
            $image = wp_get_attachment_image_src($siteLogoId, WPImages::FULL);
            $item->title = '';
            $siteName = get_bloginfo('name', 'display');
            $cssSiteLogo = WPOptions::SITE_LOGO;
            $attributes['class'] = "{$cssSiteLogo}-link";
            if ($image) {
                list($src, $width, $height) = $image;
                $hwData = image_hwstring($width, $height);
                $item->title .= "<img src='{$src}' class='{$cssSiteLogo} d-xs-none d-lg-inline-block' alt='{$siteName}' {$hwData}>";
            }

            $cssSiteName = WPOptions::SITE_NAME;
            $cssSiteDescription = WPOptions::SITE_DESCRIPTION;
            $cssSiteInfo = 'd-xs-none d-lg-block';
            $item->title .= "<h1 class='{$cssSiteName} {$cssSiteInfo}'>{$siteName}</h1>";
            $siteDescription = get_bloginfo('description', 'display');
            $item->title .= "<small class='{$cssSiteDescription} {$cssSiteInfo}'>{$siteDescription}</small>";
        }
        return $attributes;
    }

    function handleAdminInit()
    {
        remove_action('welcome_panel', 'wp_welcome_panel');
        remove_action('try_gutenberg_panel', 'wp_try_gutenberg_panel');
        remove_meta_box('dashboard_secondary', 'dashboard', 'normal');
        remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
        remove_meta_box('dashboard_quick_press', 'dashboard', 'normal');
        //Widget: Right now
        remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
        //Widget: WordPress Events and News
        remove_meta_box('dashboard_primary', 'dashboard', 'normal');
        if (is_multisite()) {
            //https://rudrastyh.com/wordpress-multisite/dashboard-widgets.html
            //Widget: Right now
            remove_meta_box('network_dashboard_right_now', 'dashboard-network', 'core');
            //Widget: WordPress Events and News
            remove_meta_box('dashboard_primary', 'dashboard-network', 'side');
        }
    }

    function handleAdminBarMenu(WP_Admin_Bar $wpAdminBar)
    {
        $wpAdminBar->remove_node('wp-logo');
    }

    function handleAdminBarRenderBefore()
    {
        /** Save the color styles list into wp_options table*/ global $_wp_admin_css_colors;
        if (is_array($_wp_admin_css_colors) && count($_wp_admin_css_colors) > 1 && has_action('admin_color_scheme_picker')) {
            update_option(self::OPTION_ADMIN_COLOR_STYLES, $_wp_admin_css_colors);
        }
    }

    function enqueueScriptsTheme()
    {
        if (is_admin_bar_showing()) {
            $adminColor = get_user_option('admin_color');
            $adminColorStyles = get_option(self::OPTION_ADMIN_COLOR_STYLES);
            if (isset($adminColor) && isset($adminColorStyles)) {
                $currentColor = $adminColorStyles[$adminColor];
                if ($currentColor) {
                    wp_enqueue_style($adminColor, $adminColorStyles[$adminColor]->url);
                }
            }
        }
    }

    /** Load Styles & Scripts for: Backend*/
    function enqueueScriptsAdmin()
    {
        $uriToDirLibs = UtilsWp::getUriToLibsDir();
        wp_enqueue_style('profile', "{$uriToDirLibs}/profile.css");
        wp_enqueue_media();
        wp_enqueue_script('profile', "{$uriToDirLibs}/profile.js", ['jquery']);
    }

    function handleUserRegister($idUser)
    {
        $activeUser = get_userdata($idUser);
        $displayNameNormalized = trim($activeUser->first_name . ' ' . $activeUser->last_name);
        if (!$displayNameNormalized) {
            $displayNameNormalized = $activeUser->user_login;
        }
        $activeUser->display_name = $displayNameNormalized;
        wp_update_user($activeUser);
    }

    function handlePersonalOptionsUpdate($idUser)
    {
        if (current_user_can('edit_user', $idUser)) {
            $displayNameNormalized = trim($_POST['first_name'] . " " . $_POST['last_name']);
            if (!$displayNameNormalized) {
                $displayNameNormalized = $_POST['user_login'];
            }
            $_POST['display_name'] = $displayNameNormalized;
            wp_update_user(['ID' => $idUser, 'display_name' => $displayNameNormalized]);
            UtilsWp::updateUserMeta($idUser, self::META_AVATAR);
        }
    }

    public function getAvatarData($args, $idOrEmail)
    {
        if (empty($idOrEmail)) {
            $idOrEmail = get_the_author_meta('ID');
        }
        if (is_object($idOrEmail)) {
            $idOrEmail = $idOrEmail->user_id;
        } else if ($idOrEmail !== intval($idOrEmail)) {
            $user = get_user_by('email', $idOrEmail);
            if (is_bool($user) == false) {
                $idOrEmail = $user->ID;
            }
        }
        $metaProfileAvatar = get_user_meta($idOrEmail, self::META_AVATAR, true);
        $profileImageUrl = wp_get_attachment_url($metaProfileAvatar);
        if (empty($profileImageUrl)) {
            $profileImageUrl = self::DEFAULT_AVATAR;
            //TODO If force default is true don't show image
            $args['force_default'] = true;
        }
        $args['found_avatar'] = true;
        $args['url'] = $profileImageUrl;
        return $args;
    }

    public function getAvatar($avatar)
    {
        if (empty($avatar) == false) {
            $dom = new DOMDocument();
            $dom->loadHTML($avatar);
            /**
             * @var DOMElement $item
             */
            foreach ($dom->getElementsByTagName('img') as $item) {
                $srcValue = $item->getAttribute('src');
                if (empty($srcValue)) {
                    $item->setAttribute('src', self::DEFAULT_AVATAR);
                }
                $item->removeAttribute('srcset');
                $avatar = $dom->saveHTML($item);
                return $avatar;
            }
        }
        return $avatar;
    }

    public function addContactFields($fields)
    {
        unset($fields['aim']);
        unset($fields['yim']);
        unset($fields['jabber']);
        return array_merge($fields, [self::META_PHONE => __('Mobile Number')]);
    }

    public function renderAfterPersonalOptions($user)
    {
        if (current_user_can('upload_files')) {
            $idUser = $user->ID;
            $idFieldAvatar = self::META_AVATAR;
            $metaProfileAvatar = get_user_meta($idUser, $idFieldAvatar, true);
            $contentAvatar = get_avatar($idUser, 96);
            $textChange = __("Change");
            $textProfilePicture = __("Profile Picture");
            echo "<tr><th scope='row'><label for='{$idFieldAvatar}'>{$textProfilePicture}</label></th>
            <td><input id='{$idFieldAvatar}' name='{$idFieldAvatar}' type='hidden' value='{$metaProfileAvatar}'/>
            <button id='btnSetImage' title='{$textChange} {$textProfilePicture}' type='button' class='button button-secondary'>
            {$contentAvatar} <p>{$textChange}</p></button></td></tr>";
        }
    }

    function handleCommentFormFields($fields)
    {
        $comment_field = $fields['comment'];
        unset($fields['comment']);
        $fields['comment'] = $comment_field;
        return $fields;
    }

    function handleGetTheAuthorId($value)
    {
        if (empty($value)) {
            global $authordata;
            $authordata = get_queried_object();
            if ($authordata && isset($authordata->ID)) {
                $value = $authordata->ID;
            }
        }
        return $value;
    }

    function handleWpHead()
    {
        if (is_author()) {
            //Solve the case when author don't have any post and it's data has not been populated
            add_filter("get_the_author_ID", [$this, 'handleGetTheAuthorId']);
        }
        //Adding the Open Graph Meta Info Docs: http://ogp.me
        //TODO Review this to publish all information about Post to facebook OpenGraph
        if (is_single()) {
            global $post;
            if (has_excerpt($post->ID)) {
                $description = strip_tags(get_the_excerpt());
            } else {
                $description = str_replace("\r\n", ' ',
                    substr(strip_tags(strip_shortcodes($post->post_content)), 0, 160));
            }
            if (empty($description)) {
                $description = get_bloginfo('description');
            }
            $pageThumb = "";
            if (has_post_thumbnail($post->ID)) {
                $thumbnailSrc = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
                $pageThumb = esc_attr($thumbnailSrc[0]);
            }
            $metas = ['og:site_name' => get_bloginfo('name'),
                'og:url' => get_permalink(),
                'og:type' => 'article',
                'og:image' => $pageThumb,
                'og:title' => get_the_title(),
                'og:description' => $description];
            foreach ($metas as $property => $content) {
                echo "<meta property='$property' content='$content'>\n";
            }
        }
    }

    function handleUserQuery($query)
    {
        if (WPOrderBy::RANDOM == $query->query_vars[QueryUsers::ORDER_BY]) {
            $query->query_orderby = str_replace(WPUser::LOGIN, "RAND()", $query->query_orderby);
        }
    }

    /**
     * Hide the admin bar on front end for users with user level equal to or below restricted level
     */
    function preventUserAdminBarUsage()
    {
        if (is_user_logged_in()) {
            if (UtilsWp::isUserRestricted()) {
                add_filter('show_admin_bar', '__return_false');
            }
            // get the the role object
            $editor = get_role('editor');
            // add $cap capability to this role object
            $editor->add_cap('edit_theme_options');
        }
    }

    /**
     * Restrict user access to admin if his level is equal or below restricted level
     * Or request is an AJAX request or delete request from my properties page
     */
    function preventUserAdminAccess()
    {
        if (defined('DOING_AJAX') && DOING_AJAX) {
            // let it go
        } else {
            if (isset($_GET['action']) && ($_GET['action'] == 'delete')) {
                // let it go as it is from my properties delete button
            } else {
                if (UtilsWp::isUserRestricted()) {
                    wp_redirect(esc_url_raw(home_url('/')));
                    exit;
                }
            }
        }
    }

    /**
     * Show Posts related to current Author only
     *
     * @param $wp_query
     */
    function showOnlyRelatedPosts(WP_Query $wp_query)
    {
        global $current_user;
        if (is_admin() && !current_user_can('edit_others_posts')) {
            $wp_query->set('author', $current_user->ID);
        }
    }

    /**
     * WordPress Login page Logo Title
     * @return string - site name
     */
    function getLoginLogoTitle()
    {
        return get_bloginfo('name');
    }

    /**
     * WordPress Login page Logo URL
     * @return string - site url
     */
    function getLoginLogoUrl()
    {
        return home_url();
    }

    /**
     * WordPress Login Logout Redirect
     * @return string - site url
     */
    static function handleLogout()
    {
        wp_redirect(home_url());
        exit();
    }

    /** Load Styles & Scripts for: Login Backend*/
    function handleScriptsAdminLoginLogo()
    {
        $logoId = get_theme_mod(WPOptions::SITE_LOGO);
        $image = wp_get_attachment_image_url($logoId, 'full');
        $textIndent = "-9999px";
        if (!$image) {
            $textIndent = "0";
        }
        $cssContent = "body.login div#login h1 a { 
			background-image: url($image);
			background-position: center center;
			background-size:auto;
			height: 96px;
		    width: auto;
		    text-indent: $textIndent;
		    font-size: 36px;
		    font-weight: bold;
		}
		.login form{background:none; margin-top:10px;} #backtoblog, #nav{display:none;}";
        wp_add_inline_style('login', $cssContent);
    }

    /**
     * Filters the URL for a user’s profile editor.
     * @link  https://developer.wordpress.org/reference/hooks/edit_profile_url/
     *
     * @param string $url The complete URL including scheme and path.
     * @param int $userId The user ID.
     *
     * @return string
     */
    function handleEditProfileUrl(string $url, int $userId)
    {
        if (function_exists('get_current_screen')) {
            $screen = get_current_screen();
            if ($screen->id == "profile") {
                $url = get_author_posts_url($userId);
            }
        }
        return $url;
    }

    function disableCoreUpdatesNotifications()
    {
        if (current_user_can('update_core')) {
            add_action('init', function () {
                remove_action('init', 'wp_version_check');
            }, 2);
            add_filter('pre_option_update_core', '__return_null');
            add_filter('pre_site_transient_update_core', '__return_null');
        }
    }

    function disableEmotionIcons()
    {
        if (is_admin() == false) {
            remove_action('wp_head', 'print_emoji_detection_script', 7);
            remove_action('admin_print_scripts', 'print_emoji_detection_script');
            remove_action('wp_print_styles', 'print_emoji_styles');
            remove_action('admin_print_styles', 'print_emoji_styles');
            remove_filter('the_content_feed', 'wp_staticize_emoji');
            remove_filter('comment_text_rss', 'wp_staticize_emoji');
            remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
        }
    }

    function removeWpVersion($src)
    {
        if (strpos($src, 'ver=')) {
            $src = remove_query_arg('ver', $src);
        }
        return $src;
    }

    function handleUploadMimeTypes($mimes)
    {
        $mimes['svg'] = 'image/svg+xml';
        $mimes['csv'] = 'text/csv';
        //$mimes['txt'] = 'text/plain';
        return $mimes;
    }
}