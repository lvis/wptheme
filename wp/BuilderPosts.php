<?php namespace wp;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class BuilderPosts extends Widget_Base {
    const TYPE = 'widgetPostsType';
    const SORT_CRITERIA = "sortCriteria";
    const SORT_RANDOM = "sortRandom";
    const SORT_DATE_ASC = "sortDateAsc";
    const SORT_DATE_DESC = "sortDateDesc";
    const LAYOUT = "layout";
    const LAYOUT_LIST = "layoutList";
    const LAYOUT_GRID = "layoutGrid";
    const CHANGE_CONTENT_BY_PAGE = 'widgetPostsChangeContentByPage';

    public function get_name() {
        return 'builderPosts';
    }

    public function get_title() {
        return __('Posts');
    }

    public function get_icon() {
        return 'fa fa-star';
    }

    public function get_categories() {
        return [BuilderWidget::CATEGORY_BASIC];
    }

    public function get_keywords() {
        return ['post', 'posts', 'gallery', 'blog', 'news', 'pages'];
    }
    /*public function get_script_depends() {
        return [ 'imagesloaded', 'tilt', 'bdt-uikit-icons' ];
    }*/
    /*public function _register_skins() {
        $this->add_skin( new Skins\Skin_Abetis( $this ) );
        $this->add_skin( new Skins\Skin_Fedara( $this ) );
        $this->add_skin( new Skins\Skin_Trosia( $this ) );
    }*/
    protected function _register_controls() {
        //==================================== [Section: Layout]
        $sectionOptions = [BuilderControlSection::TAB   => Controls_Manager::TAB_CONTENT,
                           BuilderControlSection::LABEL => __('Content')];
        $this->start_controls_section(BuilderControlSection::ID_CONTENT, $sectionOptions);
        $controlOptions = [BuilderControl::TYPE        => Controls_Manager::SELECT,
                           BuilderControl::LABEL       => __('Content Type'),
                           BuilderControl::DEFAULT     => PostBase::TYPE,
                           BuilderControl::OPTIONS     => Widget::getPagesOfPosts(),
                           BuilderControl::IN_FRONTEND => true];
        $this->add_responsive_control(self::TYPE, $controlOptions);
        $controlOptions = [BuilderControl::TYPE        => Controls_Manager::SELECT,
                           BuilderControl::LABEL       => __('Show first the', WpApp::TEXT_DOMAIN),
                           BuilderControl::DEFAULT     => self::SORT_RANDOM,
                           BuilderControl::OPTIONS     => [self::SORT_RANDOM    => __('Random'),
                                                           self::SORT_DATE_DESC => __('Recent', WpApp::TEXT_DOMAIN)],
                           BuilderControl::IN_FRONTEND => true];
        $this->add_responsive_control(self::SORT_CRITERIA, $controlOptions);
        $controlOptions = [BuilderControl::TYPE    => Controls_Manager::NUMBER,
                           BuilderControl::LABEL   => __('Show only'),
                           BuilderControl::DEFAULT => 3];
        $this->add_control(QueryPost::PER_PAGE, $controlOptions);
        $controlOptions = [BuilderControl::TYPE  => Controls_Manager::SWITCHER,
                           BuilderControl::LABEL => __('Show all on archive page')];
        $this->add_control(self::CHANGE_CONTENT_BY_PAGE, $controlOptions);
        $this->end_controls_section();
        //==================================== [Section: Layout]
        $sectionOptions = [BuilderControlSection::TAB   => Controls_Manager::TAB_CONTENT,
                           BuilderControlSection::LABEL => __('Layout')];
        $this->start_controls_section(BuilderControlSection::ID_LAYOUT, $sectionOptions);
        $controlOptions = [BuilderControl::TYPE        => Controls_Manager::SELECT,
                           BuilderControl::LABEL       => __('Layout'),
                           BuilderControl::DEFAULT     => self::LAYOUT_GRID,
                           BuilderControl::OPTIONS     => [self::LAYOUT_LIST => __('List'),
                                                           self::LAYOUT_GRID => __('Grid')],
                           BuilderControl::IN_FRONTEND => true];
        $this->add_responsive_control(self::LAYOUT, $controlOptions);
        $this->end_controls_section();
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

    protected function render() {
        $settings = $this->get_settings_for_display();
        $content = '';
        $postType = $settings[self::TYPE];
        $sortCriteria = $settings[self::SORT_CRITERIA];
        $queryArgs = [QueryPost::TYPE     => $postType,
                      QueryPost::ORDER_BY => $this->getPostOrderBy($sortCriteria, false),
                      QueryPost::ORDER    => $this->getPostOrder($sortCriteria, false),];
        if ($postType == WPostTypes::ATTACHMENT) {
            $queryArgs [QueryPost::STATUS] = WPostStatus::INHERIT;
        }
        $postsCount = intval($settings[QueryPost::PER_PAGE]);
        $changeContentByPage = intval($settings[self::CHANGE_CONTENT_BY_PAGE]);
        $customTitle = '';
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
        $layoutType = $settings[self::LAYOUT];
        $templatePath = UtilsWp::locatePostTemplate(strtolower($postType), $layoutType, __DIR__);
        $content .= UtilsWp::renderTemplate($queryArgs, $templatePath, $postsCountResult);
        $queryArgs[QueryPost::PER_PAGE] = -1;
        $postsQuery = new \WP_Query($queryArgs);
        $customTitle = "<h2 class='widgettitle'>{$customTitle}</h2>";
        if ($postsCount > 0 && $postsCountResult < $postsQuery->post_count) {
            $linkToCategory = get_post_type_archive_link($postType);
            if (empty($linkToCategory) == false) {
                $textViewAll = __('See All');
                $customTitle = "<a href='{$linkToCategory}' title='{$textViewAll}'>{$customTitle}</a>";
            }
        }
        echo $customTitle . $content;
    }
}