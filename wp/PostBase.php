<?php namespace wp;

/** Author: Vitali Lupu <vitaliix@gmail.com> */
abstract class PostBase
{
    const TYPE = 'post';
    const META_BOX_MEDIA = 'metaBoxPostGallery';
    const META_VIDEOS = 'postVideo';
    const META_GALLERY = 'postGallery';
    const VIEW_COUNT = 'postViewCount';
    static $counter = 0;
    protected static $instances = [];

    protected $enableQuickEdit = false;
    protected $hasThumbnail = true;
    private $type = PostBase::TYPE;

    private function __construct() {
        $this->type = static::TYPE;
        add_action(WPActions::INIT, [$this, 'registerPost']);
        add_action(WPActions::ENQUEUE_SCRIPTS_ADMIN, [$this, 'enqueueScriptsAdmin']);
        add_action(WPActions::RESTRICT_MANAGE_POSTS, [$this, 'createTaxonomyFilter'], 10);
        //https://codex.wordpress.org/Plugin_API/Filter_Reference/wp_insert_post_data
        add_filter('wp_insert_post_data', [$this, 'handlePostSaveBefore'], 99, 2);
        add_filter("manage_{$this->type}_posts_custom_column", [$this, 'handlePostColumnsContent'], 10, 2);
        add_filter("manage_edit-{$this->type}_columns", [$this, "handlePostColumns"]);
        add_filter("manage_edit-{$this->type}_sortable_columns", [$this, "handlePostColumnsSortable"]);
        add_filter(MetaBoxFilter::REGISTER, [$this, 'registerPostMetaBoxes']);
        //add_filter('post_row_actions', [$this, 'handlePostRowActions'], 10, 2);//Hide Quick Edit Link for Posts
        //add_filter('page_row_actions', [$this, 'handlePostRowActions'], 10, 2);//Hide Quick Edit Link for Pages
        add_filter("bulk_actions-edit-{$this->type}", [$this, 'handlePostBulkActions'], 10, 2);
        add_action('transition_post_status', [$this, 'handlePostStatusChange'], 10, 3);
        if (WPUsers::isSiteEditor() == false) {
            add_filter("get_user_option_screen_layout_{$this->type}", [$this, 'handleUserScreenLayout']);
        }
    }

    final public static function i() {
        if (!isset(static::$instances[static::TYPE])) {
            static::$instances[static::TYPE] = new static();
        }
        return static::$instances[static::TYPE];
    }

    final public static function getPostViews($postID) {
        //TODO Add a statistics mechanism to Project Without external dependency
        $count = 0;
        $countKey = PostBase::VIEW_COUNT;
        $metaCount = get_post_meta($postID, $countKey, true);
        $getPost = 'pvc_get_post_views';
        $hasStatisticsPlugin = function_exists($getPost);//do_shortcode('[post-views]')
        if ($hasStatisticsPlugin) {
            $count = $getPost(get_the_ID());
        }
        if ($metaCount == '') {
            //delete_post_meta($postID, $countKey);
            if ($hasStatisticsPlugin == false) {
                $count++;
            }
            add_post_meta($postID, $countKey, $count);
        }
        update_post_meta($postID, $countKey, $count);
        return $count;
    }

    static function getTaxonomyLabels($name, $namePlural) {
        return [WPTaxonomyLabels::NAME_IN_MENU => $namePlural,
            WPTaxonomyLabels::NAME_PLURAL => $namePlural,
            WPTaxonomyLabels::NAME_SINGULAR => $name,
            WPTaxonomyLabels::CHOSE_FROM_MOST_USED => __("Choose from the most used $namePlural"),
            WPTaxonomyLabels::ITEM_ADD_NEW => __("Add"),
            WPTaxonomyLabels::ITEM_NEW_NAME => __("New $name Name"),
            WPTaxonomyLabels::ITEM_EDIT => sprintf(__("Edit &#8220;%s&#8221;"), $name),
            WPTaxonomyLabels::ITEM_VIEW => sprintf(__('View &#8220;%s&#8221;'), $name),
            WPTaxonomyLabels::ITEM_UPDATE => __("Update $name"),
            WPTaxonomyLabels::ITEM_PARENT => __("Parent $name"),
            WPTaxonomyLabels::ITEM_PARENT_COLON => __("Parent $name:"),
            WPTaxonomyLabels::ITEMS_ALL => sprintf(__('All %s', WpApp::TEXT_DOMAIN), $namePlural),
            WPTaxonomyLabels::ITEMS_SEARCH => __("Search $namePlural"),
            WPTaxonomyLabels::ITEMS_POPULAR => __("Popular $namePlural"),
            WPTaxonomyLabels::ITEMS_SEPARATE_WITH_COMMAS => __("Separate $namePlural with commas"),
            WPTaxonomyLabels::ITEMS_ADD_OR_REMOVE => __("Add or remove $namePlural")];
    }

