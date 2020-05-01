<?php /** Author: Vitali Lupu <vitaliix@gmail.com> */
namespace wp;

use WP_Query;

final class UtilsWp {
    /** @var \WP_Query $currentQuery */
    private static $currentQuery;
    private static $postsWatermarked = [];

    static function getPostTypes($args = [], $field = false, $operator = 'and') {
        global $wp_post_types;
        return wp_filter_object_list($wp_post_types, $args, $operator, $field);
    }

    static function getCurrentQuery() {
        return self::$currentQuery;
    }

    static function locatePostTemplate(string $postType, string $name, $currentDir = '') {
        if ($name) {
            $name = strtolower($name);
        }
        $postBaseType = PostBase::TYPE;
        $templateNames = [];
        if ($name !== '') {
            $templateNames[] = "{$postType}-{$name}.php";
            if ($postType != $postBaseType) {
                $templateNames [] = "{$postBaseType}-{$name}.php";
            }
        }
        $located = '';
        foreach ($templateNames as $templateName) {
            if ($templateName) {
                if (file_exists(STYLESHEETPATH . '/' . $templateName)) {
                    $located = STYLESHEETPATH . '/' . $templateName;
                    break;
                } else {
                    if (file_exists(TEMPLATEPATH . '/' . $templateName)) {
                        $located = TEMPLATEPATH . '/' . $templateName;
                        break;
                    } else {
                        if (file_exists(ABSPATH . WPINC . '/theme-compat/' . $templateName)) {
                            $located = ABSPATH . WPINC . '/theme-compat/' . $templateName;
                            break;
                        } else {
                            if (file_exists($currentDir . '/' . $templateName)) {
                                $located = $currentDir . '/' . $templateName;
                                break;
                            }
                        }
                    }
                }
            }
        }
        return $located;
    }

    static function renderTemplate(array $queryArgs, string $templatePath = '', &$results = 0) {
        $content = '';
        if ($templatePath != '') {
            self::$currentQuery = new WP_Query($queryArgs);
            $results = self::$currentQuery->post_count;
            while (self::$currentQuery->have_posts()) {
                self::$currentQuery->the_post();
                ob_start();
                load_template($templatePath, false);
                $content .= ob_get_clean();
            }
        }
        if (empty($content)) {
            $content .= self::getNotFoundMessage(__('No posts found.'));
        }
        self::$currentQuery = null;
        wp_reset_query();
        return $content;
    }

    static function getCurrentScreenId() {
        $currentScreenId = '';
        if (is_admin()) {
            $currentAdminScreen = get_current_screen();
            $currentScreenId = $currentAdminScreen->id;
        }
        return $currentScreenId;
    }

    static function getNotFoundMessage($message = '', $redirectLink = '') {
        if (empty($redirectLink)) {
            $urlHome = home_url();
            $textHome = __('Home');
            $redirectLink = "<a href='$urlHome' class='text-danger'>{$textHome}</a>";
        }
        if (empty($message)) {
            $textNotFound = __("It looks like nothing was found at this location. Maybe try visiting %s directly?");
            $message = sprintf($textNotFound, $redirectLink);
        }
        return "<h4 class='not-found text-xs-center'>{$message}</h4>";
    }
    // TODO: Make this functionality optional because it is not verify attachment dependency for case when some one use same image in different post type
    // TODO: Make a function that register who and when delete the object
    /**
     * Delete Atached images to the post
     * Docs: https://codex.wordpress.org/Plugin_API/Action_Reference/before_delete_post
     * Solution: https://wordpress.org/support/topic/is-there-any-solution-for-deleting-posts-also-deletes-image-attached-to-it/
     *
     * @param $post_id
     */
    static function deletePostAttachments($post_id) {
        //TODO Make Customizer option where we be able to chose for which post Media files must be deleted
        $postType = get_post_type($post_id);
        if (in_array($postType, self::$postsWatermarked)) {
            $media = get_children([QueryPost::PARENT => $post_id,
                                   QueryPost::TYPE   => WPostTypes::ATTACHMENT]);
            if (!empty($media)) {
                foreach ($media as $file) {
                    // pick what you want to do
                    // unlink(get_attached_file($file->ID));
                    //TODO Delete Cached File with same id
                    wp_delete_attachment($file->ID);
                }
            }
        }
    }

