<?php namespace wp;
use WP_Customize_Cropped_Image_Control;
use WP_Customize_Manager;

class Customizer
{
    protected static $instance = null;

    public static function i()
    {
        if (!self::$instance) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    protected function __construct()
    {
        //add_action(WPActions::CUSTOMIZER_REGISTER, [$this, 'removeCssSection'], 15);
        add_action(WPActions::CUSTOMIZER_REGISTER, [$this, 'registerCustomizer']);
    }

    /**
     * Remove the additional CSS section, introduced in 4.7, from the Customizer.
     * @param $customizer WP_Customize_Manager
     */
    function removeCssSection(WP_Customize_Manager $customizer)
    {
        $customizer->remove_section('custom_css');
    }

    /**
     * Init Selective Section refresh utility function
     * @param WP_Customize_Manager $customizer
     * @param $idOption
     */
    function initSelectiveRefresh(WP_Customize_Manager $customizer, $idOption)
    {
        $customizer->get_setting($idOption)->transport = CustomizerTransport::POST_MESSAGE;
        $customizer->selective_refresh->add_partial($idOption, [
            'selector' => ".{$idOption}",
            'render_callback' => function () use ($idOption) {
                return get_option($idOption);
            },
        ]);
    }

    /**
     * Init Customizer
     * @param WP_Customize_Manager $customizer
     */
    function registerCustomizer(WP_Customize_Manager $customizer)
    {
        add_action(WPActions::ENQUEUE_SCRIPTS_CUSTOMIZER, [$this, 'enqueueScriptsCustomizer']);
        add_action(WPActions::CUSTOMIZER_INIT, [$this, 'enqueueScriptsCustomizerPreview']);
        add_action(WPActions::CUSTOMIZER_AFTER_SAVE, [$this, 'handleActionAfterSave']);
        if (empty($customizer->widgets) == false) {
            if (is_admin()) {
                $this->registerSidebarSettings(); //Before Customizer Section Loaded
            } else {
                add_action('wp', [$this, 'registerSidebarSettings'], 100); //After All Customizer Section Loaded
            }
        }
        // Section: Identity
        $this->registerSectionIdentity($customizer);
        //Section: Init Defaults
        $this->initSelectiveRefresh($customizer, WPOptions::SITE_NAME);
        $this->initSelectiveRefresh($customizer, WPOptions::SITE_DESCRIPTION);
        $this->initSelectiveRefresh($customizer, SettingsSite::EMAIL);
        $this->initSelectiveRefresh($customizer, SettingsSite::PHONES);
        $this->initSelectiveRefresh($customizer, SettingsSite::ADDRESS);
        // Section: Google
         $this->registerSectionGoogle($customizer);
        // Section: Payments
        // $this->registerSectionPayments($customizer);
    }

    /**
     * Load Styles & Scripts for: Customizer
     */
    function enqueueScriptsCustomizer()
    {
        $uriToDirLibs = UtilsWp::getUriToLibsDir();
        wp_enqueue_style('customizer', "{$uriToDirLibs}/customizer.css", ['fa-all']);
        $uriToDirCustomizer = "{$uriToDirLibs}/customizer/";
        //wp_enqueue_style('customizer-general', $uriToDirCustomizer . 'general.css');
        wp_enqueue_script('customizer-general', $uriToDirCustomizer . 'general.js',
            ['jquery', 'customize-base'], false, true);
        if (is_rtl()) {
            wp_enqueue_style('customizer-rtl', $uriToDirCustomizer . 'rtl.css');
        }
    }

    /**
     * Load Styles & Scripts for: Customizer Preview Iframe
     */
    function enqueueScriptsCustomizerPreview()
    {
        $uriToDirLibs = UtilsWp::getUriToLibsDir();
        wp_enqueue_script('customizer', "{$uriToDirLibs}/customizer.js", ['jquery', 'customize-preview'],
            '', true);
    }

    /**
     * Set default values for Sections. Initialize default values for settings as customizer api do not do so by default.
     * @param WP_Customize_Manager $customizer
     */
    function handleActionAfterSave(WP_Customize_Manager $customizer)
    {
        $settingsIDs = [
            // Set default values for Section: Identity
            SettingsSite::EMAIL,
            SettingsSite::PHONES,
            SettingsSite::ADDRESS,
            SettingsSite::REGISTRATION_ENABLED,
            SettingsSite::BACKEND_ACCESS_LEVEL,
            SettingsSite::EMAIL_CC_ENABLE,
            // Set default values for Section: Google Services
            SettingsSite::RECAPTCHA_ENABLED,
            SettingsSite::RECAPTCHA_KEY_PUBLIC,
            SettingsSite::RECAPTCHA_KEY_PRIVATE,
            SettingsSite::GOOGLE_MAP_API,
            /*// Set default values for Section: Payments
            SettingsSite::CURRENCY_SIGN,
            SettingsSite::CURRENCY_POSITION,
            SettingsSite::CURRENCY_DECIMALS,
            SettingsSite::CURRENCY_DECIMAL_POINT,
            SettingsSite::CURRENCY_THOUSANDS_SEPARATOR,*/
        ];
        foreach ($settingsIDs as $settingID) {
            $setting = $customizer->get_setting($settingID);
            if ($setting) {
                add_option($setting->id, $setting->default);
            }
        }
    }

    function registerSidebarSettings()
    {
        global $wp_customize;
        $widgetAreaOptions = Widget::WIDGET_AREA;
        $widgetAreaControlOptions = Widget::CSS_CLASSES;
        $widgetAreaScriptData = '';
        foreach ($wp_customize->sections() as $section) {
            if ($section instanceof \WP_Customize_Sidebar_Section) {
                $widgetAreaId = $section->sidebar_id;
                $settingId = "{$widgetAreaOptions}[{$widgetAreaId}][{$widgetAreaControlOptions}]";
                $setting = $wp_customize->add_setting($settingId, [
                    CustomizerSettingArgs::TYPE => CustomizerSettingType::OPTION,
                    CustomizerSettingArgs::SANITITZE_CALLBACK => CustomizerSanitize::TEXT,
                    CustomizerSettingArgs::STD => '',
                    CustomizerSettingArgs::TRANSPORT => CustomizerTransport::POST_MESSAGE
                ]);
                $wp_customize->add_control($settingId, [
                    CustomizerControlArgs::SECTION => $section->id,
                    CustomizerControlArgs::SIDEBAR_ID => $widgetAreaId,
                    CustomizerControlArgs::TYPE => CustomizerInputTypes::TEXT,
                    CustomizerControlArgs::LABEL => __('CSS Classes'),
                    CustomizerControlArgs::PRIORITY => -1
                ]);
                // Handle previewing of late-created settings.
                if (did_action(WPActions::CUSTOMIZER_INIT)) {
                    $setting->preview();
                }
                $widgetAreaScriptData .= "wp.customize('{$settingId}', function (value) {
                    value.bind(function (newValue) {
                        var widgetAreaId = '{$widgetAreaId}';
                        var widgetArea = jQuery(document.getElementById(widgetAreaId));
                        console.log(widgetAreaId,widgetArea);
                        widgetArea.removeClass();
                        widgetArea.addClass('widget-area '+ widgetAreaId + ' ' + newValue);
                    });
                });\n";
            }
        }
        wp_add_inline_script('customizer', $widgetAreaScriptData);
    }

    /**
     * Register Section: Identity
     * @param WP_Customize_Manager $customizer
     */
    function registerSectionIdentity(WP_Customize_Manager $customizer)
    {
        // Site Watermark
        $customizer->add_setting(SettingsSite::WATERMARK);
        $customLogoArgs = get_theme_support(WPOptions::SITE_LOGO);
        $controlOptions = [
            CustomizerControlArgs::SECTION => CustomizerSection::IDENTITY,
            CustomizerControlArgs::LABEL => __('Watermark'),
            CustomizerControlArgs::PRIORITY => 9,
            'height' => $customLogoArgs[0]['height'],
            'width' => $customLogoArgs[0]['width'],
            'flex_height' => $customLogoArgs[0]['flex-height'],
            'flex_width' => $customLogoArgs[0]['flex-width'],
            'button_labels' => [
                'select' => __('Select Image'),
                'change' => __('Change Image'),
                'remove' => __('Remove'),
                'default' => __('Default'),
                'placeholder' => __('No image selected'),
                'frame_title' => __('Select Image'),
                'frame_button' => __('Choose image'),
            ]
        ];
        $customizer->add_control(new WP_Customize_Cropped_Image_Control($customizer, SettingsSite::WATERMARK, $controlOptions));
        // Email
        $settingOptions = [
            CustomizerSettingArgs::TYPE => CustomizerSettingType::OPTION,
            CustomizerSettingArgs::SANITITZE_CALLBACK => CustomizerSanitize::EMAIL,
        ];
        $customizer->add_setting(SettingsSite::EMAIL, $settingOptions);
        $controlOptions = [
            CustomizerControlArgs::SECTION => CustomizerSection::IDENTITY,
            CustomizerControlArgs::TYPE => CustomizerInputTypes::EMAIL,
            CustomizerControlArgs::LABEL => __('Email Address'),
        ];
        $customizer->add_control(SettingsSite::EMAIL, $controlOptions);
        // Phone
        $settingOptions = [
            CustomizerSettingArgs::TYPE => CustomizerSettingType::OPTION,
            CustomizerSettingArgs::SANITITZE_CALLBACK => CustomizerSanitize::TEXT,
        ];
        $customizer->add_setting(SettingsSite::PHONES, $settingOptions);
        $controlOptions = [
            CustomizerControlArgs::SECTION => CustomizerSection::IDENTITY,
            CustomizerControlArgs::TYPE => CustomizerInputTypes::TEL,
            CustomizerControlArgs::LABEL => __('Phone Number', WpApp::TEXT_DOMAIN),
        ];
        $customizer->add_control(SettingsSite::PHONES, $controlOptions);
        // Address
        $settingOptions = [
            CustomizerControlArgs::TYPE => CustomizerSettingType::OPTION
        ];
        $customizer->add_setting(SettingsSite::ADDRESS, $settingOptions);
        $controlOptions = [
            CustomizerControlArgs::SECTION => CustomizerSection::IDENTITY,
            CustomizerControlArgs::TYPE => CustomizerInputTypes::TEXT,
            CustomizerControlArgs::LABEL => __('Address'),
        ];
        $customizer->add_control(SettingsSite::ADDRESS, $controlOptions);
        // Members Panel
        $customizer->add_setting('headerForCategoryMembers');
        $controlOptions = [
            CustomizerControlArgs::SECTION => CustomizerSection::IDENTITY,
            CustomizerControlArgs::LABEL => __('Membership'),
        ];
        $customizer->add_control(new CustomizerControlHeading($customizer, 'headerForCategoryMembers', $controlOptions));
        // Members Restrict Access
        $settingOptions = [
            CustomizerSettingArgs::TYPE => CustomizerSettingType::OPTION,
            CustomizerSettingArgs::TRANSPORT => CustomizerTransport::POST_MESSAGE,
            CustomizerSettingArgs::STD => 0
        ];
        $customizer->add_setting(SettingsSite::REGISTRATION_ENABLED, $settingOptions);
        $controlOptions = [
            CustomizerControlArgs::SECTION => CustomizerSection::IDENTITY,
            CustomizerControlArgs::TYPE => CustomizerInputTypes::RADIO,
            CustomizerControlArgs::LABEL => __('Users can register', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::CHOICES => [
                true => __('Yes'),
                false => __('No'),
            ]
        ];
        $customizer->add_control(SettingsSite::REGISTRATION_ENABLED, $controlOptions);
        // Members Restrict Access Level
        $customizer->add_setting(SettingsSite::BACKEND_ACCESS_LEVEL, [
            CustomizerSettingArgs::TYPE => CustomizerSettingType::OPTION,
            CustomizerSettingArgs::TRANSPORT => CustomizerTransport::POST_MESSAGE,
            CustomizerSettingArgs::STD => '0',
        ]);
        $customizer->add_control(SettingsSite::BACKEND_ACCESS_LEVEL, [
            CustomizerControlArgs::SECTION => CustomizerSection::IDENTITY,
            CustomizerControlArgs::TYPE => CustomizerInputTypes::SELECT,
            CustomizerControlArgs::LABEL => __('Restrict Admin Side Access', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::DESCRIPTION => __('To any user level equal or below the selected', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::CHOICES => [
                '0' => __('Subscriber'),
                '1' => __('Contributor'),
                '2' => __('Author'),
                '7' => __('Editor'),
            ],
        ]);
        // Enable/Disable Message Copy
        $customizer->add_setting(SettingsSite::EMAIL_CC_ENABLE, [
            CustomizerSettingArgs::TYPE => CustomizerSettingType::OPTION,
            CustomizerSettingArgs::TRANSPORT => CustomizerTransport::POST_MESSAGE,
            CustomizerSettingArgs::STD => 0,
        ]);
        $customizer->add_control(SettingsSite::EMAIL_CC_ENABLE, [
            CustomizerControlArgs::SECTION => CustomizerSection::IDENTITY,
            CustomizerControlArgs::TYPE => CustomizerInputTypes::RADIO,
            CustomizerControlArgs::LABEL => __('Get Copy of Message Sent to User', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::CHOICES => [
                'true' => __('Yes'),
                'false' => __('No'),
            ],
        ]);
        // Email Address to Get a Copy of Agent Message
        $customizer->add_setting(SettingsSite::EMAIL_CC, [
            CustomizerSettingArgs::TYPE => CustomizerSettingType::OPTION,
            CustomizerSettingArgs::SANITITZE_CALLBACK => CustomizerSanitize::EMAIL,
            CustomizerSettingArgs::TRANSPORT => CustomizerTransport::POST_MESSAGE,
        ]);
        $customizer->add_control(SettingsSite::EMAIL_CC, [
            CustomizerControlArgs::SECTION => CustomizerSection::IDENTITY,
            CustomizerControlArgs::TYPE => CustomizerInputTypes::EMAIL,
            CustomizerControlArgs::LABEL => __('Email Address'),
            CustomizerControlArgs::DESCRIPTION => __("The given email address will get copy of the message", WpApp::TEXT_DOMAIN),
        ]);
    }

    /**
     * Register Section: Google Services
     * @param WP_Customize_Manager $customizer
     */
    function registerSectionGoogle(WP_Customize_Manager $customizer)
    {
        // Google Services
        $customizer->add_section(CustomizerSection::GOOGLE_SERVICES, [
            CustomizerSectionArgs::TITLE => __('Google Services', WpApp::TEXT_DOMAIN),
            CustomizerSectionArgs::PRIORITY => 125,
        ]);
        // ---------------------------------- [ReCaptcha]
        $setting = $customizer->add_setting('headerForCategoryGoogleRecaptcha');
        $customizer->add_control(new CustomizerControlHeading($customizer, $setting->id, [
            CustomizerControlArgs::SECTION => CustomizerSection::GOOGLE_SERVICES,
            CustomizerControlArgs::LABEL => __('ReCaptcha'),
        ]));
        // Show
        $customizer->add_setting(SettingsSite::RECAPTCHA_ENABLED, [
            CustomizerSettingArgs::TYPE => CustomizerSettingType::OPTION,
            CustomizerSettingArgs::STD => 'false'
        ]);
        $textSignUp = __('Here');
        $textSignUp = "<a href='https://www.google.com/recaptcha/admin#whyrecaptcha' target='_blank'>{$textSignUp}</a>";
        $textGetReCaptchaKeys = sprintf(__('Get reCAPTCHA public and private keys %s', WpApp::TEXT_DOMAIN), $textSignUp);
        $customizer->add_control(SettingsSite::RECAPTCHA_ENABLED, [
            CustomizerControlArgs::SECTION => CustomizerSection::GOOGLE_SERVICES,
            CustomizerControlArgs::TYPE => CustomizerInputTypes::RADIO,
            CustomizerControlArgs::LABEL => __('Enable ReCaptcha?', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::DESCRIPTION => $textGetReCaptchaKeys,
            CustomizerControlArgs::CHOICES => [ 'true' => __('Yes'), 'false' => __('No')],
        ]);
        // Public Key
        $customizer->add_setting(SettingsSite::RECAPTCHA_KEY_PUBLIC, [
            CustomizerSettingArgs::TYPE => CustomizerSettingType::OPTION,
            CustomizerSettingArgs::SANITITZE_CALLBACK => CustomizerSanitize::TEXT,
            CustomizerSettingArgs::STD => '',
        ]);
        $customizer->add_control(SettingsSite::RECAPTCHA_KEY_PUBLIC, [
            CustomizerControlArgs::SECTION => CustomizerSection::GOOGLE_SERVICES,
            CustomizerControlArgs::TYPE => CustomizerInputTypes::TEXT,
            CustomizerControlArgs::LABEL => __('Public Key', WpApp::TEXT_DOMAIN),
        ]);
        // Private Key
        $customizer->add_setting(SettingsSite::RECAPTCHA_KEY_PRIVATE, [
            CustomizerSettingArgs::TYPE => CustomizerSettingType::OPTION,
            CustomizerSettingArgs::SANITITZE_CALLBACK => CustomizerSanitize::TEXT,
            CustomizerSettingArgs::STD => '',
        ]);
        $customizer->add_control(SettingsSite::RECAPTCHA_KEY_PRIVATE, [
            CustomizerControlArgs::SECTION => CustomizerSection::GOOGLE_SERVICES,
            CustomizerControlArgs::TYPE => CustomizerInputTypes::TEXT,
            CustomizerControlArgs::LABEL => __('Private Key', WpApp::TEXT_DOMAIN),
        ]);
        // ----------------------------------- Google Maps
        $setting = $customizer->add_setting('headerForCategoryGoogleMaps');
        $customizer->add_control(new CustomizerControlHeading($customizer, $setting->id, [
            CustomizerControlArgs::SECTION => CustomizerSection::GOOGLE_SERVICES,
            CustomizerControlArgs::LABEL => __('Maps'),
        ]));
        // API Key
        $customizer->add_setting(SettingsSite::GOOGLE_MAP_API, [
            CustomizerSettingArgs::TYPE => CustomizerSettingType::OPTION,
            CustomizerSettingArgs::SANITITZE_CALLBACK => CustomizerSanitize::TEXT,
            CustomizerSettingArgs::STD => '',
        ]);
        $customizer->add_control(SettingsSite::GOOGLE_MAP_API, [
            CustomizerControlArgs::SECTION => CustomizerSection::GOOGLE_SERVICES,
            CustomizerControlArgs::TYPE => CustomizerInputTypes::TEXT,
            CustomizerControlArgs::LABEL => __('API Key', WpApp::TEXT_DOMAIN),
        ]);
        // ----------------------------------- Google Analytics
        $setting = $customizer->add_setting('headerForCategoryGoogleAnalytics');
        $customizer->add_control(new CustomizerControlHeading($customizer, $setting->id, [
            CustomizerControlArgs::SECTION => CustomizerSection::GOOGLE_SERVICES,
            CustomizerControlArgs::LABEL => __('Analytics'),
        ]));
        // Tracking Code
        $customizer->add_setting(SettingsSite::GOOGLE_ANALYTICS,
            [CustomizerControlArgs::TYPE => CustomizerSettingType::OPTION]);
        $customizer->add_control(SettingsSite::GOOGLE_ANALYTICS, [
            CustomizerControlArgs::SECTION => CustomizerSection::GOOGLE_SERVICES,
            CustomizerControlArgs::TYPE => CustomizerInputTypes::TEXTAREA,
            CustomizerControlArgs::LABEL => __('Code'),
        ]);

    }

    /**
     * Register Section: Payments
     * @param WP_Customize_Manager $customizer
     */
    function registerSectionPayments(WP_Customize_Manager $customizer)
    {
        $customizer->add_section(CustomizerSection::PAYMENTS, [
            CustomizerSectionArgs::TITLE => __('Payments', WpApp::TEXT_DOMAIN),
            CustomizerSectionArgs::PRIORITY => 136,
        ]);
        // Currency Sign
        $customizer->add_setting(SettingsSite::CURRENCY_SIGN, [
            CustomizerSettingArgs::TYPE => CustomizerSettingType::OPTION,
            CustomizerSettingArgs::SANITITZE_CALLBACK => CustomizerSanitize::TEXT,
            CustomizerSettingArgs::STD => '$',
        ]);
        $customizer->add_control(SettingsSite::CURRENCY_SIGN, [
            CustomizerControlArgs::SECTION => CustomizerSection::PAYMENTS,
            CustomizerControlArgs::TYPE => CustomizerInputTypes::TEXT,
            CustomizerControlArgs::LABEL => __('Currency Sign', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::DESCRIPTION => __('Provide currency sign. For Example: $', WpApp::TEXT_DOMAIN),
        ]);
        // Position
        $customizer->add_setting(SettingsSite::CURRENCY_POSITION, [
            CustomizerSettingArgs::TYPE => CustomizerSettingType::OPTION,
            CustomizerSettingArgs::STD => 'before',
        ]);
        $customizer->add_control(SettingsSite::CURRENCY_POSITION, [
            CustomizerControlArgs::SECTION => CustomizerSection::PAYMENTS,
            CustomizerControlArgs::TYPE => CustomizerInputTypes::RADIO,
            CustomizerControlArgs::LABEL => __('Position of Currency Sign', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::CHOICES => [
                'before' => __('Before the numbers', WpApp::TEXT_DOMAIN),
                'after' => __('After the numbers', WpApp::TEXT_DOMAIN),
            ],
        ]);
        // Number of Decimals
        $customizer->add_setting(SettingsSite::CURRENCY_DECIMALS, [
            CustomizerSettingArgs::TYPE => CustomizerSettingType::OPTION,
            CustomizerSettingArgs::STD => '0',
        ]);
        $customizer->add_control(SettingsSite::CURRENCY_DECIMALS, [
            CustomizerControlArgs::SECTION => CustomizerSection::PAYMENTS,
            CustomizerControlArgs::TYPE => CustomizerInputTypes::SELECT,
            CustomizerControlArgs::LABEL => __('Number of Decimals Points', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::CHOICES => [
                '0' => 0,
                '1' => 1,
                '2' => 2,
                '3' => 3,
                '4' => 4,
                '5' => 5,
                '6' => 6,
                '7' => 7,
                '8' => 8,
                '9' => 9,
                '10' => 10,
            ],
        ]);
        // Decimal Point Separator
        $customizer->add_setting(SettingsSite::CURRENCY_DECIMAL_POINT, [
            CustomizerSettingArgs::TYPE => CustomizerSettingType::OPTION,
            CustomizerSettingArgs::SANITITZE_CALLBACK => CustomizerSanitize::TEXT,
            CustomizerSettingArgs::STD => '.',
        ]);
        $customizer->add_control(SettingsSite::CURRENCY_DECIMAL_POINT, [
            CustomizerControlArgs::SECTION => CustomizerSection::PAYMENTS,
            CustomizerControlArgs::TYPE => CustomizerInputTypes::TEXT,
            CustomizerControlArgs::LABEL => __('Decimal Point Separator', WpApp::TEXT_DOMAIN),
        ]);
        // Thousand Separator
        $customizer->add_setting(SettingsSite::CURRENCY_THOUSANDS_SEPARATOR, [
            CustomizerSettingArgs::TYPE => CustomizerSettingType::OPTION,
            CustomizerSettingArgs::SANITITZE_CALLBACK => CustomizerSanitize::TEXT,
            CustomizerSettingArgs::STD => ',',
        ]);
        $customizer->add_control(SettingsSite::CURRENCY_THOUSANDS_SEPARATOR, [
            CustomizerControlArgs::SECTION => CustomizerSection::PAYMENTS,
            CustomizerControlArgs::TYPE => CustomizerInputTypes::TEXT,
            CustomizerControlArgs::LABEL => __('Thousands Separator', WpApp::TEXT_DOMAIN),
        ]);
    }
}