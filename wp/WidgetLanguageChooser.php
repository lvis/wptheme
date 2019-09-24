<?php
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/5/18
 * Time: 6:32 PM
 */

namespace wp;
class WidgetLanguageChooser extends WidgetDialogBase
{
    const DISPLAY = "languageChooserDisplay";
    const DISPLAY_LIST = "languageChooserDisplayList";
    const DISPLAY_SELECT = "languageChooserDisplaySelect";

    function __construct()
    {
        parent::__construct(__('Language Chooser', WpApp::TEXT_DOMAIN),
            __('Allows your visitors to choose a Language.', 'qtranslate'));
    }

    function enqueueScriptsTheme()
    {
        $uriToDirLibs = UtilsWp::getUriToLibsDir(__FILE__);
        wp_enqueue_style('WidgetLanguageChooser', "{$uriToDirLibs}/WidgetLanguageChooser.css");
    }

    function initFields()
    {
        $this->addField(new WidgetField(WidgetField::RADIO, self::DISPLAY, __('Display'), [
            self::DISPLAY_LIST => __('List'),
            self::DISPLAY_SELECT => __('Select')
        ], self::DISPLAY_LIST));
    }

    function widget($args, $instance)
    {
        $content = "";
        $getSortedLanguages = 'qtranxf_getSortedLanguages';
        $convertLanguageUrl = 'qtranxf_convertURL';
        if (function_exists($getSortedLanguages) && function_exists($convertLanguageUrl)) {
            $languageChooserDisplay = self::getInstanceValue($instance, self::DISPLAY, $this);
            global $q_config;
            $url = '';
            if (is_404()) {
                $url = get_option('home');
            }
            if ($languageChooserDisplay == self::DISPLAY_SELECT) {
                foreach ($getSortedLanguages() as $language) {
                    $languageName = $q_config['language_name'][$language];
                    $languageHref = $convertLanguageUrl($url, $language, false, true);
                    $languageSelected = '';
                    if ($language == $q_config['language']) {
                        $languageSelected = 'selected';
                    }
                    $optionValue = addslashes(htmlspecialchars_decode($languageHref, ENT_NOQUOTES));
                    $content .="<option value='$optionValue' {$languageSelected}>{$languageName}</option>";
                }
                $content = "<select onchange='document.location.href=this.value;'>$content</select>";
            } else {
                foreach ($getSortedLanguages() as $language) {
                    $languageName = $q_config['language_name'][$language];
                    $languageHref = $convertLanguageUrl($url, $language, false, true);
                    $languageSelected = '';
                    if ($language == $q_config['language']) {
                        $languageSelected = ' active';
                    }
                    $content .= "<a href='$languageHref' hreflang='$language' title='$languageName' class='{$language}{$languageSelected}'>
                    <span>$languageName</span></a>";
                }
            }

        }
        $args[WPSidebar::CONTENT] = $content;
        parent::widget($args, $instance);
    }
}