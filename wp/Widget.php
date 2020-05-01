<?php
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/5/18
 * Time: 6:32 PM
 */

namespace wp;

use WP_Widget;

abstract class Widget extends WP_Widget
{
    const CUSTOM_TITLE = 'title';
    const CSS_CLASSES = 'cssClasses';
    const WIDGET_AREA = 'widgetArea';
    const VISIBLE_FOR_USERS = 'visibleForUsers';
    const USERS_ALL = 'usersAll';
    const USERS_AUTHORIZED = 'usersAuthorized';
    const USERS_NOT_AUTHORIZED = 'usersNotAuthorized';
    const VISIBILITY_ON_PAGES = 'showWidgetOnPages';
    const PAGES = 'pagesGeneral';
    const PAGE_FRONT = 'pageFront';
    const PAGE_BLOG = 'pageBlog';
    const PAGE_ARCHIVES = 'pageArchives';
    const PAGE_AUTHOR = 'pageAuthor';
    const PAGE_SINGLE_POST = 'pageSinglePost';
    const PAGE_SEARCH = 'pageSearch';
    const PAGE_404 = 'page404';

    const CLASSNAME = 'classname';
    const DESCRIPTION = 'description';
    const CUSTOMIZE_SELECTIVE_REFRESH = 'customize_selective_refresh';
    const MIME_TYPE = 'mime_type';
    static $widgetFields = [];
    static $widgetDefaultOptions = [];
    private static $pagesOfSite;
    private static $pagesGeneral;
    private static $pagesOfPosts;
    private static $pagesOfArchives;
    private static $pagesOfCategories;
    private static $pagesOfTaxonomies;
    private static $commonFiltersAdded = false;
    protected $fields = [];
    private $defaultOptions = [];

    /**
     * Widget constructor.
     * @param $name
     * @param string $description
     * @param array $widgetOptions
     * @param array $controlOptions
     */
    function __construct(string $name, $description = '', array $widgetOptions = [], array $controlOptions = [])
    {
        /*$reflect = new \ReflectionClass($this);
        $className = $reflect->getShortName();
        substr(static::class, strrpos(static::class, '\\') + 1);
        */
        $className = UtilsWp::getShortClassName(static::class);
        parent::__construct($className, $name, wp_parse_args($widgetOptions, [
            self::CLASSNAME => $className,
            self::DESCRIPTION => $description,
            self::CUSTOMIZE_SELECTIVE_REFRESH => true,
            self::MIME_TYPE => '',
        ]), $controlOptions);
        $this->addField(new WidgetField(WidgetField::TEXT, Widget::CUSTOM_TITLE,
            __('Custom Title', WpApp::TEXT_DOMAIN)));
        add_action(WPActions::INIT, [$this, 'initFields']);
        add_action(WPActions::ENQUEUE_SCRIPTS_THEME, [$this, 'enqueueScriptsTheme']);
        add_action(WPActions::ENQUEUE_SCRIPTS_ADMIN, [$this, 'enqueueScriptsAdmin']);
        add_action(WPActions::ENQUEUE_SCRIPTS_CUSTOMIZER, [$this, 'enqueueScriptsCustomizer']);
    }

    protected function addField(WidgetField $field)
    {
        $this->fields[$field->name] = $field;
        $this->defaultOptions[$field->name] = $field->defaultValue;
    }

    static function handleWidgetsUpdate($instanceOld, $instanceNew)
    {
        return self::updateWidget($instanceOld, $instanceNew, Widget::$widgetFields);
    }

    /**
     * @param WP_Widget $widget
     * @param $return
     * @param array $instance
     */
    public static function handleWidgetsForm(WP_Widget $widget, $return, array $instance)
    {
        self::showWidgetForm($widget, $instance, Widget::$widgetFields, Widget::$widgetDefaultOptions);
    }