    static function getThumbnail($size = WPImages::THUMB, $attr = null) {
        $postId = get_the_ID();
        $result = false;
        //TODO Add Option to Customizer where we be able to choose which Post will be watermarked
        if (has_post_thumbnail($postId)) {
            $imageId = get_post_thumbnail_id($postId);
            //TODO Don't add attr if i want to watermark it will return only scaled image path then we will watermark and add watermark-termination
            $result = Placeholder::getScaledImage($imageId, $size, $attr);
            //$postType = get_post_type($postId);
            //in_array($postType, self::$postsWatermarked)
        }
        if (!$result) {
            $result = Placeholder::getPlaceHolder($size, $attr);
        }
        return $result;
    }

    static function loadThemeLocale($domain, $language = "") {
        unload_textdomain($domain);
        if (empty($language)) {
            $language = UtilsWp::getLanguageShortCode();
        }
        $result = load_textdomain($domain, get_template_directory() . "/languages/$language.mo");
        if (!$result) {
            $result = load_textdomain($domain, WP_LANG_DIR . "/themes/$domain-$language.mo");
        }
        return $result;
    }

    static function getLanguageShortCode() {
        $locale = get_locale();
        if (is_admin()) {
            $locale = get_user_locale();
        }
        $locale = substr($locale, 0, 2);
        return $locale;
    }

    /**
     * Retrieve or display nonce hidden field for forms.
     * The nonce field is used to validate that the contents of the form came from
     * the location on the current site and not somewhere else. The nonce does not
     * offer absolute protection, but should protect against most cases. It is very
     * important to use nonce field in forms.
     * The $action and $name are optional, but if you want to have better security,
     * it is strongly suggested to set those two parameters. It is easier to just
     * call the function without any parameters, because validation of the nonce
     * doesn't require any parameters, but since crackers know what the default is
     * it won't be difficult for them to find a way around your nonce and cause
     * damage.
     * The input name will be whatever $name value you gave. The input value will be
     * the nonce creation value.
     *
     * @param int|string $action   Optional. Action name. Default -1.
     * @param string     $name     Optional. Nonce name. Default '_wpnonce'.
     * @param bool       $referrer Optional. Whether to set the referer field for validation. Default true.
     * @param bool       $echo     Optional. Whether to display or return hidden form field. Default true.
     *
     * @return string Nonce field HTML markup.
     */
    static function getNonceField($action = -1, string $name = "_nonce", bool $referrer = true, bool $echo = true) {
        $name = esc_attr($name);
        $nonce = wp_create_nonce($action);
        $nonceField = "<input type='hidden' name='$name' value='$nonce'/>";
        if ($referrer) {
            $nonceField .= wp_referer_field(false);
        }
        if ($echo) {
            echo $nonceField;
        }
        return $nonceField;
    }

    static function isRealUserBrowser() {
        //Unknown
        return (isset($_SERVER['HTTP_USER_AGENT']) && empty($_SERVER['HTTP_USER_AGENT']) === false && strpos($_SERVER['HTTP_USER_AGENT'], 'GTmetrix') === false && strpos($_SERVER['HTTP_USER_AGENT'], 'Speed Insights') === false && strpos($_SERVER['HTTP_USER_AGENT'], 'Bot') === false && strpos($_SERVER['HTTP_USER_AGENT'], 'bot') === false);
    }

    static function saveServerReferrals() {
        $httpUserAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "Empty";
        //$httpReferral = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "EmptyReferer";
        if (!empty($httpUserAgent)) {
            $fp = fopen('file.txt', 'a');
            //fwrite($fp, "$httpUserAgent [$httpReferral]" . "\n");
            fwrite($fp, "$httpUserAgent" . "\n");
            fclose($fp);
        }
    }

    /**
     * Checks if current user is restricted or not
     * @return bool
     */
    static function isUserRestricted() {
        // get restricted level from theme options
        $levelOfRestriction = get_option(SettingsSite::BACKEND_ACCESS_LEVEL);
        if (!empty($levelOfRestriction)) {
            $levelOfRestriction = intval($levelOfRestriction);
        } else {
            $levelOfRestriction = 0;
        }
        $current_user = wp_get_current_user();
        // Redirects user below a certain user level to home url
        // Ref: https://codex.wordpress.org/Roles_and_Capabilities#User_Level_to_Role_Conversion
        if ($current_user->user_level <= $levelOfRestriction) {
            return true;
        }
        return false;
    }

