<?php
namespace wp;
class RealEstate extends WpApp {
    public function __construct() {
        parent::__construct();
        RestrictedEditor::i();
        PostPartner::i();
        PostProperty::i();
        add_filter(MetaBoxFilter::REGISTER, [$this, 'registerMetaBoxesForUser']);
        if (is_admin() == false) {
            // If we are in the admin area, skip this function
            add_filter(WPActions::WP_SETUP_NAV_MENU_ITEM, [$this, 'handleNavMenuItems'], 10, 1);
        }
    }

    function enqueueScriptsTheme() {
        parent::enqueueScriptsTheme();
        wp_enqueue_style('realestate', $this->uriToLibs . 'realestate/realestate.css');
    }

    public function registerMetaBoxesForUser($meta_boxes) {
        $textPhotos = ucfirst(__('photos'));
        $textVideos = __('Videos');
        $idTabInfo = 'information';
        $idTabPortfolio = 'portfolio';
        $meta_boxes[] = [MetaBox::TITLE => '',
                         MetaBox::TYPE => 'user',
                         MetaBox::FIELDS => [[MetaBoxField::ID => 'phone',
                                              MetaBoxField::NAME => __('Mobile Phone'),
                                              MetaBoxField::TYPE => MetaBoxFieldType::TEXT]]];
        return $meta_boxes;
    }

    function handleNavMenuItems($menuItem) {
        $taxonomy = $menuItem->object;
        if ($taxonomy == PostProperty::TAX_TYPE) {
            $termNamePlural = get_term_meta($menuItem->object_id, PostProperty::META_TAX_TYPE_NAME_PLURAL, true);
            if ($termNamePlural) {
                $menuItem->title = $termNamePlural;
            }
        }
        return $menuItem;
    }
}