    static function handleInit()
    {
        Widget::addFieldCommon(new WidgetField(WidgetField::SELECT, Widget::VISIBILITY_ON_PAGES,
            __('Visibility on chosen "Pages"', WpApp::TEXT_DOMAIN), [
                "1" => __("Show"),
                "0" => __("Hide"),
            ], "1"));
        $posts = Widget::getPagesOfPosts();
        $optionsPages = [
            __("General") => Widget::getPagesGeneral(),
            __("Posts") => $posts,
            __("Archives") => Widget::getPagesOfArchives(),
            __("Taxonomies") => Widget::getPagesOfTaxonomies($posts),
            __("Categories") => Widget::getPagesOfCategories(),
            __('Static Pages') => Widget::getPagesOfSite(),
        ];
        Widget::addFieldCommon(new WidgetField(WidgetField::SELECT_MULTIPLE, Widget::PAGES,
            __('Chose "Pages"'), $optionsPages));
        Widget::addFieldCommon(new WidgetField(WidgetField::SELECT, Widget::VISIBLE_FOR_USERS,
            __('Which users will see this widget', WpApp::TEXT_DOMAIN), [
                Widget::USERS_ALL => __('All Users'),
                Widget::USERS_AUTHORIZED => __('Authorized'),
                Widget::USERS_NOT_AUTHORIZED => __('Not Authorized'),
            ],
            Widget::USERS_ALL));
        Widget::addFieldCommon(new WidgetField(WidgetField::TEXT, Widget::CSS_CLASSES, __('CSS Classes')));
    }

    static function addFieldCommon(WidgetField $field)
    {
        Widget::$widgetFields[$field->name] = $field;
        Widget::$widgetDefaultOptions[$field->name] = $field->defaultValue;
    }

    static function getPagesOfPosts()
    {
        if (!self::$pagesOfPosts) {
            $posts = UtilsWp::getPostTypes(['public' => true], 'label');
            unset($posts['revision']);
            unset($posts['nav_menu_item']);
            self::$pagesOfPosts = $posts;
        }

        return self::$pagesOfPosts;
    }

    static function getPagesGeneral()
    {
        if (!self::$pagesGeneral) {
            self::$pagesGeneral = [
                Widget::PAGE_FRONT => __('Front Page'), //Single page marked as Front Page
                Widget::PAGE_BLOG => __('Blog'), //Single page where is paced all Blog content HomePage [home.php, front-page.php, index.php]
                Widget::PAGE_SEARCH => __('Search'), // Single Search Page [search.php]
                Widget::PAGE_404 => '404', // Single Not found page [404.php]
                Widget::PAGE_AUTHOR => __('Author'), //Pages of Authors [author.php, author-{name}.php]
                Widget::PAGE_SINGLE_POST => __('Single Post'), //For All Post Types [single.php, single-{name}.php]
                Widget::PAGE_ARCHIVES => __('Archives'), //For All Archive Pages [archive.php, archive-{name}.php]
            ];
        }

        return self::$pagesGeneral;
    }

    static function getPagesOfArchives()
    {
        if (!self::$pagesOfArchives) {
            self::$pagesOfArchives = UtilsWp::getPostTypes(['public' => true, 'has_archive' => true], 'label');
        }

        return self::$pagesOfArchives;
    }

    static function getPagesOfTaxonomies($posts)
    {
        if (!self::$pagesOfTaxonomies) {
            global $wp_taxonomies;
            $taxonomies = $wp_taxonomies;
            unset($taxonomies['post_tag']);
            unset($taxonomies['post_format']);
            $postsKeys = array_keys($posts);
            $postTaxonomies = [];
            foreach ($wp_taxonomies as $key => $taxonomy) {
                if (count($taxonomy->object_type) == 1 && in_array($taxonomy->object_type[0],$postsKeys)) {
                    $postTaxonomies[$key] = $taxonomy->label;
                }
            }
            self::$pagesOfTaxonomies = $postTaxonomies;
        }

        return self::$pagesOfTaxonomies;
    }

    static function getPagesOfCategories()
    {
        if (!self::$pagesOfCategories) {
            self::$pagesOfCategories = get_categories([
                QueryTaxonomy::HIDE_EMPTY => false,
                QueryTaxonomy::FIELDS  => 'id=>name'
            ]);
        }

        return self::$pagesOfCategories;
    }