    /**
     * Register Custom Widgets and Sidebars
     *
     * @param        $id
     * @param        $name
     * @param        $description
     * @param string $tagTitle
     * @param string $tagContent
     */
    static function registerSidebarWidget($id, $name, $description = '', $tagTitle = 'h2', $tagContent = 'div') {
        register_sidebar([WPSidebar::ID                 => $id,
                          WPSidebar::NAME               => $name,
                          WPSidebar::DESCRIPTION        => $description,
                          WPSidebar::BEFORE_WIDGET      => "<$tagContent id='%1\$s' class='widget %2\$s'>",
                          WPSidebar::AFTER_WIDGET       => "</$tagContent>",
                          WPSidebar::BEFORE_TITLE       => "<$tagTitle class='widgettitle'>",
                          WPSidebar::AFTER_TITLE        => "</$tagTitle>",
                          WPSidebar::CONTAINER_SELECTOR => "#$id"]);
    }

    static function getSidebarContent(string $id) {
        $content = "";
        if (is_active_sidebar($id)) {
            ob_start();
            dynamic_sidebar($id);
            $content = ob_get_clean();
            $content = self::getSidebar($id, $content);
        }
        return $content;
    }

    static function getSidebar(string $id, string $content = '',array $classes = [], string $tagContent = 'div') {
        $sidebarMetaValue = (array)get_option(Widget::WIDGET_AREA);
        if (isset($sidebarMetaValue[$id]) && isset($sidebarMetaValue[$id][Widget::CSS_CLASSES])) {
            $sidebarBgColourValue = $sidebarMetaValue[$id][Widget::CSS_CLASSES];
            if (empty($sidebarBgColourValue) == false) {
                $classesDefined = explode(' ', $sidebarBgColourValue);
                $classes = array_merge($classesDefined, $classes);
            }
        }
        $classes = array_map('esc_attr', $classes);
        $classes = array_unique($classes);
        $classesContent = join(' ', $classes);
        return "<$tagContent id='{$id}' class='widget-area $id {$classesContent}'>{$content}</$tagContent>";
    }

    static function getCurrentAuthor() {
        if (get_query_var('author_name')) {
            $currentAuthor = get_user_by('slug', get_query_var('author_name'));
        } else {
            $currentAuthor = get_userdata(get_query_var('author'));
        }
        if (!$currentAuthor) {
            global $authordata;
            $currentAuthor = $authordata;
        }
        return $currentAuthor;
    }

    static function getShortClassName($className) {
        if (empty($className) == false) {
            $classNameStartPos = strrpos($className, '\\');
            if ($classNameStartPos) {
                $classNameStartPos++;
            }
            $className = substr($className, $classNameStartPos);
        }
        return $className;
    }

    static function getUriToLibsDir($path = __FILE__) {
        $pathToLibs = self::getDirName($path, 1) . 'libs';
        $uriTemplateDir = get_template_directory_uri();
        return $uriTemplateDir . $pathToLibs;
    }

    static function getDirName($path, $level) {
        $result = DIRECTORY_SEPARATOR;
        if (isset($path) && $level > 0) {
            while ($level !== 0) {
                $result .= basename(dirname($path, $level--)) . DIRECTORY_SEPARATOR;
            }
        }
        return $result;
    }

    static function getPostAuthorAndDate($showFullDate = false, $showAuthor = false) {
        $textPublished = __('Published');
        $publishDate = human_time_diff(get_the_modified_time('U'), current_time('timestamp'));
        $publishTime = get_the_modified_time('d M Y');
        $result = sprintf(__('%1$s %2$s, %3$s ago (%4$s)'), '', $textPublished, $publishDate, $publishTime);
        if ($showFullDate == false) {
            $result = strtok($result, '(');
        }
        if ($showAuthor) {
            $result .= ' ' . self::getAuthorOfPost();
        }
        return $result;
    }

    static function getAuthorOfPost($showAvatar = true) {
        $author = get_the_author_meta('display_name');
        $byAuthor = '';
        //TODO Add code Author avatar photo resolve
        //if ($showAvatar) { }
        $textAuthor = __('Author:');
        $byAuthor .= "<strong>{$textAuthor} {$author}</strong>";
        return $byAuthor;
    }

