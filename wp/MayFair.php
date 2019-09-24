<?php namespace wp;
/** Author: Vitalie Lupu */
class MayFair extends WpApp {
    public function __construct() {
        parent::__construct();
        add_filter(MetaBoxFilter::REGISTER, [$this, 'registerMetaBoxesForUser']);
    }

    function enqueueScriptsTheme() {
        parent::enqueueScriptsTheme();
        wp_enqueue_style('mayfaiclub', $this->uriToLibs . 'mayfair.css');
    }

    public function registerMetaBoxesForUser($meta_boxes) {
        $textPhotos = ucfirst(__('photos'));
        $textVideos = __('Videos');
        $idTabInfo = 'information';
        $idTabPortfolio = 'portfolio';
        $meta_boxes[] = [
            MetaBox::TITLE  => '',
            MetaBox::TYPE   => 'user',
            'tabs'          => [
                $idTabPortfolio => [
                    'label' => __('Portfolio'),
                    'icon'  => 'dashicons-admin-media',
                ],
                $idTabInfo      => [
                    'label' => __('General Info'),
                    'icon'  => 'dashicons-info',
                ],
            ],
            'tab_style'     => 'left',
            'tab_wrapper'   => false,
            MetaBox::FIELDS => [
                [
                    'tab'              => $idTabInfo,
                    MetaBoxField::ID   => 'skills',
                    MetaBoxField::NAME => 'Специализация',
                    MetaBoxField::TYPE => MetaBoxFieldType::TEXTAREA,
                ],
                [
                    'tab'              => $idTabInfo,
                    MetaBoxField::ID   => 'study',
                    MetaBoxField::NAME => 'Образование',
                    MetaBoxField::TYPE => MetaBoxFieldType::TEXT,
                ],
                [
                    'tab'              => $idTabInfo,
                    MetaBoxField::ID   => 'experience',
                    MetaBoxField::NAME => 'Опыт и достижения',
                    MetaBoxField::TYPE => MetaBoxFieldType::TEXT,
                ],
                [
                    'tab'              => $idTabInfo,
                    MetaBoxField::ID   => 'certificate',
                    MetaBoxField::NAME => 'Сертификаты и документы',
                    MetaBoxField::TYPE => MetaBoxFieldType::IMAGE_ADVANCED,
                ],
                [
                    'tab'              => $idTabPortfolio,
                    MetaBoxField::ID   => 'photos',
                    MetaBoxField::NAME => $textPhotos,
                    MetaBoxField::TYPE => MetaBoxFieldType::IMAGE_ADVANCED,
                ],
                [
                    'tab'              => $idTabPortfolio,
                    MetaBoxField::ID   => 'videos',
                    MetaBoxField::NAME => $textVideos,
                    MetaBoxField::TYPE => MetaBoxFieldType::VIDEO,
                ],
            ],
        ];
        return $meta_boxes;
    }
}