    static function getPagesOfSite()
    {
        if (!self::$pagesOfSite) {
            $postPages = get_posts([
                QueryPost::TYPE => WPostTypes::PAGE,
                QueryPost::STATUS => WPostStatus::PUBLISH,
                QueryPost::ORDER_BY => WPOrderBy::TITLE,
                QueryPost::ORDER => WPOrder::ASC,
                QueryPost::PER_PAGE => -1,
            ]);
            $pages = [];
            foreach ($postPages as $page) {
                $pages[$page->ID] = apply_filters('translate_text', $page->post_title);
            }
            self::$pagesOfSite = $pages;
        }

        return self::$pagesOfSite;
    }

    /**
     * Filters the settings for a particular widget instance.
     * @url https://developer.wordpress.org/reference/hooks/widget_display_callback/
     *
     * @param array $instance The current widget instance's settings.
     * @param WP_Widget $widgetInstance The current widget instance.
     * @param array $args An array of default widget arguments.
     *
     * @return bool Returning false will effectively short-circuit display of the widget.
     */
    static function handleWidgetsDisplay(array $instance, WP_Widget $widgetInstance, array $args)
    {
        $cssClasses = self::getInstanceValue($instance, Widget::CSS_CLASSES, $widgetInstance);
        if (!empty($cssClasses) && isset($args[WPSidebar::BEFORE_WIDGET])) {
            $className = $widgetInstance->widget_options[Widget::CLASSNAME];
            $cssClassesWithClassName = $className . " " . $cssClasses;
            $beforeWidgetMarkup = $args[WPSidebar::BEFORE_WIDGET];
            $args[WPSidebar::BEFORE_WIDGET] = str_replace($className, $cssClassesWithClassName, $beforeWidgetMarkup);
        }
        $widgetInstance->widget($args, $instance);

        return false;
    }

    /**
     * Filters the list of sidebars and their widgets.
     *
     * @param array $widgetAreas An associative array of sidebars and their widgets.
     *
     * @return array The modified $widget_area array.
     */
    public static function handleSidebarsWidgets(array $widgetAreas)
    {
        $settings = [];
        foreach ($widgetAreas as $widgetArea => $widgets) {
            if ($widgetArea == 'wp_inactive_widgets' ||
                strpos($widgetArea, 'orphaned_widgets') === 0 ||
                empty($widgets) ||
                !is_array($widgets)) {
                continue;
            }
            foreach ($widgets as $widgetPosition => $widgetId) {
                if (preg_match('/^(.+?)-(\d+)$/', $widgetId, $matches)) {
                    $idBase = $matches[1];
                    $widgetNumber = intval($matches[2]);
                } else {
                    $idBase = $widgetId;
                    $widgetNumber = null;
                }
                if (isset($settings[$idBase]) == false) {
                    $settings[$idBase] = get_option('widget_' . $idBase);
                }
                if (!is_null($widgetNumber)) {
                    // New multi widget (WP_Widget)
                    if (isset($settings[$idBase][$widgetNumber]) &&
                        self::displayWidget($settings[$idBase][$widgetNumber]) === false) {
                        unset($widgetAreas[$widgetArea][$widgetPosition]);
                    }
                } else if (!empty($settings[$idBase]) &&
                    // Old single widget
                    self::displayWidget($settings[$idBase]) === false) {
                    unset($widgetAreas[$widgetArea][$widgetPosition]);
                }
            }
        }

        return $widgetAreas;
    }