    static function getPostLabels($name, $namePlural) {
        $textAdd = sprintf(__('Add'), $name);
        return [WPostLabels::NAME_PLURAL => $namePlural,
            WPostLabels::NAME_SINGULAR => $name,
            WPostLabels::ADD_NEW => $textAdd,
            WPostLabels::ITEM_ADD_NEW => $textAdd,
            WPostLabels::ITEMS_ALL => sprintf(__('All %s', WpApp::TEXT_DOMAIN), $namePlural),
            WPostLabels::ITEM_EDIT => sprintf(__("Edit &#8220;%s&#8221;"), $name),
            WPostLabels::ITEM_NEW => __("New $name"),
            WPostLabels::ITEM_VIEW => sprintf(__('View &#8220;%s&#8221;'), $name),
            WPostLabels::ITEMS_SEARCH => __("Search $name"),
            WPostLabels::NOT_FOUND => __("No $name found"),
            WPostLabels::NOT_FOUND_IN_TRASH => __("No $name found in Trash"),
            WPostLabels::ITEM_PARENT_COLON => ''];
    }

    // function to display number of posts.
    function handlePostStatusChange($new_status, $old_status, $post) {
        if ($new_status == WPostStatus::PUBLISH && $old_status == WPostStatus::AUTO_DRAFT) {
            // the post is inserted
            $this->handlePostInsert($post);
        } else {
            if ($new_status == WPostStatus::PUBLISH && $old_status != WPostStatus::PUBLISH) {
                // the post is published
                $this->handlePostPublish($post);
            } else {
                // the post is updated
                $this->handlePostUpdate($post);
            }
        }
    }

    function handlePostInsert($post) {
    }

    function handlePostPublish($post) {
    }

    function handlePostUpdate($post) {
    }

    public function registerPost() {
    }

    /** Load Styles & Scripts for: Backend*/
    function enqueueScriptsAdmin() {
    }

    /**
     * https://codex.wordpress.org/Plugin_API/Filter_Reference/wp_insert_post_data
     * @param $data
     * @param $postFields
     * @return mixed
     */
    public function handlePostSaveBefore($data, $postFields) {
        return $data;
    }

    public function handlePostRowActions($actions) {
        if ($this->enableQuickEdit == false) {
            unset($actions['inline hide-if-no-js']);
        }
        return $actions;
    }

    function handlePostBulkActions($actions) {
        //unset( $actions['inline'] );
        return $actions;
    }

    public function handlePostColumns($columns) {
        /*if ($this->hasThumbnail) {
            $textImage = self::getIcon('image');
            self::arrayInsert($columns, 1, [self::COLUMN_THUMB => $textImage]);
        }*/
        $handler = [$this, 'handlePostTitle'];
        if (has_filter('display_post_states', $handler) == false) {
            add_filter('display_post_states', $handler, 10, 2);
        }
        return $columns;
    }

    function handlePostTitle($post_states, $post) {
        $postId = $post->ID;
        if (has_post_thumbnail($postId)) {
            $post = get_post($postId);
            $postThumbnail = get_the_post_thumbnail($post, [48, 48]);
            $postLink = get_the_permalink();
            $thumb = "<a href='{$postLink}' target='_blank' class='post-row-thumb'>{$postThumbnail}</a>";
            echo $thumb;
            //remove_filter('display_post_states', [$this, 'handlePostTitle']);
        }
        return $post_states;
    }

    static function arrayInsert(&$array, $position, $insert_array) {
        $first_array = array_splice($array, 0, $position);
        $array = array_merge($first_array, $insert_array, $array);
    }

    public function handlePostColumnsSortable($columns) {
        return $columns;
    }

    public function handlePostColumnsContent($column, $postId) {
        /*switch ($column) {
            case self::COLUMN_THUMB:
            {
                $thumb = __('No Thumbnail', WpApp::TEXT_DOMAIN);
                if (has_post_thumbnail($postId)) {
                    $post = get_post($postId);
                    $postLink = get_the_permalink();
                    $postThumbnail = get_the_post_thumbnail($post, [108, 73]);
                    $thumb = "<a href='{$postLink}' target='_blank'>{$postThumbnail}</a>";
                }
                echo $thumb;
                break;
            }
        }*/
    }

