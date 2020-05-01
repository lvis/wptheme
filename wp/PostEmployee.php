<?php namespace wp;
/** Author: Vitali Lupu <vitaliix@gmail.com> */
class PostEmployee extends PostBase {
    const TYPE = "employee";
    const MENU_ICON = 'dashicons-groups';
    const TAXONOMY_CATEGORY = 'employee-category';
    const TAXONOMY_LOCATION = 'employee-location';
    const TAXONOMY_TAG = 'employee-tag';
    const META_BOX_EMPLOYEE_INFO = 'metaBoxEmployeeInfo';
    const META_CERTIFICATES = 'certificates';
    const META_EXPERIENCE = 'experience';
    const META_LOCATION = 'location';
    const META_EDUCATION = 'education';
    //const META_SPECIALIZATION = 'specialization';

    public function registerPost() {
        /** Register Taxonomy: Job Title */
        $textCategory = __('Job Title', WpApp::TEXT_DOMAIN);
        $textCategories = __('Job Titles', WpApp::TEXT_DOMAIN);
        register_taxonomy(self::TAXONOMY_CATEGORY, [self::TYPE], [WPTaxonomyArguments::HIERARCHICAL => false,
            WPTaxonomyArguments::SHOW_UI => true,
            WPTaxonomyArguments::QUERY_VAR => true,
            WPTaxonomyArguments::REST_SHOW_IN => true,
            WPTaxonomyArguments::LABELS => self::getTaxonomyLabels($textCategory, $textCategories)]);
        $textTag = __('Tag');
        $textTags = __('Tags');
        register_taxonomy(self::TAXONOMY_TAG, [self::TYPE], [WPTaxonomyArguments::HIERARCHICAL => false,
            WPTaxonomyArguments::SHOW_UI => true,
            WPTaxonomyArguments::QUERY_VAR => true,
            WPTaxonomyArguments::REST_SHOW_IN => true,
            WPTaxonomyArguments::LABELS => self::getTaxonomyLabels($textTag, $textTags)]);
        $textLocation = __('Location', WpApp::TEXT_DOMAIN);
        $textLocations = __('Locations', WpApp::TEXT_DOMAIN);
        register_taxonomy(self::TAXONOMY_LOCATION, [self::TYPE], [WPTaxonomyArguments::HIERARCHICAL => false,
            WPTaxonomyArguments::SHOW_UI => true,
            WPTaxonomyArguments::QUERY_VAR => true,
            WPTaxonomyArguments::REST_SHOW_IN => true,
            WPTaxonomyArguments::LABELS => self::getTaxonomyLabels($textLocation, $textLocations)]);
        $textEvent = __('Employee', WpApp::TEXT_DOMAIN);
        $textEvents = __('Employees', WpApp::TEXT_DOMAIN);
        register_post_type(self::TYPE, [WPostArguments::HIERARCHICAL => false,
            WPostArguments::SHOW_UI => true,
            WPostArguments::QUERY_VAR => true,
            WPostArguments::IS_PUBLIC => true,
            WPostArguments::IS_PUBLIC_QUERY => true,
            WPostArguments::HAS_ARCHIVE => true,
            WPostArguments::MENU_ICON => self::MENU_ICON,
            WPostArguments::MENU_POSITION => 6,
            WPostArguments::LABELS => self::getPostLabels($textEvent, $textEvents),
            WPostArguments::REST_SHOW_IN => true,
            WPostArguments::REST_BASE => self::TYPE,
            WPostArguments::REST_CONTROLLER_CLASS => 'WP_REST_Posts_Controller',
            WPostArguments::SUPPORTS => [WPostSupport::TITLE,
                WPostSupport::EDITOR,
                WPostSupport::THUMBNAIL,
                WPostSupport::AUTHOR,
                WPostSupport::REVISIONS],]);
        parent::registerPost();
    }

    public function handlePostColumns($columns) {
        self::arrayInsert($columns, 2, [self::TAXONOMY_CATEGORY => __("Categories")]);
        return parent::handlePostColumns($columns);
    }

    public function handlePostColumnsContent($column, $postId) {
        switch ($column) {
        case self::TAXONOMY_CATEGORY:
        {
            echo $this->getTaxonomyLinksOfTerms($postId, self::TAXONOMY_CATEGORY);
            break;
        }
        }
        parent::handlePostColumnsContent($column, $postId);
    }

    public function registerPostMetaBoxes($meta_boxes) {
        $meta_boxes = parent::registerPostMetaBoxes($meta_boxes);
        $textDescription = self::getIcon('file-user') . __('Description');
        //$textLocation = self::getIcon('map-marker') . __('Location', WpApp::TEXT_DOMAIN);
        $textWorkExperience = self::getIcon('business-time') . __('Work Experience', WpApp::TEXT_DOMAIN);
        $textEducation = self::getIcon('graduation-cap') . __('Education', WpApp::TEXT_DOMAIN);
        $textCertificates = self::getIcon('file-certificate') . __('Certificates and Documents', WpApp::TEXT_DOMAIN);
        $meta_boxes[] = [MetaBox::ID => self::META_BOX_EMPLOYEE_INFO,
            MetaBox::POST_TYPES => [$this->getType()],
            MetaBox::TITLE => $textDescription,
            MetaBox::CONTEXT => MetaBoxContext::NORMAL,
            MetaBox::PRIORITY => MetaBoxPriority::HIGH,
            MetaBox::FIELDS => [
                /*[
                    MetaBoxFieldTaxonomy::TYPE => MetaBoxFieldType::TAXONOMY,
                    MetaBoxFieldTaxonomy::ID => self::META_LOCATION,
                    MetaBoxFieldTaxonomy::NAME => $textLocation,
                    MetaBoxFieldTaxonomy::TAXONOMY => self::TAXONOMY_LOCATION
                ],*/
                [
                    MetaBoxFieldNumber::TYPE => MetaBoxFieldType::NUMBER,
                    MetaBoxFieldNumber::ID => self::META_EXPERIENCE,
                    MetaBoxFieldNumber::NAME => $textWorkExperience,
                ],
                [
                    MetaBoxField::TYPE => MetaBoxFieldType::TEXT,
                    MetaBoxField::ID => self::META_EDUCATION,
                    MetaBoxField::NAME => $textEducation,
                ],
                [
                    MetaBoxFieldImage::TYPE => MetaBoxFieldType::IMAGE_ADVANCED,
                    MetaBoxFieldImage::ID => self::META_CERTIFICATES,
                    MetaBoxFieldImage::NAME => $textCertificates,
                    MetaBoxFieldImage::COLUMNS => 12,
                    MetaBoxFieldImage::MAX_UPLOADS => 10,
                    MetaBoxFieldImage::MAX_STATUS => true,
                    MetaBoxFieldImage::FORCE_DELETE => true, // Cause Removing of image when rearrange
                ]
            ]];
        return $meta_boxes;
    }
}