    /**
     *
     * @param mixed $instance The current widget instance's settings.
     *
     * @return array|bool Returning false will effectively short-circuit display of the widget.
     */
    static private function displayWidget($instance)
    {
        if (!is_array($instance)) {
            $instance = [];
        }
        $showWidget = true;
        $visibleForUsers = self::getInstanceValue($instance, Widget::VISIBLE_FOR_USERS);
        if ($visibleForUsers == Widget::USERS_AUTHORIZED && !is_user_logged_in()) {
            $showWidget = false;
        } else if ($visibleForUsers == Widget::USERS_NOT_AUTHORIZED && is_user_logged_in()) {
            $showWidget = false;
        } else {
            $pagesGeneral = self::getInstanceValue($instance, Widget::PAGES);
            if (is_array($pagesGeneral) && !empty($pagesGeneral)) {
                $showWidgetOnPages = (self::getInstanceValue($instance, Widget::VISIBILITY_ON_PAGES) == 1);
                $hideForRestPage = (!empty($pagesGeneral) && $showWidgetOnPages);
                $pageId = "";
                if (is_home()) {
                    $pageId = Widget::PAGE_BLOG;
                } else if (is_front_page()) {
                    $pageId = Widget::PAGE_FRONT;
                } else if (is_author()) {
                    $pageId = Widget::PAGE_AUTHOR;
                } else if (is_category()) {
                    if (in_array('category', $pagesGeneral) == false) {
                        $pageId = get_query_var('cat');
                    } else {
                        $pageId = 'category';
                    }
                } else if (is_tax()) {
                    $term = get_queried_object();
                    $pageId = $term->taxonomy;
                } else if (is_post_type_archive()) {
                    $pageId = get_post_type();
                    //TODO Add Case when select which type of Archive to Handle
                } else if (is_archive()) {
                    $pageId = Widget::PAGE_ARCHIVES;
                } else if (is_single()) {
                    $pageId = Widget::PAGE_SINGLE_POST;
                } else if (is_search()) {
                    $pageId = Widget::PAGE_SEARCH;
                } else if (is_404()) {
                    $pageId = Widget::PAGE_404;
                } else if (is_page()) {
                    $pageId = get_post_type();
                    //TODO Add Case when select specific "Static Page"
                }

                if (in_array($pageId, $pagesGeneral)) {
                    $showWidget = $showWidgetOnPages;
                } else if ($hideForRestPage) {
                    $showWidget = false;
                }
            }
            if ($showWidget) {
                $showWidget = $instance;
            }
        }

        return $showWidget;
    }

    final public static function i()
    {
        /** Add Handlers for all Widgets Event */
        if (self::$commonFiltersAdded === false) {
            add_filter(WPActions::INIT, [self::class, 'handleInit'], 999);
            add_filter(WPActions::WIDGET_UPDATE, [self::class, 'handleWidgetsUpdate'], 10, 2);
            add_filter(WPActions::WIDGET_DISPLAY, [self::class, 'handleWidgetsDisplay'], 10, 3);
            add_filter(WPActions::WIDGET_FORM_AFTER, [self::class, 'handleWidgetsForm'], 999, 3);
            if (!is_admin() && !in_array($GLOBALS['pagenow'], ['wp-login.php', 'wp-register.php'])) {
                add_filter(WPActions::WIDGETS_IN_SIDEBARS, [self::class, 'handleSidebarsWidgets']);
            }
        }
    }

    public function getDefaultOptions()
    {
        return $this->defaultOptions;
    }

    /**
     * Load Required CSS Styles and Javascript Files
     * Docs: https://developer.wordpress.org/themes/basics/including-css-javascript/
     */
    function enqueueScriptsTheme()
    {
    }

    /** Load Styles & Scripts for: Backend*/
    function enqueueScriptsAdmin($screenId)
    {
        /*$screen = get_current_screen();
        $screenId = $screen->id;*/
        if ($screenId == 'widgets') {
            $this->enqueueScriptsCustomizer();
        }
    }

