<?php namespace wp;
/** Author: Vitali Lupu <vitaliix@gmail.com> */
final class PostPartner extends PostBase {
    const TYPE = "partner";

    public function registerPost() {
        $textPartner = __('Partner', WpApp::TEXT_DOMAIN);
        $textPartners = __('Partners', WpApp::TEXT_DOMAIN);
        register_post_type(self::TYPE, [WPostArguments::HIERARCHICAL => false,
            WPostArguments::SHOW_UI => true,
            WPostArguments::QUERY_VAR => true,
            WPostArguments::IS_PUBLIC => true,
            WPostArguments::IS_PUBLIC_QUERY => true,
            WPostArguments::EXCLUDE_FROM_SEARCH => true,
            WPostArguments::HAS_ARCHIVE => true,
            WPostArguments::MENU_POSITION => 8,
            WPostArguments::MENU_ICON => 'dashicons-groups',
            WPostArguments::CAPABILITY_TYPE => 'post',
            WPostArguments::LABELS => self::getPostLabels($textPartner, $textPartners),
            WPostArguments::SUPPORTS => [WPostSupport::TITLE, WPostSupport::THUMBNAIL],]);
        parent::registerPost();
    }

    public function registerPostMetaBoxes($meta_boxes) {
        $textPartnerInformation = $this->getIcon("fa-handshake-o") . __('Partner Information', WpApp::TEXT_DOMAIN);
        $meta_boxes[] = [MetaBox::POST_TYPES => [self::TYPE],
            MetaBox::CONTEXT => MetaBoxContext::NORMAL,
            MetaBox::PRIORITY => MetaBoxPriority::HIGH,
            MetaBox::ID => MetaPartner::ID_BOX,
            MetaBox::TITLE => $textPartnerInformation,
            MetaBox::FIELDS => [[MetaBoxFieldInput::TYPE => MetaBoxFieldType::TEXT,
                MetaBoxFieldInput::COLUMNS => 12,
                MetaBoxFieldInput::ID => MetaPartner::URL,
                MetaBoxFieldInput::NAME => __('Website'),
                MetaBoxFieldInput::DESCRIPTION => __('Site Address (URL)'),
                MetaBoxFieldInput::PLACEHOLDER => 'Ex: http://example.com']]];
        return $meta_boxes;
    }
}