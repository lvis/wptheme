<?php
/**
 * Created by Vitalie Lupu
 * User: lvis
 * Date: 2019-02-27
 * Time: 17:43
 */

namespace wp;


class PostEvent extends PostBase
{
    const TYPE = "portfolio";
    const TAXONOMY_CATEGORY = 'portfolio_filter';
    const META_BOX_LOCATION = "metaBoxEventLocation";
    const META_ADDRESS = "metaEventAddress";
    const META_ADDRESS_DESCRIPTION = "metaEventAddressDescription";
    const META_LOCATION = "metaEventLocation";

    public function registerPost()
    {
        $textEvent = 'Event';
        /** Register Taxonomy: Type */
        register_taxonomy(PostEvent::TAXONOMY_CATEGORY, [self::TYPE], [
            WPTaxonomyArguments::HIERARCHICAL => true,
            WPTaxonomyArguments::SHOW_UI => true,
            WPTaxonomyArguments::QUERY_VAR => true,
            WPTaxonomyArguments::REST_SHOW_IN => true,
            WPTaxonomyArguments::LABELS => self::getTaxonomyLabels('Category', 'Categories', $textEvent),
        ]);
        //'rewrite' => ['slug' => 'portfolio-item'],
        register_post_type(PostEvent::TYPE, [
            WPostArguments::HIERARCHICAL => false,
            WPostArguments::SHOW_UI => true,
            WPostArguments::QUERY_VAR => true,
            WPostArguments::IS_PUBLIC => true,
            WPostArguments::IS_PUBLIC_QUERY => true,
            WPostArguments::HAS_ARCHIVE => true,
            WPostArguments::MENU_ICON => 'dashicons-portfolio',
            WPostArguments::MENU_POSITION => 6,
            WPostArguments::LABELS => self::getPostLabels($textEvent, 'Events'),
            WPostArguments::REST_SHOW_IN => true,
            WPostArguments::REST_BASE => PostEvent::TYPE,
            WPostArguments::REST_CONTROLLER_CLASS => 'WP_REST_Posts_Controller',
            WPostArguments::SUPPORTS => [
                WPostSupport::TITLE,
                WPostSupport::EDITOR,
                WPostSupport::THUMBNAIL,
                WPostSupport::AUTHOR,
                WPostSupport::REVISIONS],
        ]);
        parent::registerPost();
    }

    public function handlePostColumns($columns)
    {
        self::arrayInsert($columns, 2, [
            PostEvent::TAXONOMY_CATEGORY => __("Categories")
        ]);

        return parent::handlePostColumns($columns);
    }

    public function handlePostColumnsContent($column, $postId)
    {
        switch ($column) {
            case PostEvent::TAXONOMY_CATEGORY:
                {
                    echo $this->getTaxonomyLinksOfTerms($postId, PostEvent::TAXONOMY_CATEGORY);
                    break;
                }
        }
        parent::handlePostColumnsContent($column, $postId);
    }

    public function registerPostMetaBoxes($meta_boxes)
    {
        //[MAP]
        $textEventLocation = __("Event Location", "framework");
        $textAddress = __("Address");
        $textAddressDescription = __("Address Name");
        $mapKey = get_option(Customizer::GOOGLE_MAP_API) ? get_option(Customizer::GOOGLE_MAP_API) : "";
        //$siteAddress = get_option(Customizer::SITE_ADDRESS);
        //$siteAddress = apply_filters('translate_text', $siteAddress);
        $siteAddress = "Park Avenue, Moscow Oblast, Russia, 143582";//Park Avenue, Moscow Oblast, Russia, 143582 //Новорижское ш., Москва, Московская обл., 143082
        $meta_boxes[] = [
            MetaBox::ID => PostEvent::META_BOX_LOCATION,
            MetaBox::POST_TYPES => [PostEvent::TYPE],
            MetaBox::TITLE => $this->getIcon("fa-map-marked-alt") . $textEventLocation,
            MetaBox::CONTEXT => MetaBoxContext::NORMAL,
            MetaBox::PRIORITY => MetaBoxPriority::LOW,
            MetaBox::FIELDS => [
                [
                    MetaBoxFieldInput::ID => PostEvent::META_ADDRESS_DESCRIPTION,
                    MetaBoxFieldInput::TYPE => MetaBoxFieldType::TEXTAREA,
                    MetaBoxFieldInput::COLUMNS => 12,
                    MetaBoxFieldInput::STD => $siteAddress,
                    MetaBoxFieldInput::NAME => $this->getIcon("fa-map-marker-edit") . $textAddressDescription,
                    MetaBoxFieldInput::PLACEHOLDER => __('Place here the Address name translation'),
                ],
                //TODO Set Parameter in Site configuration default Address for Google Map
                [
                    MetaBoxFieldInput::ID => PostEvent::META_ADDRESS,
                    MetaBoxFieldInput::TYPE => MetaBoxFieldType::TEXT,
                    MetaBoxFieldInput::COLUMNS => 12,
                    MetaBoxFieldInput::STD => $siteAddress,
                    MetaBoxFieldInput::NAME => $this->getIcon("fa-map-marker-alt") . $textAddress,
                    MetaBoxFieldInput::PLACEHOLDER => $textAddress,
                ],
                //TODO Set Parameter in Site configuration default location Coordinates and get them from there
                [
                    MetaBoxFieldMap::ID => PostEvent::META_LOCATION,
                    MetaBoxFieldMap::TYPE => MetaBoxFieldType::MAP,
                    MetaBoxFieldMap::ADDRESS_FIELD => PostEvent::META_ADDRESS,
                    MetaBoxFieldMap::NAME => $this->getIcon("fa-map") . __('Map'),
                    MetaBoxField::COLUMNS => 12,
                    MetaBoxFieldMap::STD => '',
                    MetaBoxFieldMap::API_KEY => $mapKey,
                ],
            ],
        ];
        return parent::registerPostMetaBoxes($meta_boxes);
    }
}