    /**
     * Execute functions hooked on a specific action hook.
     * This function invokes all functions attached to action hook `$tag`. It is
     * possible to create new action hooks by simply calling this function,
     * specifying the name of the new hook using the `$tag` parameter.
     * You can pass extra arguments to the hooks, much like you can with apply_filters().
     *
     * @param string $tag     The name of the action to be executed.
     * @param mixed  $arg,... Optional. Additional arguments which are passed on to the
     *                        functions hooked to the action. Default empty.
     *
     * @return string
     */
    static function doAction($tag, ...$arg) {
        ob_start();
        do_action($tag, $arg);
        return ob_get_clean();
    }

    static function isSiteEditor() {
        return (current_user_can('editor') || current_user_can('administrator'));
    }

    static function updateUserMeta($userId, $metaKey) {
        if (isset($_POST[$metaKey])) {
            update_user_meta($userId, $metaKey, $_POST[$metaKey]);
        }
    }

    /*-----------------[ Sanitization Callbacks ]*/
    static function sanitizeCheckbox($checked) {
        // Boolean check.
        return ((isset($checked) && true == $checked) ? true : false);
    }

    static function sanitizeMultiCheck($values) {
        $multi_values = !is_array($values) ? explode(',', $values) : $values;
        return !empty($multi_values) ? array_map('sanitize_text_field', $multi_values) : [];
    }

    static function sanitizeDropdownPages($page_id, $setting) {
        // Ensure $input is an absolute integer.
        $page_id = absint($page_id);
        // If $page_id is an ID of a published page, return it; otherwise, return the default.
        return ('publish' == get_post_status($page_id) ? $page_id : $setting->default);
    }

    static function sanitizeColor($color) {
        $result = '';
        if (empty($color) == false && is_array($color) == false) {
            // If string does not start with 'rgba', then treat as hex.
            // sanitize the hex color and finally convert hex to rgba
            if (strpos($color, 'rgba')) {
                $color = str_replace(' ', '', $color);
                list($red, $green, $blue, $alpha) = sscanf($color, 'rgba(%d,%d,%d,%f)');
                $result = "rgba({$red},{$green},{$blue},{$alpha})";
            } else {
                $result = sanitize_hex_color($color);
            }
        }
        return $result;
    }

    static function sanitizeMultiChoices($input, $setting) {
        // Get list of choices from the control associated with the setting.
        $choices = $setting->manager->get_control($setting->id)->choices;
        $input_keys = $input;
        foreach ($input_keys as $key => $value) {
            if (!array_key_exists($value, $choices)) {
                unset($input[$key]);
            }
        }
        // If the input is a valid key, return it;
        // otherwise, return the default.
        return (is_array($input) ? $input : $setting->default);
    }

    static function sanitizeImage($image, $setting) {
        /*
         * Array of valid image file types.
         * The array includes image mime types that are included in wp_get_mime_types()
         */
        $mimes = ['jpg|jpeg|jpe' => 'image/jpeg',
                  'gif'          => 'image/gif',
                  'png'          => 'image/png',
                  'bmp'          => 'image/bmp',
                  'tif|tiff'     => 'image/tiff',
                  'ico'          => 'image/x-icon'];
        // Return an array with file extension and mime_type.
        $file = wp_check_filetype($image, $mimes);
        // If $image has a valid mime_type, return it; otherwise, return the default.
        return ($file['ext'] ? $image : $setting->default);
    }

    static function sanitizeNumber($value) {
        return is_numeric($value) ? $value : 0;
    }

    /**
     * Number with blank value
     *
     * @param $value
     *
     * @return int|string
     */
    static function sanitizeNumberWithBlankValue($value) {
        return is_numeric($value) ? $value : '';
    }

    /**
     * Sanitizie Select control values
     *
     * @param $input
     * @param $setting
     *
     * @return string
     */
    static function sanitizeSelect($input, $setting) {
        // Ensure input is a slug.
        $input = sanitize_key($input);
        // Get list of choices from the control associated with the setting.
        $choices = $setting->manager->get_control($setting->id)->choices;
        // If the input is a valid key, return it; otherwise, return the default.
        return (array_key_exists($input, $choices) ? $input : $setting->default);
    }
}