    /** Load Styles & Scripts for: Customizer */
    function enqueueScriptsCustomizer()
    {
        $uriToDirLibs = UtilsWp::getUriToLibsDir();
        //TODO Replace Semantic Dropdown with own implementation
        $nameOfLibTransition = 'dropdown-transition';
        $nameOfLibDropDown = 'dropdown';
        wp_enqueue_style($nameOfLibTransition, "{$uriToDirLibs}/semantic/transition/transition.css");
        wp_enqueue_script($nameOfLibTransition, "{$uriToDirLibs}/semantic/transition/transition.js", ['jquery']);
        wp_enqueue_style($nameOfLibDropDown, "{$uriToDirLibs}/semantic/dropdown/dropdown.css");
        wp_enqueue_script($nameOfLibDropDown, "{$uriToDirLibs}/semantic/dropdown/dropdown.js", [$nameOfLibTransition]);
        wp_enqueue_style('dropdownfix', "{$uriToDirLibs}/semantic/dropdown/dropdownfix.css");
        wp_enqueue_script('dropdownfix', "{$uriToDirLibs}/semantic/dropdown/dropdownfix.js", [$nameOfLibDropDown]);
        wp_enqueue_script('add-media', "{$uriToDirLibs}/media.js", ['wp-api', 'jquery']);
    }

    function initFields()
    {
        remove_action(WPActions::INIT, [$this, 'initFields']);
    }

    public function widget($args, $instance)
    {
        $titleContent = '';
        $title = self::getInstanceValue($instance, Widget::CUSTOM_TITLE, $this);
        if (ctype_space($title) || $title === "" || $title === null) {
            $title = '';
        } else {
            $title = __(apply_filters('widget_title', $title, $instance, $this->id_base), WpApp::TEXT_DOMAIN);
        }
        $titleAdditionBefore = '';
        if (isset($args[WPSidebar::BEFORE_TITLE_ADDITION])) {
            $titleAdditionBefore = $args[WPSidebar::BEFORE_TITLE_ADDITION];
        }
        $titleAdditionAfter = '';
        if (isset($args[WPSidebar::AFTER_TITLE_ADDITION])) {
            $titleAdditionAfter = $args[WPSidebar::AFTER_TITLE_ADDITION];
        }
        if (!empty($title) || !empty($titleAdditionBefore) || !empty($titleAdditionAfter)) {
            $titleContent .= $args[WPSidebar::BEFORE_TITLE];
            $titleContent .= $titleAdditionBefore;
            $titleContent .= $title;
            $titleContent .= $titleAdditionAfter;
            $titleContent .= $args[WPSidebar::AFTER_TITLE];
        }
        echo $args[WPSidebar::BEFORE_WIDGET] . $titleContent . $args[WPSidebar::CONTENT] . $args[WPSidebar::AFTER_WIDGET];
    }

