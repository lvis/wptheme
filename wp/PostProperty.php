<?php namespace wp;
/** Author: Vitali Lupu <vitaliix@gmail.com> */

use WP_Query;
use WP_Taxonomy;
use WP_Term;

final class PostProperty extends PostBase {
    const TYPE = "property";
    //Taxonomies
    const TAX_TYPE = 'property-type';
    const TAX_STATUS = 'property-status';
    const TAX_LOCATION = 'property-location';
    const TAX_FEATURE = 'property-feature';
    const META_TAX_TYPE_NAME_PLURAL = 'propertyMetaTaxTypeNamePlural';
    //Search
    const SEARCH_KEYWORD = 's';
    const SEARCH_ID = 'code';
    const SEARCH_TYPE = 'type';
    const SEARCH_LOCATION = 'location';
    const SEARCH_STATUS = 'status';
    const SEARCH_FEATURES = 'features';
    const SEARCH_AUTHOR = 'author';
    const SEARCH_ROOMS = 'rooms';
    const SEARCH_FLOOR = 'floor';
    const SEARCH_FLOORS = 'floors';
    const SEARCH_AREA = 'area';
    const SEARCH_PRICE = 'price';
    protected static $searchPropertyFields;

    static function getSearchPropertyFields() {
        if (!self::$searchPropertyFields) {
            self::$searchPropertyFields = [self::SEARCH_KEYWORD => __('Keyword', WpApp::TEXT_DOMAIN),
                self::SEARCH_ID => __('Property Code', WpApp::TEXT_DOMAIN),
                self::SEARCH_STATUS => __('Property Status', WpApp::TEXT_DOMAIN),
                self::SEARCH_TYPE => __('Property Type', WpApp::TEXT_DOMAIN),
                self::SEARCH_LOCATION => __('Property Location', WpApp::TEXT_DOMAIN),
                self::SEARCH_ROOMS => __('Rooms', WpApp::TEXT_DOMAIN),
                self::SEARCH_FLOORS => __('Floors', WpApp::TEXT_DOMAIN),
                self::SEARCH_PRICE => __('Max Price', WpApp::TEXT_DOMAIN),
                self::SEARCH_AREA => __('Max Area', WpApp::TEXT_DOMAIN),
                self::SEARCH_FEATURES => __('Property Features', WpApp::TEXT_DOMAIN)];
        }
        return self::$searchPropertyFields;
    }

    public function registerPost() {
        // Register Taxonomy: Status
        $textStatus = __('Status', WpApp::TEXT_DOMAIN);
        $textStatuses = __('Statuses', WpApp::TEXT_DOMAIN);
        $taxonomyLabels = self::getTaxonomyLabels($textStatus, $textStatuses);
        register_taxonomy(PostProperty::TAX_STATUS, [self::TYPE], [
            WPTaxonomyArguments::HIERARCHICAL => true,
            WPTaxonomyArguments::SHOW_UI => false,
            WPTaxonomyArguments::QUERY_VAR => true,
            WPTaxonomyArguments::REST_SHOW_IN => true,
            WPTaxonomyArguments::META_BOX_CB => false,
            WPTaxonomyArguments::LABELS => $taxonomyLabels]);
        // Register Taxonomy: Type
        $textType = __('Type', WpApp::TEXT_DOMAIN);
        $textTypes = __('Types', WpApp::TEXT_DOMAIN);
        $taxonomyLabels = self::getTaxonomyLabels($textType, $textTypes);
        register_taxonomy(PostProperty::TAX_TYPE, [self::TYPE], [
            WPTaxonomyArguments::HIERARCHICAL => true,
            WPTaxonomyArguments::SHOW_UI => true,
            WPTaxonomyArguments::QUERY_VAR => true,
            WPTaxonomyArguments::REST_SHOW_IN => true,
            WPTaxonomyArguments::META_BOX_CB => false,
            WPTaxonomyArguments::LABELS => $taxonomyLabels]);
        // Register Taxonomy: Location
        $textLocation = __('Location', WpApp::TEXT_DOMAIN);
        $textLocations = __('Locations', WpApp::TEXT_DOMAIN);
        $taxonomyLabels = self::getTaxonomyLabels($textLocation, $textLocations);
        register_taxonomy(PostProperty::TAX_LOCATION, [self::TYPE], [
            WPTaxonomyArguments::HIERARCHICAL => true,
            WPTaxonomyArguments::SHOW_UI => true,
            WPTaxonomyArguments::QUERY_VAR => true,
            WPTaxonomyArguments::REST_SHOW_IN => true,
            WPTaxonomyArguments::META_BOX_CB => false,
            WPTaxonomyArguments::LABELS => $taxonomyLabels]);
        // Register Taxonomy: Feature
        $textFeature = __('Feature', WpApp::TEXT_DOMAIN);
        $textFeatures = __('Features', WpApp::TEXT_DOMAIN);
        $taxonomyLabels = self::getTaxonomyLabels($textFeature, $textFeatures);
        register_taxonomy(PostProperty::TAX_FEATURE, [self::TYPE], [
            WPTaxonomyArguments::HIERARCHICAL => true,
            WPTaxonomyArguments::SHOW_UI => true,
            WPTaxonomyArguments::QUERY_VAR => true,
            WPTaxonomyArguments::REST_SHOW_IN => true,
            WPTaxonomyArguments::META_BOX_CB => false,
            WPTaxonomyArguments::LABELS => $taxonomyLabels]);
        // Register Post: Property
        $textProperty = __('Property', WpApp::TEXT_DOMAIN);
        $textProperties = __('Properties', WpApp::TEXT_DOMAIN);
        register_post_type(self::TYPE, [WPostArguments::HIERARCHICAL => false,
            WPostArguments::SHOW_UI => true,
            WPostArguments::QUERY_VAR => true,
            WPostArguments::IS_PUBLIC => true,
            WPostArguments::IS_PUBLIC_QUERY => true,
            WPostArguments::HAS_ARCHIVE => true,
            WPostArguments::MENU_ICON => 'dashicons-building',
            WPostArguments::MENU_POSITION => 6,
            WPostArguments::LABELS => self::getPostLabels($textProperty, $textProperties),
            WPostArguments::REST_SHOW_IN => true,
            WPostArguments::REST_BASE => self::TYPE,
            WPostArguments::REST_CONTROLLER_CLASS => 'WP_REST_Posts_Controller',
            WPostArguments::SUPPORTS => [WPostSupport::AUTHOR]]);
//            WPostArguments::SUPPORTS => [WPostSupport::THUMBNAIL, WPostSupport::AUTHOR, WPostSupport::EDITOR]]);
        remove_post_type_support(self::TYPE, 'editor');
        add_filter('rwmb_content_value', '__return_empty_string');
        parent::registerPost();
    }

