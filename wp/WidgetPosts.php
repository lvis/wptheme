<?php namespace wp;
final class WidgetPosts extends Widget {
    const TYPE = 'widgetPostsType';
    const SORT_CRITERIA = "sortCriteria";
    const SORT_RANDOM = "sortRandom";
    const SORT_DATE_ASC = "sortDateAsc";
    const SORT_DATE_DESC = "sortDateDesc";
    const LAYOUT = "layout";
    const LAYOUT_LIST = "layoutList";
    const LAYOUT_GRID = "layoutGrid";
    const CHANGE_CONTENT_BY_PAGE = 'widgetPostsChangeContentByPage';

    function __construct() {
        parent::__construct(__('Posts'));
    }

    function initFields() {
        $posts = Widget::getPagesOfPosts();
        $this->addField(new WidgetField(WidgetField::CHECKBOX, self::CHANGE_CONTENT_BY_PAGE, __('Change content by page type'), [], false));
        $this->addField(new WidgetField(WidgetField::SELECT, self::TYPE, __('Content Type'), $posts, PostBase::TYPE));
        $this->addField(new WidgetField(WidgetField::SELECT, self::SORT_CRITERIA, __('Show first the', WpApp::TEXT_DOMAIN), $this->getSortingCriteria(), self::SORT_RANDOM));
        $this->addField(new WidgetField(WidgetField::NUMBER, QueryPost::PER_PAGE, __('Show only'), [], 3));
        $field = new WidgetField(WidgetField::SELECT, self::LAYOUT, __('Layout'), [self::LAYOUT_LIST => __('List'),
                                                                                   self::LAYOUT_GRID => __('Grid'),], self::LAYOUT_LIST);
        $this->addField($field);
        parent::initFields();
    }

    function getPostOrderByMeta($sortCriteria, $defaultReturnSortCriteria = true) {
        if ($defaultReturnSortCriteria == false) {
            $sortCriteria = "";
        }
        return $sortCriteria;
    }

    function getPostOrder($sortCriteria, $defaultReturnSortCriteria = true) {
        switch ($sortCriteria) {
            case self::SORT_RANDOM:
            {
                break;
            }
            case self::SORT_DATE_DESC:
            {
                $sortCriteria = WPOrder::DESC;
                break;
            }
            default:
            {
                if ($defaultReturnSortCriteria == false) {
                    $sortCriteria = "";
                }
                break;
            }
        }
        return $sortCriteria;
    }

    function getPostOrderBy($sortCriteria, $defaultReturnSortCriteria = true) {
        switch ($sortCriteria) {
            case self::SORT_RANDOM:
            {
                $sortCriteria = WPOrderBy::RANDOM;
                break;
            }
            case self::SORT_DATE_DESC:
            {
                $sortCriteria = WPOrderBy::DATE;
                break;
            }
            default:
            {
                if ($defaultReturnSortCriteria == false) {
                    $sortCriteria = "";
                }
                break;
            }
        }
        return $sortCriteria;
    }

    function getCurrentSortCriteria() {
        return isset($_GET[self::SORT_CRITERIA]) ? $_GET[self::SORT_CRITERIA] : self::SORT_DATE_DESC;
    }

    protected $sortingCriteria;

    public function getSortingCriteria() {
        $this->sortingCriteria = [self::SORT_RANDOM    => __('Random'),
                                  self::SORT_DATE_DESC => __('Recent', WpApp::TEXT_DOMAIN),];
        return $this->sortingCriteria;
    }

    function widget($args, $instance) {
        $content = '';
        $customTitle = self::getInstanceValue($instance, self::CUSTOM_TITLE, $this);
        $postType = self::getInstanceValue($instance, self::TYPE, $this);
        $sortCriteria = self::getInstanceValue($instance, self::SORT_CRITERIA, $this);
        $queryArgs = [QueryPost::TYPE     => $postType,
                      QueryPost::ORDER_BY => $this->getPostOrderBy($sortCriteria, false),
                      QueryPost::ORDER    => $this->getPostOrder($sortCriteria, false),];
        if ($postType == WPostTypes::ATTACHMENT) {
            $queryArgs [QueryPost::STATUS] = WPostStatus::INHERIT;
        }
        $postsCount = intval(self::getInstanceValue($instance, QueryPost::PER_PAGE, $this));
        $changeContentByPage = intval(self::getInstanceValue($instance, self::CHANGE_CONTENT_BY_PAGE, $this));
        if ($changeContentByPage && (is_category() || is_tax() || is_archive() || is_tag() || is_home())) {
            $postsCount = -1;
            /** @var $currentTax \WP_Term */
            $currentTax = get_queried_object();
            $currentTaxIsTerm = is_a($currentTax, 'WP_Term');
            if ($currentTaxIsTerm && $currentTax->term_id > 0) {
                $queryArgs[QueryTaxonomy::DEFINITION] = [QueryTaxonomy::RELATION => QueryRelations::_AND,
                                                         [QueryTaxonomy::NAME  => $currentTax->taxonomy,
                                                          QueryTaxonomy::TERMS => $currentTax->term_id]];
            }
            if ($customTitle == '') {
                if (is_home() && !is_front_page()) {
                    $customTitle = get_the_title(get_option('page_for_posts', true));
                } else {
                    if ($currentTaxIsTerm) {
                        $titlePrefix = get_term_parents_list($currentTax->term_id, 'category', ['inclusive' => false,
                                                                                                'separator' => ' / ']);
                        $args[WPSidebar::BEFORE_TITLE_ADDITION] = $titlePrefix;
                        $customTitle = single_term_title('', false);
                    }
                }
            }
        }
        if ($customTitle == '') {
            $currentPostType = get_post_type_object($postType);
            if ($currentPostType) {
                $customTitle = $currentPostType->labels->name;
            }
        }
        $queryArgs[QueryPost::PER_PAGE] = $postsCount;
        $layoutType = self::getInstanceValue($instance, self::LAYOUT, $this);
        $templatePath = UtilsWp::locatePostTemplate(strtolower($postType), $layoutType, __DIR__);
        $content .= UtilsWp::renderTemplate($queryArgs, $templatePath, $postsCountResult);
        $queryArgs[QueryPost::PER_PAGE] = -1;
        $postsQuery = new \WP_Query($queryArgs);
        if ($postsCount > 0 && $postsCountResult < $postsQuery->post_count) {
            $linkToCategory = get_post_type_archive_link($postType);
            if (!empty($linkToCategory)) {
                $textViewAll = __('See All');
                $args[WPSidebar::BEFORE_TITLE_ADDITION] = "<a href='{$linkToCategory}' title='{$textViewAll}'>";
                $args[WPSidebar::AFTER_TITLE_ADDITION] = "</a>";
            }
        }
        $instance[Widget::CUSTOM_TITLE] = $customTitle;
        $args[WPSidebar::CONTENT] = $content;
        parent::widget($args, $instance);
    }
}