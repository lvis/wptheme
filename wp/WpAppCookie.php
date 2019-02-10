<?php namespace wp;

use WP_Customize_Manager;
use WP_Customize_Control;

final class WpAppCookie extends WpApp
{
    protected function __construct()
    {
        parent::__construct();
        add_action(WPActions::ENQUEUE_SCRIPTS_THEME, [$this, 'enqueueScriptsTheme']);
        add_action(WPActions::CUSTOMIZER_INIT, [$this, 'handleCustomizerInit']);
        add_action(WPActions::CUSTOMIZER_REGISTER, [$this, 'handleCustomizerRegister']);
        add_action(WPActions::WP_HEAD, [$this, 'handleHeadScripts']);
        add_action(WPActions::WP_HEAD, [$this, 'handleHeadCss'], 9999);
        add_action('wp_footer', [$this, 'handleFooter'], 9999);
        add_action('wp_print_footer_scripts', [$this, 'handleFooterPrintScripts']);
    }
    /**
     * @inheritdoc
     */
    function enqueueScriptsTheme()
    {
        parent::enqueueScriptsTheme();
        //---------------------------------------------- [Cookie]
        wp_enqueue_style('cookie', $this->uriToLibs . 'cookie.css');
        $time = get_theme_mod('ocn_expiry', '1month');
        $times = [
            '1hour' => [__('An hour', WpApp::TEXT_DOMAIN), 3600],
            '1day' => [__('1 day', WpApp::TEXT_DOMAIN), 86400],
            '1week' => [__('1 week', WpApp::TEXT_DOMAIN), 604800],
            '1month' => [__('1 month', WpApp::TEXT_DOMAIN), 2592000],
            '3months' => [__('3 months', WpApp::TEXT_DOMAIN), 7862400],
            '6months' => [__('6 months', WpApp::TEXT_DOMAIN), 15811200],
            '1year' => [__('1 year', WpApp::TEXT_DOMAIN), 31536000],
            'infinity' => [__('infinity', WpApp::TEXT_DOMAIN), 2147483647]
        ];
        $get_time = sanitize_text_field(isset($times[$time]) ? $time : '1month');
        $localizeData = [
            'cookieName'=> 'ocn_accepted',
            'cookieTime'=> $times[$get_time][1],
            'cookiePath'=> (defined('COOKIEPATH') ? COOKIEPATH : ''),
            'cookieDomain'=> (defined('COOKIE_DOMAIN') ? COOKIE_DOMAIN : ''),
            'cache'=> (defined('WP_CACHE') && WP_CACHE),
            'secure'=> (int)is_ssl(),
            'reload'=> get_theme_mod('ocn_reload', 'no'),
            'overlay'=> get_theme_mod('ocn_overlay', 'no')
        ];
        wp_enqueue_script('cookie-main', $this->uriToLibs . 'cookie-main.js', ['jquery'], false, true);
        wp_localize_script('cookie-main', 'oceanwpLocalize', $localizeData);
        self::enqueueGoogleFonts([get_theme_mod(self::OPTION_COOKIE_CONTENT_FONT, '')]);
    }
    //-----------------------------------------[Cookie Functions]
    private static $googleFonts = [];
    static function getGoogleFonts()
    {
        if (empty(self::$googleFonts)) {
            self::$googleFonts = include 'Fonts.php';
        }
        return self::$googleFonts;
    }
    const OPTION_COOKIE_CONTENT_FONT = 'ocn_content_typo_font_family';