    function enqueueScriptsAdmin() {
        parent::enqueueScriptsAdmin();
        $printMeta = 'rwmb_meta';
        if (function_exists($printMeta) && UtilsWp::getCurrentScreenId() == PostProperty::TYPE) {
            $idTermRent = '';
            /**@var $term WP_Term */
            $term = get_term_by('slug', PostProperty::CONTRACT_RENT, PostProperty::TAX_STATUS);
            if (is_a($term, 'WP_Term')) {
                $idTermRent = $term->term_id;
            }
            $idTermSale = '';
            $term = get_term_by('slug', PostProperty::CONTRACT_SALE, PostProperty::TAX_STATUS);
            if (is_a($term, 'WP_Term')) {
                $idTermSale = $term->term_id;
            }
            $valuePropertyStatus = '';
            $term = rwmb_meta(self::TAX_STATUS);
            if (is_a($term, 'WP_Term')) {
                $valuePropertyStatus = $term->term_id;
            }
            $valuePropertyType = '';
            $term = rwmb_meta(self::TAX_TYPE);
            if (is_a($term, 'WP_Term')) {
                $valuePropertyType = $term->term_id;
            }
            $valuePropertyLocation = '';
            $term = rwmb_meta(self::TAX_LOCATION);
            if (is_a($term, 'WP_Term')) {
                $valuePropertyLocation = $term->term_id;
            }
            $siteEditor = WPUsers::isSiteEditor() ? 'true' : 'false';
            $contentJsModel = /**@lang JavaScript */
                "function ViewModelAdmin() {
                var self = this;
                self.propertyStatus = ko.observable('{$valuePropertyStatus}');
                self.propertyType = ko.observable('{$valuePropertyType}');
                self.propertyLocation = ko.observable('{$valuePropertyLocation}');
                self.propertyPrice = ko.observable('{$printMeta(self::META_PRICE)}');
                self.propertyPartner = ko.observable('{$printMeta(self::META_PARTNER)}');
                self.isPropertySale = ko.pureComputed(function () {
                    return self.propertyStatus() === '{$idTermSale}';
                });
                self.isPropertyRent = ko.pureComputed(function () {
                    return self.propertyStatus() === '{$idTermRent}';
                });
                self.hasPropertySellPrice = ko.pureComputed(function () {
                    return self.isPropertySale() && self.propertyPrice() && {$siteEditor};
                });
                self.isFinanceable = ko.pureComputed(function () {
                    return self.hasPropertySellPrice() && self.propertyPartner();
                });
                self.propertyCanBePublished = ko.pureComputed(function(){
                    return (self.propertyStatus() && self.propertyLocation() && self.propertyType() && self.propertyPrice());
                });
                self.cssSubmitPost = ko.pureComputed(function(){
                    return { pointerEvents: self.propertyCanBePublished()? 'auto' : 'none', opacity: self.propertyCanBePublished() ? 1 : 0.4 };
                });
                var formSubmit = document.getElementById('submitpost');
                console.log('formSubmit',formSubmit);
                if (formSubmit !== undefined){
                    formSubmit.setAttribute('data-bind','style:cssSubmitPost()');
                    var btnPublish = document.getElementById('publish');
                    if (btnPublish !== undefined) {
                        btnPublish.setAttribute('data-bind','enable:propertyCanBePublished');
                    }
                }
            }
            var postElement = document.getElementById('poststuff');
            var vmAdmin = new ViewModelAdmin();
            ko.applyBindings(vmAdmin, postElement);";
            wp_add_inline_script('knockout', $contentJsModel);
        }
    }

    function getValueByKey($from, $key) {
        $result = "";
        if (isset($from[$key])) {
            $result = $from[$key];
        }
        return $result;
    }

    public function handlePostSaveBefore($data, $postFields) {
        if ($data[QueryPost::TYPE] == self::TYPE) {
            $defaultLocale = UtilsWp::getLanguageShortCode();
            $languages = [$defaultLocale];
            if (function_exists('qtranxf_getSortedLanguages')) {
                $defaultLocale = qtranxf_getLanguageDefault();
                $languages = qtranxf_getSortedLanguages();
            }
            $propertyId = $this->getValueByKey($postFields, "ID");
            $propertyStatusId = $this->getValueByKey($postFields, PostProperty::TAX_STATUS);
            $propertyTypeId = $this->getValueByKey($postFields, PostProperty::TAX_TYPE);
            $propertyLocationId = $this->getValueByKey($postFields, PostProperty::TAX_LOCATION);
            $price = $this->getValueByKey($postFields, PostProperty::META_PRICE);
            $titleCustom = "";
            $postName = "";
            foreach ($languages as $lang) {
                //Status
                $propertyPrice = "";
                $propertyStatus = "";
                if (empty($propertyStatusId) == false) {
                    //@var $term WP_Term
                    $term = get_term($propertyStatusId, PostProperty::TAX_STATUS);
                    $term = apply_filters('translate_term', $term, $lang, PostProperty::TAX_STATUS);
                    $propertyStatus = " {$term->name}";
                    $currency = $this->getValueByKey($postFields, PostProperty::META_PRICE_CURRENCY);
                    $currency = self::getPriceCurrency()[$currency];
                    $propertyPrice = "{$price} {$currency}";
                    if ($term->slug == PostProperty::CONTRACT_RENT) {
                        $priceRentCriteria = $this->getValueByKey($postFields, PostProperty::META_PRICE_RENT_CRITERIA);
                        if (isset(self::getRentalPriceCriteria()[$priceRentCriteria])) {
                            $priceRentCriteria = PostProperty::getRentalPriceCriteria(true)[$priceRentCriteria];
                            $propertyPrice .= " $priceRentCriteria";
                        }
                    }
                }
                //Type
                $propertyType = "";
                if (empty($propertyTypeId) == false) {
                    /** @var $term WP_Term */
                    $term = get_term($propertyTypeId, PostProperty::TAX_TYPE);
                    $term = apply_filters('translate_term', $term, $lang, PostProperty::TAX_TYPE);
                    $propertyType = " {$term->name}";
                }
                //Location
                $propertyLocation = "";
                if (empty($propertyLocationId) == false) {
                    /** @var  $term WP_Term */
                    $term = get_term($propertyLocationId, PostProperty::TAX_LOCATION);
                    $term = apply_filters('translate_term', $term, $lang, PostProperty::TAX_LOCATION);
                    $propertyLocation = " {$term->name}";
                    if ($term->parent) {
                        $term = get_term($term->parent, PostProperty::TAX_LOCATION);
                        $term = apply_filters('translate_term', $term, $lang, PostProperty::TAX_LOCATION);
                        $propertyLocation = " $term->name, $propertyLocation";
                    }
                }
                $titleCustom .= "[:$lang]$propertyId. $propertyStatus$propertyType$propertyLocation $propertyPrice";
                if ($lang == "en") {
                    $postName = sanitize_title("$propertyId. $propertyStatus$propertyType$propertyLocation $propertyPrice");
                }
            }
            UtilsWp::loadThemeLocale(WpApp::TEXT_DOMAIN, $defaultLocale);
            $titleCustom .= "[:]";
            $data['post_title'] = $titleCustom;
            if ($postName) {
                $data['post_name'] = $postName;
            }
            $postGallery = $this->getValueByKey($postFields, PostProperty::META_GALLERY);
            //postGallery   - $price = $this->getValueByKey($postarr, PostProperty::META_PRICE);
            if (isset($postGallery[0])){
                set_post_thumbnail($propertyId, $postGallery[0]);
                //$data['_thumbnail_id'] = $postGallery[0];
            }
        }
        return parent::handlePostSaveBefore($data, $postFields);
    }

    public function handlePostColumns($columns) {
        self::arrayInsert($columns, 2, [
            PostProperty::SEARCH_PRICE => __('Price', WpApp::TEXT_DOMAIN),
            /*PostProperty::SEARCH_ROOMS => __('Rooms', WpApp::TEXT_DOMAIN),
            PostProperty::SEARCH_FLOOR => __('Floor', WpApp::TEXT_DOMAIN),
            PostProperty::SEARCH_FLOORS => __('Floors', WpApp::TEXT_DOMAIN),
            PostProperty::SEARCH_ID => __('Code'),*/
            PostProperty::SEARCH_AUTHOR => __('Author')
        ]);
        return parent::handlePostColumns($columns);
    }

    public function handlePostColumnsSortable($columns) {
        self::arrayInsert($columns, 2, [
            PostProperty::SEARCH_ID => PostProperty::SEARCH_ID,
            PostProperty::SEARCH_AUTHOR => PostProperty::SEARCH_AUTHOR,
            PostProperty::SEARCH_PRICE => PostProperty::SEARCH_PRICE,
            /*PostProperty::SEARCH_ROOMS => PostProperty::SEARCH_ROOMS,
            PostProperty::SEARCH_FLOOR => PostProperty::SEARCH_FLOOR,
            PostProperty::SEARCH_FLOORS => PostProperty::SEARCH_FLOORS*/
        ]);
        return parent::handlePostColumnsSortable($columns);
    }

    public function handlePostColumnsContent($column, $postId) {
        switch ($column) {
        /*case PostBase::COLUMN_THUMB:
            echo UtilsWp::getThumbnail([144, 98], []);
            break;*/
        case PostProperty::SEARCH_PRICE:
            echo self::getPriceFormatted();
            break;
        case PostProperty::SEARCH_ROOMS:
            echo self::getRooms();
            break;
        case PostProperty::SEARCH_FLOOR:
            echo self::getFloor();
            break;
        case PostProperty::SEARCH_FLOORS:
            echo self::getFloorsValue();
            break;
        case PostProperty::SEARCH_STATUS:
            echo $this->getTaxonomyLinksOfTerms($postId, PostProperty::TAX_STATUS);
            break;
        case PostProperty::SEARCH_TYPE:
            echo $this->getTaxonomyLinksOfTerms($postId, PostProperty::TAX_TYPE);
            break;
        case PostProperty::SEARCH_LOCATION:
            echo $this->getTaxonomyLinksOfTerms($postId, PostProperty::TAX_LOCATION);
            break;
        case PostProperty::SEARCH_FEATURES:
            echo get_the_term_list($postId, PostProperty::TAX_FEATURE, '', ', ', '');
            break;
        case PostProperty::SEARCH_ID:
            echo $postId;
            break;
        }
    }

    public function registerPostMetaBoxes($meta_boxes) {
        $textValueExample = __('Value Example: %s', WpApp::TEXT_DOMAIN);
        $textChoose = __('Choose %s', WpApp::TEXT_DOMAIN);
        $default_content = '';
        $post_id = filter_input(INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT);
        if (!$post_id) {
            $post_id = filter_input(INPUT_POST, 'post_ID', FILTER_SANITIZE_NUMBER_INT);
        }
        if ($post_id) {
            $default_content = get_post_field('post_content', $post_id);
        }
        $meta_boxes = parent::registerPostMetaBoxes($meta_boxes);
        $propertyGeneral = [
            MetaBox::POST_TYPES => self::TYPE,
            MetaBox::ID => self::ID_BOX,
            MetaBox::TITLE => $this->getIcon('building') . __('Description'),
            MetaBox::CONTEXT => MetaBoxContext::NORMAL,
            MetaBox::PRIORITY => MetaBoxPriority::HIGH,
            MetaBox::FIELDS => []
        ];
        //Property Views
        $propertyGeneral[MetaBox::FIELDS][] = [
            MetaBoxField::ID => PostProperty::VIEW_COUNT,
            MetaBoxField::TYPE => MetaBoxFieldType::HIDDEN,
            MetaBoxField::NAME => PostProperty::VIEW_COUNT
        ];
        //Publish Conditions
        $textPublishRequirements = __("To publish the Property please complete the required fields: Status, Type, Location and Price", WpApp::TEXT_DOMAIN);
        $propertyGeneral[MetaBox::FIELDS][] = [
            MetaBoxField::TYPE => MetaBoxFieldType::CUSTOM_HTML,
            MetaBoxField::STD => "<strong>{$textPublishRequirements}</strong>",
            MetaBoxFieldTaxonomy::BEFORE => '<!-- ko if: !propertyCanBePublished() -->',
            MetaBoxFieldTaxonomy::AFTER => '<!-- /ko -->'
        ];
        //Taxonomy: Status
        $propertyGeneral[MetaBox::FIELDS][] = [
            MetaBoxFieldTaxonomy::TYPE => MetaBoxFieldType::TAXONOMY,
            MetaBoxFieldTaxonomy::TAXONOMY => PostProperty::TAX_STATUS,
            MetaBoxFieldTaxonomy::FIELD_TYPE => MetaBoxFieldTaxonomyType::SELECT,
            MetaBoxFieldTaxonomy::ID => PostProperty::TAX_STATUS,
            MetaBoxFieldTaxonomy::FLATTEN => false,
            MetaBoxFieldTaxonomy::COLUMNS => 4,
            MetaBoxFieldTaxonomy::PLACEHOLDER => sprintf($textChoose, __('Property Status', WpApp::TEXT_DOMAIN)),
            MetaBoxFieldTaxonomy::NAME => $this->getIcon('map-signs') . __('Status', WpApp::TEXT_DOMAIN),
            MetaBoxFieldTaxonomy::ATTRIBUTES => ['data-bind' => 'value: propertyStatus'],
            MetaBoxFieldTaxonomy::ADD_TO_SEO => true
        ];
        //Taxonomy: Type
        $propertyGeneral[MetaBox::FIELDS][] = [
            MetaBoxFieldTaxonomy::TYPE => MetaBoxFieldType::TAXONOMY,
            MetaBoxFieldTaxonomy::TAXONOMY => PostProperty::TAX_TYPE,
            MetaBoxFieldTaxonomy::FIELD_TYPE => MetaBoxFieldTaxonomyType::SELECT,
            MetaBoxFieldTaxonomy::ID => PostProperty::TAX_TYPE,
            MetaBoxFieldTaxonomy::FLATTEN => false,
            MetaBoxFieldTaxonomy::COLUMNS => 4,
            MetaBoxFieldTaxonomy::PLACEHOLDER => sprintf($textChoose, __('Property Type', WpApp::TEXT_DOMAIN)),
            MetaBoxFieldTaxonomy::NAME => $this->getIcon('home') . __('Type', WpApp::TEXT_DOMAIN),
            MetaBoxFieldTaxonomy::ATTRIBUTES => ['data-bind' => 'value: propertyType'],
            MetaBoxFieldTaxonomy::ADD_TO_SEO => true
        ];
        //Taxonomy: Location
        $propertyGeneral[MetaBox::FIELDS][] = [
            MetaBoxFieldTaxonomy::TYPE => MetaBoxFieldType::TAXONOMY,
            MetaBoxFieldTaxonomy::TAXONOMY => PostProperty::TAX_LOCATION,
            MetaBoxFieldTaxonomy::FIELD_TYPE => MetaBoxFieldTaxonomyType::SELECT,
            MetaBoxFieldTaxonomy::ID => PostProperty::TAX_LOCATION,
            MetaBoxFieldTaxonomy::FLATTEN => false,
            MetaBoxFieldTaxonomy::COLUMNS => 4,
            MetaBoxFieldTaxonomy::PLACEHOLDER => sprintf($textChoose, __('Property Location', WpApp::TEXT_DOMAIN)),
            MetaBoxFieldTaxonomy::NAME => $this->getIcon('map-marker') . __('Location', WpApp::TEXT_DOMAIN),
            MetaBoxFieldTaxonomy::ATTRIBUTES => ['data-bind' => 'value: propertyLocation'],
            MetaBoxFieldTaxonomy::ADD_TO_SEO => true
        ];
        //Field: Price
        $textPrice = $this->getIcon('hand-holding-usd') . __('Price', WpApp::TEXT_DOMAIN);
        $propertyGeneral[MetaBox::FIELDS][] = [
            MetaBoxFieldNumber::TYPE => MetaBoxFieldType::NUMBER,
            MetaBoxFieldNumber::COLUMNS => 6,
            MetaBoxFieldNumber::MIN => 0,
            MetaBoxFieldNumber::STEP => 0.01,
            MetaBoxFieldNumber::ID => PostProperty::META_PRICE,
            MetaBoxFieldNumber::NAME => $textPrice,
            MetaBoxFieldNumber::DESCRIPTION => sprintf($textValueExample, '10000'),
            MetaBoxFieldTaxonomy::ATTRIBUTES => ['data-bind' => 'value: propertyPrice']
        ];
        //Field: Price Promo
        $textPricePromo = $this->getIcon('badge-dollar') . __('Promotional Price', WpApp::TEXT_DOMAIN);
        $propertyGeneral[MetaBox::FIELDS][] = [
            MetaBoxFieldNumber::TYPE => MetaBoxFieldType::NUMBER,
            MetaBoxFieldNumber::COLUMNS => 6,
            MetaBoxFieldNumber::MIN => 0,
            MetaBoxFieldNumber::STEP => 0.01,
            MetaBoxFieldNumber::ID => PostProperty::META_PRICE_PROMO,
            MetaBoxFieldNumber::NAME => $textPricePromo,
            MetaBoxFieldNumber::DESCRIPTION => sprintf($textValueExample, '9500')
        ];
        //Field: Price Currency
        $textPriceCurrency = __('Price Currency', WpApp::TEXT_DOMAIN);
        $propertyGeneral[MetaBox::FIELDS][] = [
            MetaBoxFieldSelect::TYPE => MetaBoxFieldType::SELECT,
            MetaBoxFieldSelect::COLUMNS => 6,
            MetaBoxFieldSelect::OPTIONS => self::getPriceCurrency(),
            MetaBoxFieldSelect::MULTIPLE => false,
            MetaBoxFieldSelect::STD => 'eur',
            MetaBoxFieldSelect::ID => PostProperty::META_PRICE_CURRENCY,
            MetaBoxFieldSelect::NAME => $textPriceCurrency,
            MetaBoxFieldSelect::DESCRIPTION => sprintf($textValueExample, 'MDL')
        ];
        //Field: Price Prefix
        $textPricePrefix  = __('Price Prefix', WpApp::TEXT_DOMAIN);
        $textDescPricePrefix = sprintf($textValueExample, self::getPricePrefix()[PostProperty::META_PRICE_PREFIX_FROM]);
        $propertyGeneral[MetaBox::FIELDS][] = [
            MetaBoxFieldSelect::TYPE => MetaBoxFieldType::SELECT,
            MetaBoxFieldSelect::COLUMNS => 6,
            MetaBoxFieldSelect::OPTIONS => self::getPricePrefix(),
            MetaBoxFieldSelect::MULTIPLE => false,
            MetaBoxFieldSelect::ID => PostProperty::META_PRICE_PREFIX,
            MetaBoxFieldSelect::NAME => $textPricePrefix,
            MetaBoxFieldSelect::DESCRIPTION => $textDescPricePrefix,
            MetaBoxFieldTaxonomy::BEFORE => '<!-- ko if: isPropertySale -->',
            MetaBoxFieldTaxonomy::AFTER => '<!-- /ko -->'];
        //Field: Rent Criteria
        $textRentalPriceCriteria  = __('Rental price criteria', WpApp::TEXT_DOMAIN);
        $textDescRental = sprintf($textValueExample, self::getRentalPriceCriteria()[PostProperty::RENT_CRITERIA_MONTH]);
        $propertyGeneral[MetaBox::FIELDS][] = [
            MetaBoxFieldSelect::TYPE => MetaBoxFieldType::SELECT,
            MetaBoxFieldSelect::COLUMNS => 12,
            MetaBoxFieldSelect::OPTIONS => self::getRentalPriceCriteria(),
            MetaBoxFieldSelect::MULTIPLE => false,
            MetaBoxFieldSelect::STD => PostProperty::RENT_CRITERIA_MONTH,
            MetaBoxFieldSelect::ID => PostProperty::META_PRICE_RENT_CRITERIA,
            MetaBoxFieldSelect::NAME => $textRentalPriceCriteria,
            MetaBoxFieldSelect::DESCRIPTION => $textDescRental,
            MetaBoxFieldTaxonomy::BEFORE => '<!-- ko if: isPropertyRent -->',
            MetaBoxFieldTaxonomy::AFTER => '<!-- /ko -->'];
        //Field: Financing Company
        $textFinancingCompany = $this->getIcon('handshake-o') . __('The financing company', WpApp::TEXT_DOMAIN);
        $propertyGeneral[MetaBox::FIELDS][] = [
            MetaBoxFieldPost::TYPE => MetaBoxFieldType::POST,
            MetaBoxFieldPost::ID => PostProperty::META_PARTNER,
            MetaBoxFieldPost::POST_TYPE => PostPartner::TYPE,
            MetaBoxFieldPost::COLUMNS => 12,
            MetaBoxFieldPost::PLACEHOLDER => __('None'),
            MetaBoxFieldPost::FIELD_TYPE => MetaBoxFieldSelectTypes::SELECT,
            MetaBoxFieldPost::NAME => $textFinancingCompany,
            MetaBoxFieldTaxonomy::BEFORE => "<!-- ko if: hasPropertySellPrice -->",
            MetaBoxFieldTaxonomy::AFTER => '<!-- /ko -->',
            MetaBoxFieldTaxonomy::ATTRIBUTES => ['data-bind' => 'value: propertyPartner']];
        //Field: First Rate
        $textFirstRate = __('First rate', WpApp::TEXT_DOMAIN);
        $propertyGeneral[MetaBox::FIELDS][] = [MetaBoxFieldNumber::TYPE => MetaBoxFieldType::NUMBER,
            MetaBoxFieldNumber::COLUMNS => 6,
            MetaBoxFieldNumber::MIN => 0,
            MetaBoxFieldNumber::STEP => 0.01,
            MetaBoxFieldNumber::ID => PostProperty::META_FINANCING_FIRST_RATE,
            MetaBoxFieldNumber::NAME => $textFirstRate,
            MetaBoxFieldNumber::DESCRIPTION => sprintf($textValueExample, '2500'),
            MetaBoxFieldTaxonomy::BEFORE => '<!-- ko if: isFinanceable -->',
            MetaBoxFieldTaxonomy::AFTER => '<!-- /ko -->'];
        //Field: Monthly Payment
        $textMonthlyPayment = __('Monthly payment', WpApp::TEXT_DOMAIN);
        $propertyGeneral[MetaBox::FIELDS][] = [MetaBoxFieldNumber::TYPE => MetaBoxFieldType::NUMBER,
            MetaBoxFieldNumber::COLUMNS => 6,
            MetaBoxFieldNumber::MIN => 0,
            MetaBoxFieldNumber::STEP => 0.01,
            MetaBoxFieldNumber::ID => PostProperty::META_FINANCING_MONTHLY_PAYMENT,
            MetaBoxFieldNumber::NAME => $textMonthlyPayment,
            MetaBoxFieldNumber::DESCRIPTION => sprintf($textValueExample, '500'),
            MetaBoxFieldTaxonomy::BEFORE => '<!-- ko if: isFinanceable -->',
            MetaBoxFieldTaxonomy::AFTER => '<!-- /ko -->'];
        //Field: Area Size
        $textPropertyAreaSize = $this->getIcon('arrows-alt') . __('Property area size', WpApp::TEXT_DOMAIN);
        $propertyGeneral[MetaBox::FIELDS][] = [MetaBoxFieldNumber::TYPE => MetaBoxFieldType::NUMBER,
            MetaBoxFieldNumber::COLUMNS => 6,
            MetaBoxFieldNumber::MIN => 0,
            MetaBoxFieldNumber::STEP => 0.01,
            MetaBoxFieldNumber::ID => PostProperty::META_SIZE,
            MetaBoxFieldNumber::NAME => $textPropertyAreaSize,
            MetaBoxFieldNumber::DESCRIPTION => sprintf($textValueExample, '35')];
        //Field: Area Measure Unit
        $textMeasureUnit = __('%s measure unit', WpApp::TEXT_DOMAIN);
        $textMeasureUnitArea = sprintf($textMeasureUnit, __('Area', WpApp::TEXT_DOMAIN));
        $textMeasureUnitArea = $this->getIcon('pencil-ruler') . $textMeasureUnitArea;
        $textDescMeasureUnitArea = sprintf($textValueExample, self::getSizeMeasureUnits()[PostProperty::UNIT_SQM_SYMBOL]);
        $propertyGeneral[MetaBox::FIELDS][] = [MetaBoxFieldSelect::TYPE => MetaBoxFieldType::SELECT,
            MetaBoxFieldSelect::COLUMNS => 6,
            MetaBoxFieldSelect::OPTIONS => self::getSizeMeasureUnits(),
            MetaBoxFieldSelect::MULTIPLE => false,
            MetaBoxFieldSelect::STD => PostProperty::UNIT_SQM_SYMBOL,
            MetaBoxField::ID => PostProperty::META_SIZE_UNIT,
            MetaBoxField::NAME => $textMeasureUnitArea,
            MetaBoxFieldSelect::DESCRIPTION => $textDescMeasureUnitArea];
        //Field: Plot of Land Size
        $textPlotLandSize = $this->getIcon('island-tropical') . __('Plot of land size', WpApp::TEXT_DOMAIN);
        $propertyGeneral[MetaBox::FIELDS][] = [MetaBoxFieldNumber::TYPE => MetaBoxFieldType::NUMBER,
            MetaBoxFieldNumber::COLUMNS => 6,
            MetaBoxFieldNumber::MIN => 0,
            MetaBoxFieldNumber::STEP => 0.01,
            MetaBoxFieldNumber::ID => PostProperty::META_SIZE_OF_LAND,
            MetaBoxFieldNumber::NAME => $textPlotLandSize,
            MetaBoxFieldNumber::DESCRIPTION => sprintf($textValueExample, '6')];
        //Field: Plot of Land Measure Unit
        $textMeasureUnitLand = sprintf($textMeasureUnit, __('The land', WpApp::TEXT_DOMAIN));
        $textDescMeasureUnitLand = sprintf($textValueExample, self::getLandMeasureUnits()[PostProperty::LAND_UNIT_ARE]);
        $propertyGeneral[MetaBox::FIELDS][] = [MetaBoxFieldSelect::TYPE => MetaBoxFieldType::SELECT,
            MetaBoxFieldSelect::COLUMNS => 6,
            MetaBoxFieldSelect::OPTIONS => self::getLandMeasureUnits(),
            MetaBoxFieldSelect::MULTIPLE => false,
            MetaBoxFieldSelect::STD => PostProperty::LAND_UNIT_ARE,
            MetaBoxField::ID => PostProperty::META_SIZE_OF_LAND_UNIT,
            MetaBoxField::NAME => $textMeasureUnitLand,
            MetaBoxFieldSelect::DESCRIPTION => $textDescMeasureUnitLand];
        //Field: Rooms
        $propertyGeneral[MetaBox::FIELDS][] = [MetaBoxField::TYPE => MetaBoxFieldType::NUMBER,
            MetaBoxField::COLUMNS => 4,
            MetaBoxFieldNumber::MIN => 0,
            MetaBoxFieldNumber::STEP => 1,
            MetaBoxField::ID => PostProperty::META_ROOMS,
            MetaBoxField::NAME => $this->getIcon('bed') . __('Rooms', WpApp::TEXT_DOMAIN),
            MetaBoxField::DESCRIPTION => sprintf($textValueExample, '3')];
        //Field: Floor
        $propertyGeneral[MetaBox::FIELDS][] = [MetaBoxFieldNumber::TYPE => MetaBoxFieldType::NUMBER,
            MetaBoxFieldNumber::COLUMNS => 4,
            MetaBoxFieldNumber::MIN => 0,
            MetaBoxFieldNumber::STEP => 1,
            MetaBoxFieldNumber::ID => PostProperty::META_FLOOR,
            MetaBoxFieldNumber::NAME => $this->getIcon('indent') . __('Floor', WpApp::TEXT_DOMAIN),
            MetaBoxFieldNumber::DESCRIPTION => sprintf($textValueExample, '4')];
        //Field: Floors
        $propertyGeneral[MetaBox::FIELDS][] = [MetaBoxFieldNumber::TYPE => MetaBoxFieldType::NUMBER,
            MetaBoxFieldNumber::COLUMNS => 4,
            MetaBoxFieldNumber::MIN => 0,
            MetaBoxFieldNumber::STEP => 0.1,
            MetaBoxFieldNumber::ID => PostProperty::META_FLOORS,
            MetaBoxFieldNumber::NAME => $this->getIcon('list-ol') . __('Floors', WpApp::TEXT_DOMAIN),
            MetaBoxFieldNumber::DESCRIPTION => sprintf($textValueExample, '10')];
        //Field: Rooms Height
        $propertyGeneral[MetaBox::FIELDS][] = [MetaBoxFieldNumber::TYPE => MetaBoxFieldType::NUMBER,
            MetaBoxFieldNumber::COLUMNS => 6,
            MetaBoxFieldNumber::MIN => 0,
            MetaBoxFieldNumber::STEP => 0.01,
            MetaBoxFieldNumber::ID => PostProperty::META_ROOMS_HEIGHT,
            MetaBoxFieldNumber::NAME => $this->getIcon('arrows-v') . __('Rooms Height', WpApp::TEXT_DOMAIN),
            MetaBoxFieldNumber::DESCRIPTION => sprintf($textValueExample, '2')];
        //Field: Rooms Height Measure Unit
        $propertyGeneral[MetaBox::FIELDS][] = [MetaBoxFieldSelect::TYPE => MetaBoxFieldType::SELECT,
            MetaBoxFieldSelect::COLUMNS => 6,
            MetaBoxFieldSelect::OPTIONS => self::getRoomsHeightMeasureUnits(),
            MetaBoxFieldSelect::MULTIPLE => false,
            MetaBoxFieldSelect::STD => PostProperty::ROOMS_HEIGHT_UNIT_METER,
            MetaBoxFieldSelect::ID => PostProperty::META_ROOMS_HEIGHT_UNIT,
            MetaBoxFieldSelect::NAME => sprintf(__("%s measure unit", WpApp::TEXT_DOMAIN), __("Height")),
            MetaBoxFieldSelect::DESCRIPTION => sprintf($textValueExample,
                                                       self::getRoomsHeightMeasureUnits()[PostProperty::ROOMS_HEIGHT_UNIT_METER])];
        //Field: Kitchen Area Size
        $propertyGeneral[MetaBox::FIELDS][] = [MetaBoxFieldNumber::TYPE => MetaBoxFieldType::NUMBER,
            MetaBoxFieldNumber::COLUMNS => 6,
            MetaBoxFieldNumber::MIN => 0,
            MetaBoxFieldNumber::STEP => 0.01,
            MetaBoxFieldNumber::ID => PostProperty::META_SIZE_KITCHEN,
            MetaBoxFieldNumber::NAME => $this->getIcon('utensils') . __('Kitchen Area Size', WpApp::TEXT_DOMAIN),
            MetaBoxFieldNumber::DESCRIPTION => sprintf($textValueExample, '5')];
        //Field: Shower
        $propertyGeneral[MetaBox::FIELDS][] = [MetaBoxFieldNumber::TYPE => MetaBoxFieldType::NUMBER,
            MetaBoxFieldNumber::COLUMNS => 6,
            MetaBoxFieldNumber::MIN => 0,
            MetaBoxFieldNumber::STEP => 1,
            MetaBoxFieldNumber::ID => PostProperty::META_LAVATORIES,
            MetaBoxFieldNumber::NAME => $this->getIcon('shower') . __('Lavatory', WpApp::TEXT_DOMAIN),
            MetaBoxFieldNumber::DESCRIPTION => sprintf($textValueExample, '1')];
        //Field: Balcony
        $propertyGeneral[MetaBox::FIELDS][] = [MetaBoxFieldNumber::TYPE => MetaBoxFieldType::NUMBER,
            MetaBoxFieldNumber::COLUMNS => 6,
            MetaBoxFieldNumber::MIN => 0,
            MetaBoxFieldNumber::STEP => 1,
            MetaBoxFieldNumber::ID => PostProperty::META_BALCONIES,
            MetaBoxFieldNumber::NAME => __('Balcony', WpApp::TEXT_DOMAIN),
            MetaBoxFieldNumber::DESCRIPTION => sprintf($textValueExample, '1')];
        //Field: Garage
        $propertyGeneral[MetaBox::FIELDS][] = [MetaBoxFieldNumber::ID => PostProperty::META_GARAGES,
            MetaBoxFieldNumber::TYPE => MetaBoxFieldType::NUMBER,
            MetaBoxFieldNumber::COLUMNS => 6,
            MetaBoxFieldNumber::MIN => 0,
            MetaBoxFieldNumber::STEP => 1,
            MetaBoxFieldNumber::NAME => $this->getIcon("car-garage") . __('Garage', WpApp::TEXT_DOMAIN),
            MetaBoxFieldNumber::DESCRIPTION => sprintf($textValueExample, '1')];
        if (is_child_theme()) {
            $propertyGeneral[MetaBox::FIELDS][] = [MetaBoxFieldNumber::ID => PostProperty::META_DISTANCE_TO_SEA,
                MetaBoxFieldNumber::TYPE => MetaBoxFieldType::NUMBER,
                MetaBoxFieldNumber::COLUMNS => 6,
                MetaBoxFieldNumber::MIN => 0,
                MetaBoxFieldNumber::STEP => 0.01,
                MetaBoxFieldNumber::NAME => $this->getIcon("water") . __('Distance to the sea', WpApp::TEXT_DOMAIN),
                MetaBoxFieldNumber::DESCRIPTION => sprintf($textValueExample, '100')];
            $propertyGeneral[MetaBox::FIELDS][] = [MetaBoxFieldSelect::TYPE => MetaBoxFieldType::SELECT,
                MetaBoxFieldSelect::COLUMNS => 6,
                MetaBoxFieldSelect::OPTIONS => self::getDistanceToTheSeaUnits(),
                MetaBoxFieldSelect::MULTIPLE => false,
                MetaBoxFieldSelect::STD => PostProperty::UNIT_METER,
                MetaBoxFieldSelect::ID => PostProperty::META_DISTANCE_TO_SEA_UNIT,
                MetaBoxFieldSelect::NAME => sprintf(__('%s measure unit', WpApp::TEXT_DOMAIN), __("Distance", WpApp::TEXT_DOMAIN)),
                MetaBoxFieldSelect::DESCRIPTION => sprintf($textValueExample,
                                                           self::getDistanceToTheSeaUnits()[PostProperty::UNIT_METER])];
        }
        //Taxonomy: Features
        $propertyGeneral[MetaBox::FIELDS][] = [MetaBoxFieldTaxonomy::ID => PostProperty::TAX_FEATURE,
            MetaBoxFieldTaxonomy::TYPE => MetaBoxFieldType::TAXONOMY,
            MetaBoxFieldTaxonomy::TAXONOMY => PostProperty::TAX_FEATURE,
            MetaBoxFieldTaxonomy::FIELD_TYPE => MetaBoxFieldTaxonomyType::SELECT_ADVANCED,
            MetaBoxFieldSelectAdvanced::MULTIPLE => true,
            MetaBoxFieldTaxonomy::COLUMNS => 12,
            MetaBoxFieldTaxonomy::NAME => $this->getIcon("tasks") . __('Property Features', WpApp::TEXT_DOMAIN),
            MetaBoxFieldTaxonomy::PLACEHOLDER => sprintf($textChoose, __('Property Features', WpApp::TEXT_DOMAIN)),
            MetaBoxFieldTaxonomy::ADD_TO_SEO => true];
        //Field: Content Description
        $propertyGeneral[MetaBox::FIELDS][] = [MetaBoxFieldTextArea::ID => 'content',
            'post_types' => PostProperty::TYPE,
            MetaBoxFieldTextArea::TYPE => MetaBoxFieldType::WYSIWYG,
            'options' => ['media_buttons' => false, 'textarea_rows' => 3, 'quicktags' => false, 'teeny' => true],
            MetaBoxFieldTextArea::STD => esc_html($default_content),
            MetaBoxFieldTextArea::COLUMNS => 12,
            MetaBoxFieldTextArea::NAME => $this->getIcon('file-alt') . __('Description')];
        $propertyGeneral[MetaBox::FIELDS][] = [MetaBoxFieldTextArea::TYPE => MetaBoxFieldType::CUSTOM_HTML,
            MetaBoxFieldTextArea::STD => '<style>#wp-content-editor-tools { background: none; padding-top: 0; }</style>'];
        //Field: Offer Finalized
        $propertyGeneral[MetaBox::FIELDS][] = [MetaBoxFieldSwitch::ID => PostProperty::META_OFFER_FINALIZED,
            MetaBoxFieldSwitch::TYPE => MetaBoxFieldType::SWITCH,
            MetaBoxFieldSwitch::COLUMNS => 12,
            MetaBoxFieldSwitch::STD => 0,
            MetaBoxFieldSwitch::NAME => $this->getIcon('calendar-check') . __('Offer is finalized?', WpApp::TEXT_DOMAIN),
            MetaBoxFieldSwitch::LABEL_ON => __('Yes'),
            MetaBoxFieldSwitch::LABEL_OFF => __('No')];
        $meta_boxes[] = $propertyGeneral;
        //Add Taxonomy Meta
        $meta_boxes[] = [MetaBoxTerm::TITLE => __("Additional Details", WpApp::TEXT_DOMAIN),
            MetaBoxTerm::TAXONOMIES => PostProperty::TAX_TYPE,
            MetaBoxTerm::FIELDS => [[MetaBoxFieldInput::ID => self::META_TAX_TYPE_NAME_PLURAL,
                MetaBoxFieldInput::TYPE => MetaBoxFieldType::TEXT,
                MetaBoxFieldInput::NAME => __("Name plural", WpApp::TEXT_DOMAIN)]]];
        return $meta_boxes;
    }

    const ID = 'id';
    const ID_BOX = 'mbProperty';
    const ID_BOX_MAP = 'mbPropertyMap';
    const META_ADDRESS = "propertyMetaAddress";
    const META_LOCATION = "propertyMetaLocation";
    const META_SHOW_MAP = "propertyMetaShowMap";
    const META_OFFER_FINALIZED = "propertyMetaOfferFinalized";

    static function getOfferStatus() {
        /** @var $typeTerm WP_Term */
        $typeTerm = self::getTaxonomyFirstTerm(PostProperty::TAX_STATUS);
        if (is_a($typeTerm, 'WP_Term')) {
            if ($typeTerm->slug == self::CONTRACT_RENT) {
                return __("Rented", WpApp::TEXT_DOMAIN);
            } else {
                return __("Sold", WpApp::TEXT_DOMAIN);
            }
        }
        return "";
    }

    static function getOfferStatusMarkup() {
        $result = "";
        $status = get_post_meta(get_the_ID(), PostProperty::META_OFFER_FINALIZED, true);
        if ($status) {
            $result = sprintf('<div class="cover-white"><span>%s</span></div>', self::getOfferStatus());
        }
        return $result;
    }

    const META_FLOOR = "propertyMetaFloor";
    const META_FLOORS = "propertyMetaFloors";
    const META_BEDROOMS = "propertyMetaBedrooms";
    const META_BATHROOMS = "propertyMetaBathrooms";
    const META_LAVATORIES = "propertyMetaLavatories";
    const META_BALCONIES = "propertyMetaBalconies";
    const META_GARAGES = "propertyMetaGarages";
    const META_PRICE = "propertyMetaPrice";
    const META_PRICE_PROMO = "propertyMetaPricePromo";
    const META_PARTNER = "propertyMetaPartner";
    const META_FINANCING_PARTNER = "propertyMetaFinancingPartner";
    const META_FINANCING_FIRST_RATE = "propertyMetaFinancingFirstRate";
    const META_FINANCING_MONTHLY_PAYMENT = "propertyMetaFinancingMonthlyPayment";

    static function getValueFor($data, $default = "") {
        $result = $default;
        $value = get_post_meta(get_the_ID(), $data, true);
        if ($value) {
            $result = $value;
        }
        return $result;
    }

    static function getPartnerId() {
        return self::getValueFor(PostProperty::META_PARTNER);
    }

    protected static $propertyFields;

    static function getPropertyFields() {
        if (!self::$propertyFields) {
            self::$propertyFields = [PostProperty::META_SIZE => __("Area Size", WpApp::TEXT_DOMAIN),
                PostProperty::META_SIZE_KITCHEN => __("Kitchen Area Size", WpApp::TEXT_DOMAIN),
                PostProperty::META_ROOMS => __("Rooms", WpApp::TEXT_DOMAIN),
                PostProperty::META_ROOMS_HEIGHT => __("Rooms Height", WpApp::TEXT_DOMAIN),
                PostProperty::META_FLOOR => __("Floor", WpApp::TEXT_DOMAIN),
                PostProperty::META_FLOORS => __("Floors", WpApp::TEXT_DOMAIN),
                PostProperty::META_LAVATORIES => __("Lavatory", WpApp::TEXT_DOMAIN),
                PostProperty::META_BEDROOMS => __("Bedrooms", WpApp::TEXT_DOMAIN),
                PostProperty::META_BATHROOMS => __("Bathrooms", WpApp::TEXT_DOMAIN),
                PostProperty::META_BALCONIES => __("Balcony", WpApp::TEXT_DOMAIN),
                PostProperty::META_SIZE_OF_LAND => __("Plot of land size", WpApp::TEXT_DOMAIN),
                PostProperty::META_DISTANCE_TO_SEA => __("To the sea", WpApp::TEXT_DOMAIN)];
        }
        return self::$propertyFields;
    }

    const CONTRACT_RENT = "rent";
    const CONTRACT_SALE = "sale";
    protected static $contracts;

    static function getContracts($itemsWithContract = 1) {
        if (!self::$contracts) {
            $textForRent = _n("For rent", "For rents", $itemsWithContract, WpApp::TEXT_DOMAIN);
            $textForSale = _n("For sale", "For rents", $itemsWithContract, WpApp::TEXT_DOMAIN);
            self::$contracts = [self::CONTRACT_RENT => $textForRent, self::CONTRACT_SALE => $textForSale];
        }
        return self::$contracts;
    }

    const META_PRICE_RENT_CRITERIA = "propertyMetaPriceRentCriteria";//'propertyPriceRentalCriteria'
    const RENT_CRITERIA_DAY = "per-day";
    const RENT_CRITERIA_WEEK = "per-week";
    const RENT_CRITERIA_MONTH = "per-month";
    const RENT_CRITERIA_YEAR = "per-year";
    protected static $rentalPriceCriteria;

    static function getRentalPriceCriteria($recreate = false) {
        if (!self::$rentalPriceCriteria || $recreate) {
            self::$rentalPriceCriteria = [self::UNIT_SQM_SYMBOL => __('per m²', WpApp::TEXT_DOMAIN),
                self::RENT_CRITERIA_DAY => __('per day', WpApp::TEXT_DOMAIN),
                self::RENT_CRITERIA_WEEK => __('per week', WpApp::TEXT_DOMAIN),
                self::RENT_CRITERIA_MONTH => __('per month', WpApp::TEXT_DOMAIN),
                self::RENT_CRITERIA_YEAR => __('per year', WpApp::TEXT_DOMAIN)];
        }
        return self::$rentalPriceCriteria;
    }

    const META_PRICE_CURRENCY = 'propertyMetaPriceCurrency';
    protected static $priceCurrency;
    static function getPriceCurrency($recreate = false) {
        if (!self::$priceCurrency || $recreate) {
            self::$priceCurrency = ['eur'=>'EUR','usd'=>'USD','mdl'=>'MDL'];
        }
        return self::$priceCurrency;
    }
    const META_PRICE_PREFIX = 'propertyMetaPriceRentCriteria';//'propertyPriceRentalCriteria'
    const META_PRICE_PREFIX_FROM = 'propertyMetaPricePrefixFrom';
    const META_PRICE_PREFIX_TO = 'propertyMetaPricePrefixTo';
    const META_PRICE_PREFIX_MIN = 'propertyMetaPricePrefixMin';
    const META_PRICE_PREFIX_MAX = 'propertyMetaPricePrefixMax';
    protected static $pricePrefix;

    static function getPricePrefix($recreate = false) {
        if (!self::$pricePrefix || $recreate) {
            self::$pricePrefix = ['' => __('None'),
                PostProperty::META_PRICE_PREFIX_FROM => __('from', WpApp::TEXT_DOMAIN),
                PostProperty::META_PRICE_PREFIX_TO => __('to', WpApp::TEXT_DOMAIN),
                PostProperty::META_PRICE_PREFIX_MIN => __('min.', WpApp::TEXT_DOMAIN),
                PostProperty::META_PRICE_PREFIX_MAX => __('max.', WpApp::TEXT_DOMAIN)];
        }
        return self::$pricePrefix;
    }

    static function getPriceValueByMeta($metaKey) {
        $postId = get_the_ID();
        $priceValue = get_post_meta($postId, $metaKey, true);
        return doubleval($priceValue);
    }

    static function formatPriceByValue($price) {
        $result = "";
        if ($price) {
            $currency = get_option('theme_currency_sign');
            $decimals = intval(get_option('theme_decimals'));
            $decimal_point = get_option('theme_dec_point');
            $thousands_separator = get_option('theme_thousands_sep');
            $currencyPosition = get_option('theme_currency_position');
            $priceFormatted = number_format($price, $decimals, $decimal_point, $thousands_separator);
            if ($currencyPosition == 'after') {
                $result = "$priceFormatted $currency";
            } else {
                $result = "$currency $priceFormatted";
            }
        }
        return $result;
    }

    static function formatPriceByMeta($priceMetaKey) {
        return self::formatPriceByValue(self::getPriceValueByMeta($priceMetaKey));
    }

    static function getPriceFormatted() {
        $result = "";
        /** @var $term WP_Term */
        $price = self::getPrice();
        $pricePromo = self::getPricePromoValue();
        if ($pricePromo) {
            $price = self::formatPriceByValue($price);
            $pricePromo = self::formatPriceByValue($pricePromo);
            $result .= sprintf("<span><small style='text-decoration: line-through;'>%s</small> %s</span>", $price,
                               $pricePromo);
        } else {
            $result .= sprintf("<span>%s</span>", self::formatPriceByValue($price));
        }
        if (has_term(self::CONTRACT_RENT, PostProperty::TAX_STATUS, get_the_ID())) {
            $result .= sprintf(" <small>%s</small>", self::getPriceRentCriteria());
        }
        return $result;
    }

    static function getFinancingFirstRateFormatted() {
        return self::formatPriceByMeta(PostProperty::META_FINANCING_FIRST_RATE);
    }

    static function getFinancingMonthlyPaymentFormatted() {
        return self::formatPriceByMeta(PostProperty::META_FINANCING_MONTHLY_PAYMENT);
    }

    static function getPrice() {
        return self::getPriceValueByMeta(PostProperty::META_PRICE);
    }

    static function getPricePromoValue() {
        return self::getPriceValueByMeta(PostProperty::META_PRICE_PROMO);
    }

    static function getPriceRentCriteria() {
        $result = "";
        $postId = get_the_ID();
        $priceRentCriteria = get_post_meta($postId, PostProperty::META_PRICE_RENT_CRITERIA, true);
        if (isset(self::$rentalPriceCriteria[$priceRentCriteria])) {
            $result = self::$rentalPriceCriteria[$priceRentCriteria];
        }
        return $result;
    }

    const META_DISTANCE_TO_SEA = "propertyMetaDistanceToSea";
    const META_DISTANCE_TO_SEA_UNIT = "propertyDistanceMetaToUnit";
    const UNIT_METER = "m";
    const UNIT_KILOMETER = "km";
    protected static $distanceToTheSeaUnits;

    static function getDistanceToTheSeaUnits() {
        if (!self::$distanceToTheSeaUnits) {
            self::$distanceToTheSeaUnits = [self::UNIT_METER => __("m", WpApp::TEXT_DOMAIN),
                self::UNIT_KILOMETER => __("km", WpApp::TEXT_DOMAIN)];
        }
        return self::$distanceToTheSeaUnits;
    }

    const META_ROOMS = "propertyMetaRooms";

    static function getRooms() {
        return self::getValueFor(PostProperty::META_ROOMS, 0);
    }

    //Rooms
    static function getRoomsFormatted() {
        $rooms = self::getRooms();
        return sprintf(_n('%s room', '%s rooms', $rooms, WpApp::TEXT_DOMAIN), "<strong>$rooms</strong>");
    }

    const META_ROOMS_HEIGHT = "propertyMetaRoomsHeight";
    const META_ROOMS_HEIGHT_UNIT = "propertyMetaRoomsHeightUnit";//'propertyRoomsHeightPostfix'
    const ROOMS_HEIGHT_UNIT_METER = "m";
    const ROOMS_HEIGHT_UNIT_CENTIMETER = "cm";
    const ROOMS_HEIGHT_UNIT_MILLIMETER = "mm";
    protected static $roomsHeightMeasureUnits;

    static function getRoomsHeightMeasureUnits() {
        if (!self::$roomsHeightMeasureUnits) {
            self::$roomsHeightMeasureUnits = [self::ROOMS_HEIGHT_UNIT_METER => __("m", WpApp::TEXT_DOMAIN),
                self::ROOMS_HEIGHT_UNIT_CENTIMETER => __("cm", WpApp::TEXT_DOMAIN),
                self::ROOMS_HEIGHT_UNIT_MILLIMETER => __("mm", WpApp::TEXT_DOMAIN)];
        }
        return self::$roomsHeightMeasureUnits;
    }

    const META_SIZE_OF_LAND = "propertySizeOfLand";
    const META_SIZE_OF_LAND_UNIT = "propertySizeOfLandUnit";

    static function getSizeOfLand() {
        return self::getValueFor(PostProperty::META_SIZE_OF_LAND, 0);
    }

    //Rooms
    static function getSizeOfLandFormatted() {
        $rooms = self::getSizeOfLand();
        $measureUnit = self::getMeasureUnit(PostProperty::META_SIZE_OF_LAND, $rooms);
        return sprintf('<strong>%1$s</strong> %2$s', $rooms, $measureUnit);
    }

    const LAND_UNIT_HECTARE = "hectare";
    const LAND_UNIT_ACRE = "acre";
    const LAND_UNIT_ARE = "are";
    protected static $sizeUnits = [PostProperty::META_SIZE => PostProperty::META_SIZE_UNIT,
        PostProperty::META_SIZE_KITCHEN => PostProperty::META_SIZE_UNIT,
        PostProperty::META_ROOMS_HEIGHT => PostProperty::META_ROOMS_HEIGHT_UNIT,
        PostProperty::META_SIZE_OF_LAND => PostProperty::META_SIZE_OF_LAND_UNIT];

    static function getMeasureUnit($key, $value = '') {
        $keyPostfixContent = "";
        $postId = get_the_ID();
        $metaValue = "";
        $measureUnits = [];
        switch ($key) {
        case PostProperty::META_SIZE:
        case PostProperty::META_SIZE_KITCHEN:
            {
                $metaValue = get_post_meta($postId, PostProperty::META_SIZE_UNIT, true);
                $measureUnits = self::getSizeMeasureUnits();
                break;
            }
        case PostProperty::META_DISTANCE_TO_SEA:
            {
                $metaValue = get_post_meta($postId, PostProperty::META_DISTANCE_TO_SEA_UNIT, true);
                $measureUnits = self::getDistanceToTheSeaUnits();
                break;
            }
        case PostProperty::META_ROOMS_HEIGHT:
            {
                $metaValue = get_post_meta($postId, PostProperty::META_ROOMS_HEIGHT_UNIT, true);
                $measureUnits = self::getRoomsHeightMeasureUnits();
                break;
            }
        case PostProperty::META_SIZE_OF_LAND:
            {
                $metaValue = get_post_meta($postId, PostProperty::META_SIZE_OF_LAND_UNIT, true);
                $measureUnits = self::getLandMeasureUnits($value);
                break;
            }
        }
        if (isset($measureUnits[$metaValue])) {
            $keyPostfixContent = " $measureUnits[$metaValue]";
        }
        return $keyPostfixContent;
    }

    static function getLandMeasureUnits($value = 1, $prefixValue = '') {
        return [self::LAND_UNIT_HECTARE => sprintf(_n('%s hectare', '%s hectares', $value, WpApp::TEXT_DOMAIN), $prefixValue),
            self::LAND_UNIT_ACRE => sprintf(_n('%s acre', '%s acres', $value, WpApp::TEXT_DOMAIN), $prefixValue),
            self::LAND_UNIT_ARE => sprintf(_n('%s are', '%s ares', $value, WpApp::TEXT_DOMAIN), $prefixValue),
            self::UNIT_SQM_SYMBOL => sprintf(_n('%s m²', '%s m²', $value, WpApp::TEXT_DOMAIN), $prefixValue)];
    }

    const META_SIZE = "propertyMetaSize";

    static function getSize() {
        return self::getValueFor(PostProperty::META_SIZE, 0);
    }

    const META_SIZE_KITCHEN = "propertyMetaSizeKitchen";
    const META_SIZE_UNIT = "propertyMetaSizeUnit";//'propertySizePostfix'
    const UNIT_SQM_SYMBOL = "m²";
    const UNIT_SQM = "sq.m.";
    protected static $sizeMeasureUnits;

    static function getSizeMeasureUnits() {
        if (!self::$sizeMeasureUnits) {
            self::$sizeMeasureUnits = [self::UNIT_SQM_SYMBOL => __("m²", WpApp::TEXT_DOMAIN), self::UNIT_SQM => __("sq.m.", WpApp::TEXT_DOMAIN)];
        }
        return self::$sizeMeasureUnits;
    }

    static function getSizeUnit() {
        return self::getValueFor(PostProperty::META_SIZE_UNIT, self::UNIT_SQM_SYMBOL);
    }

    //Area
    static function getSizeFormatted() {
        return sprintf('<strong>%s</strong> %s', self::getSize(), self::getSizeUnit());
    }

    static function getTaxonomyFirstTerm($taxonomy) {
        $result = "";
        $postId = get_the_ID();
        $terms = get_the_terms($postId, $taxonomy);
        if (is_array($terms)) {
            $result = $terms[0];
        }
        return $result;
    }

    static function getStatusFormatted() {
        $content = "";
        $typeTerm = self::getTaxonomyFirstTerm(PostProperty::TAX_STATUS);
        $typeTermLink = get_term_link($typeTerm, PostProperty::TAX_STATUS);
        if (is_a($typeTerm, 'WP_Term')) {
            $content .= "<a href='{$typeTermLink}'>{$typeTerm->name}</a>";
        }
        return $content;
    }

    static function getTypeFormatted() {
        $typeTerm = self::getTaxonomyFirstTerm(PostProperty::TAX_TYPE);
        if ($typeTerm) {
            $termLink = get_term_link($typeTerm);
            $termName = $typeTerm->name;
        } else {
            $postId = get_the_ID();
            $termName = "----";
            $termLink = get_the_permalink($postId);
        }
        return "<a href='{$termLink}' title='{$termName}'>{$termName}</a>";
    }

    static function getLocationFormatted($regionSeparator = ", ") {
        $postId = get_the_ID();
        $markup = '<a href="%1$s" title="%2$s">%2$s</a>';
        $terms = get_the_terms($postId, PostProperty::TAX_LOCATION);
        if (is_array($terms)) {
            // @var $term WP_Term
            $term = $terms[0];
            $termName = $term->name;
            $termLink = get_term_link($term, PostProperty::TAX_LOCATION);
            $content = sprintf($markup, $termLink, $termName);
            if ($term->parent) {
                $term = get_term($term->parent, PostProperty::TAX_LOCATION);
                $termName = $term->name;
                $termLink = get_term_link($term, PostProperty::TAX_LOCATION);
                $content = sprintf($markup, $termLink, $termName) . $regionSeparator . $content;
            }
        } else {
            $termName = "----";
            $termLink = get_the_permalink($postId);
            $content = sprintf($markup, $termLink, $termName);
        }
        return $content;
    }

    static function getFullNameFormatted() {
        $formattedStatus = self::getStatusFormatted();
        $formattedType = self::getTypeFormatted();
        $formattedLocation = self::getTypeFormatted();
        $formattedPrice = self::getPriceFormatted();
        $markup = "{$formattedStatus} {$formattedType} {$formattedLocation} - {$formattedPrice}";
        return $markup;
    }

    static function getMetaValueRange($metaKey, $minimum = true, $defaultValue = 0) {
        $result = $defaultValue;
        $queryValueMin = new WP_Query([QueryPost::TYPE => self::TYPE,
                                          QueryPost::ORDER_BY => WPOrderBy::META_VALUE,
                                          QueryPost::ORDER => $minimum ? WPOrder::ASC : WPOrder::DESC,
                                          QueryPost::META_KEY => $metaKey,
                                          QueryPost::PER_PAGE => 1]);
        if ($queryValueMin->have_posts()) {
            $queryValueMin->the_post();
            $result = intval(self::getValueFor($metaKey));
        }
        wp_reset_postdata();
        return $result;
    }

    static function getAllMetaValues($metaKey, $nameSingular, $namePlural) {
        $result = [];
        $queryValues = new WP_Query([QueryPost::TYPE => self::TYPE,
                                        QueryPost::ORDER_BY => WPOrderBy::META_VALUE,
                                        QueryPost::ORDER => WPOrder::ASC,
                                        QueryPost::META_KEY => $metaKey,
                                        QueryPost::PER_PAGE => -1]);
        while ($queryValues->have_posts()) {
            $queryValues->the_post();
            $value = intval(self::getValueFor($metaKey));
            $result[$value] = $value . ' ' . _n($nameSingular, $namePlural, $value, WpApp::TEXT_DOMAIN);
        }
        wp_reset_postdata();
        return $result;
    }

    static function getAllRooms() {
        return self::getAllMetaValues(PostProperty::META_ROOMS, "room", "rooms");
    }

    static function getFloorsValue() {
        return self::getPriceValueByMeta(PostProperty::META_FLOORS);
    }

    static function getFloor() {
        return self::getPriceValueByMeta(PostProperty::META_FLOOR);
    }

    static function getAllFloors() {
        return self::getAllMetaValues(PostProperty::META_FLOORS, "floor", "floors");
    }

    static function getSiteMinPrice() {
        //TODO Make Site option for Default Minimum Price Value
        return self::getMetaValueRange(PostProperty::META_PRICE);
    }

    static function getSiteMaxPrice() {
        //TODO Make Site option for Default Maximum Price Value
        return self::getMetaValueRange(PostProperty::META_PRICE, false, 1000000000);
    }

    static function getSiteMinSize() {
        //TODO Make Site option for Default Minimum Size Value
        return self::getMetaValueRange(PostProperty::META_SIZE);
    }

    static function getSiteMaxSize() {
        //TODO Make Site option for Default Maximum Size Value
        return self::getMetaValueRange(PostProperty::META_SIZE, false, 10000000);
    }

    static function getCoordinates($address) {
        $address = str_replace(' ', '+',
                               $address); // replace all the white space with "+" sign to match with google search pattern
        $url = "http://maps.google.com/maps/api/geocode/json?sensor=false&address=$address";
        $response = file_get_contents($url);
        $json = json_decode($response, true); //generate array object from the response from the web
        $result = ['lat' => '', 'lng' => ''];
        if ($json && isset($json['results']) && isset($json['results'][0]) && isset($json['results'][0]['geometry'])) {
            $result = $json['results'][0]['geometry']['location'];
        }
        return $result;
    }
}