    public function registerPostMetaBoxes($meta_boxes) {
        $textMedia = self::getIcon("photo-video") . __('Media');
        $textImages = self::getIcon("images") . __('Images');
        $textVideo = self::getIcon("video-plus") . __('Video');
        $meta_boxes[] = [MetaBox::ID => PostBase::META_BOX_MEDIA,
            MetaBox::POST_TYPES => [$this->type],
            MetaBox::TITLE => $textMedia,
            MetaBox::CONTEXT => MetaBoxContext::NORMAL,
            MetaBox::PRIORITY => MetaBoxPriority::HIGH,
            MetaBox::FIELDS => [
                [
                    MetaBoxFieldImage::TYPE => MetaBoxFieldType::IMAGE_ADVANCED,
                    MetaBoxFieldImage::ID => PostBase::META_GALLERY,
                    MetaBoxFieldImage::NAME => $textImages,
                    MetaBoxFieldImage::COLUMNS => 12,
                    MetaBoxFieldImage::MAX_UPLOADS => 50,
                    MetaBoxFieldImage::MAX_STATUS => true,
                ],
                [
                    MetaBoxFieldFileAdvanced::TYPE => MetaBoxFieldType::VIDEO,
                    MetaBoxFieldFileAdvanced::ID => PostBase::META_VIDEOS,
                    MetaBoxFieldFileAdvanced::NAME => $textVideo,
                    MetaBoxFieldFileAdvanced::COLUMNS => 12,
                    MetaBoxFieldFileAdvanced::MAX_UPLOADS => 10,
                    MetaBoxFieldFileAdvanced::MAX_STATUS => true,
                ]
            ]
        ];
        return $meta_boxes;
    }

    static function getIcon($data) {
        return "<i class='far fa-$data' aria-hidden='true'></i> ";
    }

    /**
     * Comma separated taxonomy terms with admin side links
     * @param $postId
     * @param $taxonomyName
     * @return string
     */
    function getTaxonomyLinksOfTerms($postId, $taxonomyName) {
        $terms = get_the_terms($postId, $taxonomyName);
        $result = '';
        if (!empty ($terms)) {
            $links = [];
            /* Loop through each term, linking to the 'edit posts' page for the specific term. */
            foreach ($terms as $term) {
                $termLink = add_query_arg([QueryPost::TYPE => $this->type,
                    $taxonomyName => $term->slug,], 'edit.php');
                $escTermLink = esc_url($termLink);
                $termName = sanitize_term_field('name', $term->name, $term->term_id, $taxonomyName, 'display');
                $escTermName = esc_html($termName);
                $links[] = "<a href='{$escTermLink}'>{$escTermName}</a>";
            }
            $result = join(', ', $links);
        }
        return $result;
    }

    /**
     * Filter the request to just give posts for the given taxonomy, if applicable.
     * @link https://developer.wordpress.org/reference/hooks/restrict_manage_posts/
     * @link https://wp-kama.ru/hook/restrict_manage_posts
     * @param string $postType The post type slug.
     * @return array
     */
    function createTaxonomyFilter($postType) {
        $taxonomies = [];
        if (isset(self::$instances[$postType])) {
            $taxonomies = get_object_taxonomies($postType, 'objects');
            //TODO Don't show empty taxonomy
            foreach ($taxonomies as $taxonomy) {
                $selectedTaxonomy = "";
                if (isset($_GET[$taxonomy->name])) {
                    $selectedTaxonomy = $_GET[$taxonomy->name];
                }
                $listOfCategories = [
                    'show_option_all' => $taxonomy->labels->all_items,
                    'name' => $taxonomy->name,
                    'taxonomy' => $taxonomy->name,
                    'selected' => $selectedTaxonomy,
                    'hierarchical' => $taxonomy->hierarchical,
                    'value_field' => 'slug',
                    'orderby' => 'name',
                    'show_count' => WPUsers::isSiteEditor(),
                    'hide_empty' => true,
                    'hide_if_empty' => true
                ];
                wp_dropdown_categories($listOfCategories);
            };
            if (WPUsers::isSiteEditor()) {
                $currentAuthor = isset($_GET['author']) ? $_GET['author'] : '';
                $listOfUsers = [
                    'show_option_all' => __('All Authors', WpApp::TEXT_DOMAIN),
                    'name' => 'author',
                    'selected' => $currentAuthor
                ];
                wp_dropdown_users($listOfUsers);
            }
        }
        return $taxonomies;
    }

    function getType() {
        return $this->type;
    }

    function handleUserScreenLayout() {
        return 1;
    }
}