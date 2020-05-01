<?php /** Author: Vitali Lupu vitaliix@gmail.com*/

namespace wp;

class EtnikWines extends WpApp
{
    public function __construct()
    {

        parent::__construct();
    }

    public function setupTheme()
    {
        parent::setupTheme();
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

    function enqueueScriptsTheme()
    {
        parent::enqueueScriptsTheme();
        //wp_enqueue_style('button', $this->uriToLibs . 'button.css');
    }

    function initWidgetsForSidebar()
    {
        parent::initWidgetsForSidebar();
        //Unregister not used WP Widgets
        unregister_widget(\WP_Widget_Categories::class);
        unregister_widget(\WP_Widget_Tag_Cloud::class);
        unregister_widget(\WP_Widget_Recent_Posts::class);
        unregister_widget(\WP_Widget_Recent_Comments::class);
    }
}