    final static function getInstanceValue(array $instance, string $value, WP_Widget $widget = null)
    {
        $result = "";
        if ($instance && isset($instance[$value])) {
            $result = $instance[$value];
        } else {
            $methodVariable = [$widget, 'getDefaultOptions'];
            if ($widget && is_callable($methodVariable)) {
                $defaultOptions = call_user_func($methodVariable);
                if (is_array($defaultOptions) && isset($defaultOptions[$value])) {
                    $result = $defaultOptions[$value];
                }
            }
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function update($new_instance, $old_instance)
    {
        return self::updateWidget($old_instance, $new_instance, $this->fields);
    }

    private static function updateWidget($instanceOld, $instanceNew, $fields)
    {
        $instance = $instanceOld;
        /** @var WidgetField $field */
        foreach ($fields as $field) {
            $newValue = '';
            if (isset($instanceNew[$field->name])){
                $newValue = $instanceNew[$field->name];
                if (is_string($newValue)) {
                    $newValue = strip_tags($newValue);
                }
            }
            $instance[$field->name] = $newValue;
        }

        return $instance;
    }

    function form($instance)
    {
        self::showWidgetForm($this, $instance, $this->fields, $this->defaultOptions);
    }

    private static function showWidgetForm(WP_Widget $widget, array $instance, array $widgetFields, array $widgetDefaultOptions)
    {
        $instance = wp_parse_args($instance, $widgetDefaultOptions);
        /** @var WidgetField $field */
        foreach ($widgetFields as $field) {
            $fieldType = $field->type;
            if ($fieldType) {
                $fieldId = $widget->get_field_id($field->name);
                if (!is_integer($widget->number)) {
                    $fieldId = 'widget-' . $widget->id_base . '-' . uniqid() . '-' . trim(str_replace(array('[]', '[', ']'), array('', '-', ''), $field->name), '-');
                }
                $fieldName = $widget->get_field_name($field->name);
                $fieldValue = self::getInstanceValue($instance, $field->name, $widget);
                $content = '';
                if ($fieldType !== WidgetField::CHECKBOX) {
                    $content = "<label for='{$fieldId}' class='wide-title'>{$field->title}</label>";
                }
                switch ($fieldType) {
                    case WidgetField::TEXT:
                    case WidgetField::NUMBER:
                        $content .= self::getWidgetFormFieldText($fieldId, $fieldName, $fieldValue, $fieldType);
                        break;
                    case WidgetField::IMAGES:
                        $content .= self::getWidgetFormFieldImages($fieldId, $fieldName, $fieldValue);
                        break;
                    case WidgetField::IMAGES_WITH_URL:
                        $content .= self::getWidgetFormFieldImagesWithUrl($fieldId, $fieldName, $fieldValue);
                        break;
                    case WidgetField::CHECKBOX:
                        $content .= self::getWidgetFormFieldCheckbox($fieldId, $fieldName, $fieldValue, $field->title);
                        break;
                    case WidgetField::CHECKBOX_MULTIPLE:
                        $content .= self::getWidgetFormFieldCheckboxM($fieldId, $fieldName, $fieldValue, $field->options);
                        break;
                    case WidgetField::SELECT:
                        $content .= self::getWidgetFormFieldSelect($fieldId, $fieldName, $fieldValue, $field->options);
                        break;
                    case WidgetField::SELECT_MULTIPLE:
                        $content .= self::getWidgetFormFieldSelectM($fieldId, $fieldName, $fieldValue, $field->options);
                        break;
                    case WidgetField::RADIO:
                        $content .= self::getWidgetFormFieldRadio($fieldId, $fieldName, $fieldValue, $field->options);
                        break;
                }
                echo "<p>{$content}</p>";
            }
        }
    }

    private static function getWidgetFormFieldText($fieldId, $fieldName, $fieldValue, $fieldType)
    {
        $fieldValue = esc_attr($fieldValue);
        return "<input id='$fieldId' name='$fieldName' value='$fieldValue' type='$fieldType' class='widefat'>";
    }

    private static function getWidgetFormFieldImages($fieldId, $fieldName, $fieldValue)
    {
        $images = '';
        if (!empty($fieldValue)) {
            $attachmentIds = (array)$fieldValue;
            foreach ($attachmentIds as $attachmentId) {
                $attachmentUrl = wp_get_attachment_image_url($attachmentId, WPImages::FULL);
                $images .= "<img class='attachment-thumb' alt='attachment-thumb' src='{$attachmentUrl}'>
                    <input name='{$fieldName}[]' value='{$attachmentId}' type='hidden'>";
            }
        }
        $textSelect = __('Select');
        return "<p id='{$fieldId}-container' class='media-widget-preview'>{$images}</p>
        <button type='button' class='button add-media' onclick='addMedia(\"{$fieldId}\",\"{$fieldName}\")'>{$textSelect}</button>";
    }

    private static function getWidgetFormFieldImagesWithUrl($fieldId, $fieldName, $fieldValue)
    {
        $images = '';
        if (!empty($fieldValue)) {
            $attachmentIds = (array)$fieldValue;
            $textLink = __('Link');
            foreach ($attachmentIds as $attachmentId => $attachmentLink) {
                $attachmentUrl = wp_get_attachment_image_url($attachmentId, WPImages::FULL);
                $images .= "<p><label class='wide-title'><span class='fa fa-link fa-lg'></span>$textLink 
                <input name='{$fieldName}[{$attachmentId}]' value='{$attachmentLink}' data-id='{$attachmentId}' class='widefat'>
                <img class='attachment-thumb' alt='attachment-thumb' src='{$attachmentUrl}'></label></p>";
            }
        }
        $textSelect = __('Select');
        return "<p id='{$fieldId}-container' class='media-widget-preview'>{$images}</p>
        <button type='button' class='button add-media' onclick='addMedia(\"{$fieldId}\",\"{$fieldName}\")'>{$textSelect}</button>";
    }

    private static function getWidgetFormFieldCheckbox($fieldId, $fieldName, $fieldValue, $fieldTitle)
    {
        $optionChecked = checked($fieldValue, 1, false);
        return "<label for='{$fieldId}'>
        <input id='{$fieldId}' name='{$fieldName}' value='1' type='checkbox' {$optionChecked}>{$fieldTitle}</label>";
    }

    private static function getWidgetFormFieldCheckboxM($fieldId, $fieldName, $fieldValue, $fieldOptions)
    {
        $optionsContent = '';
        foreach ($fieldOptions as $optionKey => $optionName) {
            $optionChecked = checked(in_array($optionKey, (array)$fieldValue), true, false);
            $optionsId = "{$fieldId}-{$optionKey}";
            $optionsContent .= "<p><label for='{$optionsId}'>
            <input id='{$optionsId}' name='{$fieldName}[]' value='{$optionKey}' type='checkbox' {$optionChecked}>
            {$optionName}</label></p>";
        }
        return "<div>{$optionsContent}</div>";
    }

    private static function getWidgetFormFieldSelect($fieldId, $fieldName, $fieldValue, $fieldOptions)
    {
        $optionsContent = "";
        foreach ($fieldOptions as $optionKey => $optionName) {
            $optionSelected = selected($fieldValue, $optionKey, false);
            $optionsContent .= "<option value='{$optionKey}' {$optionSelected}>{$optionName}</option>";
        }
        return "<select id='{$fieldId}' name='{$fieldName}' class='ui dropdown'>{$optionsContent}</select>
        <script>initDropDown('#{$fieldId}')</script>";
    }

    private static function getWidgetFormFieldSelectM($fieldId, $fieldName, $fieldValue, $fieldOptions)
    {
        $countValues = 1;
        $textAll = __("All");
        $optionsContent = "<option value='' disabled hidden>{$textAll}</option>";
        foreach ($fieldOptions as $optionKey => $optionName) {
            if (is_array($optionName)) {
                $optionsContent .= "<optgroup label='{$optionKey}'>";
                foreach ($optionName as $key => $name) {
                    $optionSelected = selected(in_array($key, (array)$fieldValue), true, false);
                    $optionsContent .= "<option value='{$key}' {$optionSelected}>{$name}</option>";
                    $countValues++;
                }
                $optionsContent .= '</optgroup>';
            } else {
                $optionSelected = selected(in_array($optionKey, (array)$fieldValue), true, false);
                $optionsContent .= "<option value='{$optionKey}' {$optionSelected}>{$optionName}</option>";
            }
            $countValues++;
        }
        return "<select id='{$fieldId}' name='{$fieldName}[]' size='{$countValues}' class='ui dropdown' multiple>
        {$optionsContent}</select><script>initDropDown('#{$fieldId}')</script>";
    }

    private static function getWidgetFormFieldRadio($fieldId, $fieldName, $fieldValue, $fieldOptions)
    {
        $optionsContent = '';
        foreach ($fieldOptions as $optionKey => $optionName) {
            $optionChecked = checked(in_array($optionKey, (array)$fieldValue), true, false);
            $optionsContent .= "<p><label for='{$fieldId}-{$optionKey}'>
								<input type='radio' id='{$fieldId}-{$optionKey}' name='{$fieldName}' value='{$optionKey}' {$optionChecked}>{$optionName}</label></p>";
        }
        return "<div>$optionsContent</div>";
    }

    function hideTitleForEmptyContent(&$instance, $content)
    {
        if (empty($content)) {
            $instance[Widget::CUSTOM_TITLE] = "";
        }
    }
}