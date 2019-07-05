<?php

namespace wp;

class RealEstate extends WpApp
{
    public function __construct()
    {
        parent::__construct();
        add_filter(MetaBoxFilter::REGISTER, [$this, 'registerMetaBoxesForUser']);
        //Temporary solution to show in builder in Display Conditions view users without posts
        add_action('pre_get_users', [$this, 'handlePreGetUsers']);
    }

    function handlePreGetUsers(\WP_User_Query $query)
    {
        if ($query->query_vars['who'] == 'authors') {
            $query->query_vars['has_published_posts'] = false;
        }
        return $query;
    }

    function enqueueScriptsTheme()
    {
        parent::enqueueScriptsTheme();
        wp_enqueue_style('mayfaiclub', $this->uriToLibs . 'mayfaiclub.css');
    }

    public function registerMetaBoxesForUser($meta_boxes)
    {
        $textPhotos = ucfirst(__('photos'));
        $textVideos = __('Videos');
        $idTabInfo = 'information';
        $idTabPortfolio = 'portfolio';
        $meta_boxes[] = [
            MetaBox::TITLE => '',
            MetaBox::TYPE => 'user',
            MetaBox::FIELDS => [
                [
                    MetaBoxField::ID => 'phone',
                    MetaBoxField::NAME => __('Mobile Phone'),
                    MetaBoxField::TYPE => MetaBoxFieldType::TEXT,
                ],
            ],
        ];
        return $meta_boxes;
    }
}