    function hasTargetClose()
    {
        return (get_theme_mod('ocn_target', 'button') == 'close');
    }
    function hasTargetButton()
    {
        return (get_theme_mod('ocn_target', 'button') == 'button');
    }
    /**
     * Enqueues a Google Font
     * @param array $fonts
     */
    static function enqueueGoogleFonts(array $fonts)
    {
        if (empty($fonts) == false && is_array($fonts)) {
            foreach ($fonts as $font) {
                // Make sure font is in our list of fonts
                if (isset(self::getGoogleFonts()[$font])) {
                    // Sanitize handle
                    $handle = trim($font);
                    $handle = strtolower($handle);
                    $handle = str_replace(' ', '-', $handle);
                    // Sanitize font name
                    $font = trim($font);
                    $font = str_replace(' ', '+', $font);
                    // Create Url
                    $url = '//fonts.googleapis.com/css?family=' . str_replace(' ', '%20', $font) . ':';
                    $weights = ['100', '200', '300', '400', '500', '600', '700', '800', '900'];
                    $url .= implode(',', $weights) . ',';
                    $italic_weights[] = ['100i', '200i', '300i', '400i', '500i', '600i', '700i', '800i', '900i'];
                    $url .= implode(',', $italic_weights);
                    $subsets = ['latin'];
                    $url .= '&amp;subset=' . implode(',', $subsets);
                    // Enqueue style
                    wp_enqueue_style('oceanwp-google-font-' . $handle, $url, false, false, 'all');
                }
            }
        }
    }
    public function handleCustomizerInit()
    {
        wp_enqueue_script('cookie-customizer', $this->uriToLibs . 'cookie-customizer.js',
            ['customize-preview'], false, true);
        wp_localize_script('cookie-customizer', 'ocn_cookie', [
            'googleFontsUrl' => '//fonts.googleapis.com',
            'googleFontsWeight' => '100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i',
        ]);
    }
    function handleCustomizerRegister(WP_Customize_Manager $customizer)
    {
        //Cookie: Section
        $customizer->add_section('ocn_section', [
            CustomizerSectionArgs::TITLE => __('Cookie Notice', WpApp::TEXT_DOMAIN),
            CustomizerSectionArgs::PRIORITY => 210,
        ]);
        //Cookie: Expiry
        $customizer->add_setting('ocn_expiry', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::STD => '1month',
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeSelect'],
        ]);
        $customizer->add_control(new WP_Customize_Control($customizer, 'ocn_expiry', [
            CustomizerControlArgs::LABEL => __('Cookie Expiry', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::TYPE => CustomizerInputTypes::SELECT,
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_expiry',
            CustomizerControlArgs::PRIORITY => 10,
            CustomizerControlArgs::CHOICES => [
                '1hour' => __('An Hour', WpApp::TEXT_DOMAIN),
                '1day' => __('1 day', WpApp::TEXT_DOMAIN),
                '1week' => __('1 Week', WpApp::TEXT_DOMAIN),
                '1month' => __('1 Month', WpApp::TEXT_DOMAIN),
                '3months' => __('3 Months', WpApp::TEXT_DOMAIN),
                '6months' => __('6 Months', WpApp::TEXT_DOMAIN),
                '1year' => __('1 Year', WpApp::TEXT_DOMAIN),
                'infinity' => __('Infinity', WpApp::TEXT_DOMAIN),
            ],
        ]));
        //Cookie: Section Settings
        $customizer->add_setting('ocn_content', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::STD => __('By continuing to use this website, you consent to the use of cookies in accordance with our Cookie Policy.', WpApp::TEXT_DOMAIN),
            CustomizerSettingArgs::SANITITZE_CALLBACK => 'wp_kses_post',
        ]);
        //Cookie: Text Content
        $customizer->add_control(new CustomizerControlTextarea($customizer, 'ocn_content', [
            CustomizerControlArgs::LABEL => __('Content'),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_content',
            CustomizerControlArgs::PRIORITY => 10,
        ]));
    // Content Text: Style Heading
        $customizer->add_setting('ocn_styling_content_heading', [
            CustomizerSettingArgs::SANITITZE_CALLBACK => 'wp_kses',
        ]);
        $customizer->add_control(new CustomizerControlHeading($customizer, 'ocn_styling_content_heading', [
            CustomizerControlArgs::LABEL => __('Styling').':'.__('Content'),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::PRIORITY => 10,
        ]));
        // Content Text: Color
        $customizer->add_setting('ocn_text_color', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::STD => '#777777',
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeColor'],
        ]);
        $customizer->add_control(new CustomizerControlColorPicker($customizer, 'ocn_text_color', [
            CustomizerControlArgs::LABEL => __('Text Color', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_text_color',
            CustomizerControlArgs::PRIORITY => 10,
        ]));
        // Content Text: Typography
        $customizer->add_setting(self::OPTION_COOKIE_CONTENT_FONT, [
            CustomizerSettingArgs::TRANSPORT => CustomizerTransport::POST_MESSAGE,
            CustomizerSettingArgs::SANITITZE_CALLBACK => CustomizerSanitize::TEXT
        ]);
        $customizer->add_setting('ocn_content_typo_font_size', [
            CustomizerSettingArgs::TRANSPORT => CustomizerTransport::POST_MESSAGE,
            CustomizerSettingArgs::SANITITZE_CALLBACK => CustomizerSanitize::TEXT
        ]);

        $customizer->add_setting('ocn_content_typo_font_weight', [
            CustomizerSettingArgs::TRANSPORT => CustomizerTransport::POST_MESSAGE,
            CustomizerSettingArgs::SANITITZE_CALLBACK => CustomizerSanitize::KEY
        ]);
        $customizer->add_setting('ocn_content_typo_font_style', [
            CustomizerSettingArgs::TRANSPORT => CustomizerTransport::POST_MESSAGE,
            CustomizerSettingArgs::SANITITZE_CALLBACK => CustomizerSanitize::KEY
        ]);
        $customizer->add_setting('ocn_content_typo_transform', [
            CustomizerSettingArgs::TRANSPORT => CustomizerTransport::POST_MESSAGE,
            CustomizerSettingArgs::SANITITZE_CALLBACK => CustomizerSanitize::KEY
        ]);
        $customizer->add_setting('ocn_content_typo_line_height', [
            CustomizerSettingArgs::TRANSPORT => CustomizerTransport::POST_MESSAGE,
            CustomizerSettingArgs::SANITITZE_CALLBACK => CustomizerSanitize::TEXT
        ]);
        $customizer->add_setting('ocn_content_typo_spacing', [
            CustomizerSettingArgs::TRANSPORT => CustomizerTransport::POST_MESSAGE,
            CustomizerSettingArgs::SANITITZE_CALLBACK => CustomizerSanitize::TEXT
        ]);
        $customizer->add_control(new CustomizerControlTypography($customizer, 'ocn_content_typo', [
            CustomizerControlArgs::LABEL => __('Typography', 'ocean-portfolio'),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => [
                'family' => self::OPTION_COOKIE_CONTENT_FONT,
                'size' => 'ocn_content_typo_font_size',
                'weight' => 'ocn_content_typo_font_weight',
                'style' => 'ocn_content_typo_font_style',
                'transform' => 'ocn_content_typo_transform',
                'line_height' => 'ocn_content_typo_line_height',
                'spacing' => 'ocn_content_typo_spacing'
            ],
            CustomizerControlArgs::PRIORITY => 10,
            'l10n' => [],
        ]));
        //Cookie: Close target
        $customizer->add_setting('ocn_target', [
            CustomizerSettingArgs::STD => CustomizerInputTypes::BUTTON,
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeSelect'],
        ]);
        //Cookie: Text Button
        $customizer->add_setting('ocn_button_text', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::STD => __('Accept', WpApp::TEXT_DOMAIN),
            CustomizerSettingArgs::SANITITZE_CALLBACK => 'wp_kses_post',
        ]);
        $customizer->add_control(new WP_Customize_Control($customizer, 'ocn_button_text', [
            CustomizerControlArgs::LABEL => __('Button Text', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::TYPE => CustomizerInputTypes::TEXT,
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_button_text',
            CustomizerControlArgs::PRIORITY => 10,
            'active_callback' => [$this, 'hasTargetButton'],
        ]));
        $customizer->add_control(new WP_Customize_Control($customizer, 'ocn_target', [
            CustomizerControlArgs::LABEL => __('Close Target', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::TYPE => CustomizerInputTypes::SELECT,
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_target',
            CustomizerControlArgs::PRIORITY => 10,
            CustomizerControlArgs::CHOICES => [
                'button' => __('Button'),
                'close' => __('Close Icon', WpApp::TEXT_DOMAIN),
            ],
        ]));
        // Button: Heading
        $customizer->add_setting('ocn_styling_button_heading', [
            CustomizerSettingArgs::SANITITZE_CALLBACK => 'wp_kses',
        ]);
        $customizer->add_control(new CustomizerControlHeading($customizer, 'ocn_styling_button_heading', [
            CustomizerControlArgs::LABEL => __('Styling').':'.__('Button'),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::PRIORITY => 10,
            'active_callback' => [$this, 'hasTargetButton'],
        ]));
        // Button: Background Color
        $customizer->add_setting('ocn_btn_background', [
            CustomizerSettingArgs::TRANSPORT => CustomizerTransport::POST_MESSAGE,
            CustomizerSettingArgs::STD => '#13aff0',
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeColor'],
        ]);
        $customizer->add_control(new CustomizerControlColorPicker($customizer, 'ocn_btn_background', [
            CustomizerControlArgs::LABEL => __('Background Color', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_btn_background',
            CustomizerControlArgs::PRIORITY => 10,
            'active_callback' => [$this, 'hasTargetButton'],
        ]));
        // Button: Color
        $customizer->add_setting('ocn_btn_color', [
            CustomizerSettingArgs::TRANSPORT => CustomizerTransport::POST_MESSAGE,
            CustomizerSettingArgs::STD => '#ffffff',
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeColor'],
        ]);
        $customizer->add_control(new CustomizerControlColorPicker($customizer, 'ocn_btn_color', [
            CustomizerControlArgs::LABEL => __('Color', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_btn_color',
            CustomizerControlArgs::PRIORITY => 10,
            'active_callback' => [$this, 'hasTargetButton'],
        ]));
        // Button: Hover Background
        $customizer->add_setting('ocn_btn_hover_background', [
            CustomizerSettingArgs::TRANSPORT => CustomizerTransport::POST_MESSAGE,
            CustomizerSettingArgs::STD => '#0b7cac',
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeColor'],
        ]);
        $customizer->add_control(new CustomizerControlColorPicker($customizer, 'ocn_btn_hover_background', [
            CustomizerControlArgs::LABEL => __('Hover: Background Color', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_btn_hover_background',
            CustomizerControlArgs::PRIORITY => 10,
            'active_callback' => [$this, 'hasTargetButton'],
        ]));
        // Button: Hover color
        $customizer->add_setting('ocn_btn_hover_color', [
            CustomizerSettingArgs::TRANSPORT => CustomizerTransport::POST_MESSAGE,
            CustomizerSettingArgs::STD => '#ffffff',
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeColor'],
        ]);
        $customizer->add_control(new CustomizerControlColorPicker($customizer, 'ocn_btn_hover_color', [
            CustomizerControlArgs::LABEL => __('Hover: Color', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_btn_hover_color',
            CustomizerControlArgs::PRIORITY => 10,
            'active_callback' => [$this, 'hasTargetButton'],
        ]));
        // Close Icon: Heading
        $customizer->add_setting('ocn_styling_close_heading', [
            CustomizerSettingArgs::SANITITZE_CALLBACK => 'wp_kses',
        ]);
        $customizer->add_control(new CustomizerControlHeading($customizer, 'ocn_styling_close_heading', [
            CustomizerControlArgs::LABEL => __('Styling').':'.__('Close Icon'),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::PRIORITY => 10,
            'active_callback' => [$this, 'hasTargetClose'],
        ]));
        // Close Icon: Color
        $customizer->add_setting('ocn_close_color', [
            CustomizerSettingArgs::TRANSPORT => CustomizerTransport::POST_MESSAGE,
            CustomizerSettingArgs::STD => '#777777',
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeColor'],
        ]);
        $customizer->add_control(new CustomizerControlColorPicker($customizer, 'ocn_close_color', [
            CustomizerControlArgs::LABEL => __('Color'),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_close_color',
            CustomizerControlArgs::PRIORITY => 10,
            'active_callback' => [$this, 'hasTargetClose'],
        ]));
        // Close Icon: Hover color
        $customizer->add_setting('ocn_close_hover_color', [
            CustomizerSettingArgs::TRANSPORT => CustomizerTransport::POST_MESSAGE,
            CustomizerSettingArgs::STD => '#333333',
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeColor'],
        ]);
        $customizer->add_control(new CustomizerControlColorPicker($customizer, 'ocn_close_hover_color', [
            CustomizerControlArgs::LABEL => __('Hover: Color', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_close_hover_color',
            CustomizerControlArgs::PRIORITY => 10,
            'active_callback' => [$this, 'hasTargetClose'],
        ]));
        //Cookie: Popup Style
        $customizer->add_setting('ocn_style', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::STD => 'flyin',
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeSelect'],
        ]);
        $customizer->add_control(new WP_Customize_Control($customizer, 'ocn_style', [
            CustomizerControlArgs::LABEL => __('Container Type', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::TYPE => CustomizerInputTypes::SELECT,
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_style',
            CustomizerControlArgs::PRIORITY => 10,
            CustomizerControlArgs::CHOICES => [
                'flyin' => __('Fly-Ins', WpApp::TEXT_DOMAIN),
                'floating' => __('Floating Bar', WpApp::TEXT_DOMAIN),
            ],
        ]));
        //Popup: Style Heading
        $customizer->add_setting('ocn_styling_general_heading', [
            CustomizerSettingArgs::SANITITZE_CALLBACK => 'wp_kses',
        ]);
        $customizer->add_control(new CustomizerControlHeading($customizer, 'ocn_styling_general_heading', [
            CustomizerControlArgs::LABEL => __('Styling').':'.__('Container'),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::PRIORITY => 10,
        ]));
        //Popup: Max Width
        $customizer->add_setting('ocn_width', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeNumberWithBlankValue'],
        ]);
        $customizer->add_control(new WP_Customize_Control($customizer, 'ocn_width', [
            CustomizerControlArgs::LABEL => __('Max Width (px)', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::TYPE => CustomizerInputTypes::NUMBER,
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_width',
            CustomizerControlArgs::PRIORITY => 10,
            'input_attrs' => [
                'min' => 100,
                'step' => 1,
            ],
        ]));
        //Popup: Padding
        $customizer->add_setting('ocn_top_padding', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeNumber'],
        ]);
        $customizer->add_setting('ocn_right_padding', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeNumber'],
        ]);
        $customizer->add_setting('ocn_bottom_padding', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeNumber'],
        ]);
        $customizer->add_setting('ocn_left_padding', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeNumber'],
        ]);
        $customizer->add_setting('ocn_tablet_top_padding', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeNumberWithBlankValue'],
        ]);
        $customizer->add_setting('ocn_tablet_right_padding', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeNumberWithBlankValue'],
        ]);
        $customizer->add_setting('ocn_tablet_bottom_padding', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeNumberWithBlankValue'],
        ]);
        $customizer->add_setting('ocn_tablet_left_padding', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeNumberWithBlankValue'],
        ]);
        $customizer->add_setting('ocn_mobile_top_padding', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeNumberWithBlankValue'],
        ]);
        $customizer->add_setting('ocn_mobile_right_padding', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeNumberWithBlankValue'],
        ]);
        $customizer->add_setting('ocn_mobile_bottom_padding', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeNumberWithBlankValue'],
        ]);
        $customizer->add_setting('ocn_mobile_left_padding', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeNumberWithBlankValue'],
        ]);
        $customizer->add_control(new CustomizerControlDimensions($customizer, 'ocn_padding_dimensions', [
            CustomizerControlArgs::LABEL => __('Padding (px)', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => [
                'desktop_top' => 'ocn_top_padding',
                'desktop_right' => 'ocn_right_padding',
                'desktop_bottom' => 'ocn_bottom_padding',
                'desktop_left' => 'ocn_left_padding',
                'tablet_top' => 'ocn_tablet_top_padding',
                'tablet_right' => 'ocn_tablet_right_padding',
                'tablet_bottom' => 'ocn_tablet_bottom_padding',
                'tablet_left' => 'ocn_tablet_left_padding',
                'mobile_top' => 'ocn_mobile_top_padding',
                'mobile_right' => 'ocn_mobile_right_padding',
                'mobile_bottom' => 'ocn_mobile_bottom_padding',
                'mobile_left' => 'ocn_mobile_left_padding',
            ],
            CustomizerControlArgs::PRIORITY => 10,
            'input_attrs' => [
                'min' => 0,
                'max' => 300,
                'step' => 1,
            ],
        ]));
        //Popup: Background Color
        $customizer->add_setting('ocn_background', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::STD => '#ffffff',
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeColor'],
        ]);
        $customizer->add_control(new CustomizerControlColorPicker($customizer, 'ocn_background', [
            CustomizerControlArgs::LABEL => __('Background Color', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_background',
            CustomizerControlArgs::PRIORITY => 10,
        ]));
        //Popup: Border Width
        $customizer->add_setting('ocn_border_width', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeNumberWithBlankValue'],
        ]);
        $customizer->add_control(new WP_Customize_Control($customizer, 'ocn_border_width', [
            CustomizerControlArgs::LABEL => __('Border Width (px)', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::TYPE => CustomizerInputTypes::NUMBER,
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_border_width',
            CustomizerControlArgs::PRIORITY => 10,
            'input_attrs' => [
                'min' => 100,
                'step' => 1,
            ],
        ]));
        //Popup: Border Style
        $customizer->add_setting('ocn_border_style', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::STD => 'none',
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeSelect'],
        ]);
        $customizer->add_control(new WP_Customize_Control($customizer, 'ocn_border_style', [
            CustomizerControlArgs::LABEL => __('Border Style', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::TYPE => CustomizerInputTypes::SELECT,
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_border_style',
            CustomizerControlArgs::PRIORITY => 10,
            CustomizerControlArgs::CHOICES => [
                'none' => __('None', WpApp::TEXT_DOMAIN),
                'solid' => __('Solid', WpApp::TEXT_DOMAIN),
                'double' => __('Double', WpApp::TEXT_DOMAIN),
                'dotted' => __('Dotted', WpApp::TEXT_DOMAIN),
                'dashed' => __('Dashed', WpApp::TEXT_DOMAIN),
                'groove' => __('Groove', WpApp::TEXT_DOMAIN),
            ],
        ]));
        //Popup: Border Color
        $customizer->add_setting('ocn_border_color', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeColor'],
        ]);
        $customizer->add_control(new CustomizerControlColorPicker($customizer, 'ocn_border_color', [
            CustomizerControlArgs::LABEL => __('Border Color', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_border_color',
            CustomizerControlArgs::PRIORITY => 10,
        ]));

        //Cookie: Scripts Heading
        $customizer->add_setting('cookie-scripts-heading', [
            CustomizerSettingArgs::SANITITZE_CALLBACK => 'wp_kses',
        ]);
        $customizer->add_control(new CustomizerControlHeading($customizer, 'cookie-scripts-heading', [
            CustomizerControlArgs::LABEL => __('Scripts', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::PRIORITY => 10,
        ]));
        //Cookie: Scripts Content Head
        $customizer->add_setting('ocn_head_scripts', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::SANITITZE_CALLBACK => false,
        ]);
        $customizer->add_control(new CustomizerControlTextarea($customizer, 'ocn_head_scripts', [
            CustomizerControlArgs::LABEL => __('Head (before the closing head tag)', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::DESCRIPTION => __('Add cookies JavaScript code here, they will be used after users consent.', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_head_scripts',
            CustomizerControlArgs::PRIORITY => 10,
        ]));
        //Cookie: Scripts Content Body
        $customizer->add_setting('ocn_body_scripts', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::SANITITZE_CALLBACK => false,
        ]);
        $customizer->add_control(new CustomizerControlTextarea($customizer, 'ocn_body_scripts', [
            CustomizerControlArgs::LABEL => __('Body (before the closing body tag)', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::DESCRIPTION => __('Add cookies JavaScript code here, they will be used after users consent.', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_body_scripts',
            CustomizerControlArgs::PRIORITY => 10,
        ]));
        //Cookie: Reloading
        $customizer->add_setting('ocn_reload', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::STD => 'no',
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeSelect'],
        ]);
        $customizer->add_control(new CustomizerControlButtonset($customizer, 'ocn_reload', [
            CustomizerControlArgs::LABEL => __('Reloading', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::DESCRIPTION => __('Reload the page after the user consent.', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_reload',
            CustomizerControlArgs::PRIORITY => 10,
            CustomizerControlArgs::CHOICES => [
                'yes' => __('Yes'),
                'no' => __('No'),
            ],
        ]));
        //Cookie: Overlay
        $customizer->add_setting('ocn_overlay', [
            CustomizerSettingArgs::TRANSPORT => CustomizerSettingArgs::TRANSPORT,
            CustomizerSettingArgs::STD => 'no',
            CustomizerSettingArgs::SANITITZE_CALLBACK => [WPUtils::class, 'sanitizeSelect'],
        ]);
        $customizer->add_control(new CustomizerControlButtonset($customizer, 'ocn_overlay', [
            CustomizerControlArgs::LABEL => __('Display Overlay', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::DESCRIPTION => __('Display an overlay to force the user to consent to your cookies policy.', WpApp::TEXT_DOMAIN),
            CustomizerControlArgs::SECTION => 'ocn_section',
            CustomizerControlArgs::SETTINGS => 'ocn_overlay',
            CustomizerControlArgs::PRIORITY => 10,
            CustomizerControlArgs::CHOICES => [
                'yes' => __('Yes'),
                'no' => __('No'),
            ],
        ]));
    }
    function isCookieAccepted()
    {
        return isset($_COOKIE['ocn_accepted']) && $_COOKIE['ocn_accepted'] === 'true';
    }
    function getAllowedHtml()
    {
        // Get allowed script blocking HTML.
        return array_merge(wp_kses_allowed_html('post'), [
            'script' => [
                'type' => [],
                'src' => [],
                'charset' => [],
                'async' => []
            ],
            'noscript' => [],
            'style' => [
                'types' => []
            ],
            'iframe' => [
                'src' => [],
                'height' => [],
                'width' => [],
                'frameborder' => [],
                'allowfullscreen' => []
            ]
        ]);
    }
    function getPaddingCss($top, $right, $bottom, $left)
    {
        $paddingTop = $paddingRight = $paddingBottom = $paddingLeft = '0';
        if (empty($top) == false) {
            $paddingTop = intval($top);
        }
        if (empty($right) == false) {
            $paddingRight = intval($right);
        }
        if (empty($bottom) == false) {
            $paddingBottom = intval($bottom);
        }
        if (empty($left) == false) {
            $paddingLeft = intval($left);
        }
        return "{$paddingTop}px {$paddingRight}px {$paddingBottom}px {$paddingLeft}px";
    }
    function handleHeadCss()
    {
        // Width
        $css = '';
        $width = get_theme_mod('ocn_width');
        if (!empty($width)) {
            $css .= '#ocn-cookie-wrap.flyin,#ocn-cookie-wrap.floating #ocn-cookie-inner{width:' . $width . 'px;}';
        }
        // Padding
        $top_padding = get_theme_mod('ocn_top_padding');
        $right_padding = get_theme_mod('ocn_right_padding');
        $bottom_padding = get_theme_mod('ocn_bottom_padding');
        $left_padding = get_theme_mod('ocn_left_padding');
        if (isset($top_padding) && '' != $top_padding
            || isset($right_padding) && '' != $right_padding
            || isset($bottom_padding) && '' != $bottom_padding
            || isset($left_padding) && '' != $left_padding) {
            $css .= '#ocn-cookie-wrap.flyin,#ocn-cookie-wrap.floating{padding:' . $this->getPaddingCss($top_padding, $right_padding, $bottom_padding, $left_padding) . '}';
        }
        // Tablet padding
        $tablet_top_padding = get_theme_mod('ocn_tablet_top_padding');
        $tablet_right_padding = get_theme_mod('ocn_tablet_right_padding');
        $tablet_bottom_padding = get_theme_mod('ocn_tablet_bottom_padding');
        $tablet_left_padding = get_theme_mod('ocn_tablet_left_padding');
        if (isset($tablet_top_padding) && '' != $tablet_top_padding
            || isset($tablet_right_padding) && '' != $tablet_right_padding
            || isset($tablet_bottom_padding) && '' != $tablet_bottom_padding
            || isset($tablet_left_padding) && '' != $tablet_left_padding) {
            $css .= '@media (max-width: 768px){#ocn-cookie-wrap.flyin,#ocn-cookie-wrap.floating{padding:' . $this->getPaddingCss($tablet_top_padding, $tablet_right_padding, $tablet_bottom_padding, $tablet_left_padding) . '}}';
        }
        // Mobile padding
        $mobile_top_padding = get_theme_mod('ocn_mobile_top_padding');
        $mobile_right_padding = get_theme_mod('ocn_mobile_right_padding');
        $mobile_bottom_padding = get_theme_mod('ocn_mobile_bottom_padding');
        $mobile_left_padding = get_theme_mod('ocn_mobile_left_padding');
        if (isset($mobile_top_padding) && '' != $mobile_top_padding
            || isset($mobile_right_padding) && '' != $mobile_right_padding
            || isset($mobile_bottom_padding) && '' != $mobile_bottom_padding
            || isset($mobile_left_padding) && '' != $mobile_left_padding) {
            $css .= '@media (max-width: 480px){#ocn-cookie-wrap.flyin,#ocn-cookie-wrap.floating{padding:' . $this->getPaddingCss($mobile_top_padding, $mobile_right_padding, $mobile_bottom_padding, $mobile_left_padding) . '}}';
        }
        // Add background
        $background = get_theme_mod('ocn_background', '#ffffff');
        if (!empty($background) && '#ffffff' != $background) {
            $css .= "#ocn-cookie-wrap{background-color: {$background};}";
        }
        // Add border width
        $border_width = get_theme_mod('ocn_border_width');
        if (!empty($border_width)) {
            $css .= "#ocn-cookie-wrap{border-width: {$border_width}px;}";
        }
        // Add border style
        $border_style = get_theme_mod('ocn_border_style', 'none');
        if (!empty($border_style) && 'none' != $border_style) {
            $css .= "#ocn-cookie-wrap{border-style: {$border_style};}";
        }
        // Add border color
        $border_color = get_theme_mod('ocn_border_color');
        if (!empty($border_color)) {
            $css .= "#ocn-cookie-wrap{border-color: {$border_color};}";
        }
        // Add color
        $text_color = get_theme_mod('ocn_text_color', '#777777');
        if (!empty($text_color) && '#777777' != $text_color) {
            $css .= "#ocn-cookie-wrap{color: {$text_color};}";
        }
        // Button: Background
        $btn_background = get_theme_mod('ocn_btn_background', '#13aff0');
        if (!empty($btn_background) && '#13aff0' != $btn_background) {
            $css .= "#ocn-cookie-wrap .ocn-btn{background-color: {$btn_background};}";
        }
        // Button: Color
        $btn_color = get_theme_mod('ocn_btn_color', '#ffffff');
        if (!empty($btn_color) && '#ffffff' != $btn_color) {
            $css .= "#ocn-cookie-wrap .ocn-btn{color: {$btn_color};}";
        }
        // Button: Hover background
        $btn_hover_background = get_theme_mod('ocn_btn_hover_background', '#0b7cac');
        if (!empty($btn_hover_background) && '#13aff0' != $btn_hover_background) {
            $css .= "#ocn-cookie-wrap .ocn-btn:hover{background-color: {$btn_hover_background};}";
        }
        // Button: Hover color
        $btn_hover_color = get_theme_mod('ocn_btn_hover_color', '#ffffff');
        if (!empty($btn_hover_color) && '#ffffff' != $btn_hover_color) {
            $css .= "#ocn-cookie-wrap .ocn-btn:hover{color: {$btn_hover_color};}";
        }
        // Close Icon: Color
        $close_color = get_theme_mod('ocn_close_color', '#777');
        if (!empty($close_color) && '#777777' != $close_color) {
            $css .= "#ocn-cookie-wrap .ocn-icon svg{fill: {$close_color} ;}";
        }
        // Close Icon: Hover color
        $close_hover_color = get_theme_mod('ocn_close_hover_color', '#333');
        if (!empty($close_hover_color) && '#333333' != $close_hover_color) {
            $css .= "#ocn-cookie-wrap .ocn-icon:hover svg{fill: {$close_hover_color} ;}";
        }
        // Content Text: Typography
        $contentTextTypography = '';
        // Content Text: Font family
        $text_font_family = get_theme_mod(self::OPTION_COOKIE_CONTENT_FONT);
        if (empty($text_font_family) == false) {
            $contentTextTypography .= "font-family: {$text_font_family};";
        }
        // Content Text: Font size
        $text_font_size = get_theme_mod('ocn_content_typo_font_size');
        if (empty($text_font_size) == false) {
            $contentTextTypography .= "font-size: {$text_font_size};";
        }
        // Content Text: Font weight
        $text_font_weight = get_theme_mod('ocn_content_typo_font_weight');
        if (empty($text_font_weight) == false) {
            $contentTextTypography .= "font-weight: {$text_font_weight};";
        }
        // Content Text: Font style
        $text_font_style = get_theme_mod('ocn_content_typo_font_style');
        if (empty($text_font_style) == false) {
            $contentTextTypography .= "font-style: {$text_font_style};";
        }
        // Content Text: Transform
        $text_text_transform = get_theme_mod('ocn_content_typo_transform');
        if (empty($text_text_transform) == false) {
            $contentTextTypography .= "text-transform: {$text_text_transform};";
        }
        // Content Text: Line height
        $text_line_height = get_theme_mod('ocn_content_typo_line_height');
        if (empty($text_line_height) == false) {
            $contentTextTypography .= "line-height: {$text_line_height};";
        }
        // Content Text: Letter spacing
        $text_letter_spacing = get_theme_mod('ocn_content_typo_spacing');
        if (empty($text_letter_spacing) == false) {
            $contentTextTypography .= "letter-spacing: {$text_letter_spacing};";
        }

        if (empty($contentTextTypography) == false) {
            $css .= "#ocn-cookie-wrap .ocn-cookie-content{ {$contentTextTypography} }";
        }
        // Minify and output CSS in the wp_head
        if (empty($css) == false) {
            // Normalize whitespace
            $css = preg_replace('/\s+/', ' ', $css);
            // Remove ; before }
            $css = preg_replace('/;(?=\s*})/', '', $css);
            // Remove space after , : ; { } */ >
            $css = preg_replace('/(,|:|;|\{|}|\*\/|>) /', '$1', $css);
            // Remove space before , ; { }
            $css = preg_replace('/ (,|;|\{|})/', '$1', $css);
            // Strips leading 0 on decimal values (converts 0.5px into .5px)
            $css = preg_replace('/(:| )0\.([0-9]+)(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}.${2}${3}', $css);
            // Strips units if value is 0 (converts 0px to 0)
            $css = preg_replace('/(:| )(\.?)0(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}0', $css);
            // Trim
            $css = trim($css);
            //Normalize
            $output = wp_strip_all_tags($css);
            echo "<style type='text/css' id='cookieNotice'>{$output}</style>";
        }
    }
    function handleHeadScripts()
    {
        if ($this->isCookieAccepted()) {
            $scripts = html_entity_decode(trim(wp_kses(get_theme_mod('ocn_head_scripts'), $this->getAllowedHtml())));
            if (empty($scripts) == false) {
                echo $scripts;
            }
        }
    }
    function handleFooter()
    {
        $content = get_theme_mod('ocn_content', __('By continuing to use this website, you consent to the use of cookies in accordance with our Cookie Policy.', WpApp::TEXT_DOMAIN));
        $content = do_shortcode($content);
        $style = get_theme_mod('ocn_style', 'flyin');
        if (!$style) {
            $style = 'flyin';
        }
        $contentButton = '';
        $contentButtonClose = '';
        if (get_theme_mod('ocn_target', 'button') === 'button') {
            $buttonText = get_theme_mod('ocn_button_text', __('Accept'));
            $contentButton = "<a class='button ocn-btn ocn-close' href='#'>{$buttonText}</a>";
        } else {
            $contentButtonClose = "<a class='ocn-icon ocn-close' href='#'>
        <svg version='1.1' xmlns='http://www.w3.org/2000/svg' x='0px' y='0px' viewBox='0 0 512 512' xml:space='preserve'>
            <g><g><path d='M505.943,6.058c-8.077-8.077-21.172-8.077-29.249,0L6.058,476.693c-8.077,8.077-8.077,21.172,0,29.249
                        C10.096,509.982,15.39,512,20.683,512c5.293,0,10.586-2.019,14.625-6.059L505.943,35.306
                        C514.019,27.23,514.019,14.135,505.943,6.058z'/></g></g>
            <g><g><path d='M505.942,476.694L35.306,6.059c-8.076-8.077-21.172-8.077-29.248,0c-8.077,8.076-8.077,21.171,0,29.248l470.636,470.636
                        c4.038,4.039,9.332,6.058,14.625,6.058c5.293,0,10.587-2.019,14.624-6.057C514.018,497.866,514.018,484.771,505.942,476.694z'/></g></g>
        </svg></a>";
        }
        // Overlay
        $contentOverlay = '';
        if (get_theme_mod('ocn_overlay', 'no') != 'no') {
            $contentOverlay = '<div id="ocn-cookie-overlay"></div>';
        }
        echo "{$contentOverlay}<div id='ocn-cookie-wrap' class='{$style}'>{$contentButtonClose}
        <div id='ocn-cookie-inner'><p class='ocn-cookie-content'>{$content}</p>{$contentButton}</div></div>";
    }
    function handleFooterPrintScripts()
    {
        if ($this->isCookieAccepted()) {
            $scripts = html_entity_decode(trim(wp_kses(get_theme_mod('ocn_body_scripts'), $this->getAllowedHtml())));
            if (empty($scripts) == false) {
                echo $scripts;
            }
        }
    }
}