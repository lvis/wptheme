<?php
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/5/18
 * Time: 6:32 PM
 */

namespace wp;
final class WidgetContacts extends Widget
{
    const SITE_CONTACTS = "siteContacts";

    function __construct()
    {
        parent::__construct(__('Site Contacts', WpApp::TEXT_DOMAIN));
    }

    protected static $siteContacts;

    static function getSiteContacts()
    {
        if (!self::$siteContacts) {
            self::$siteContacts = [
                SettingsSite::ADDRESS => __("Address", WpApp::TEXT_DOMAIN),
                SettingsSite::EMAIL   => __("Email Address"),
                SettingsSite::PHONES  => __("Phone Numbers", WpApp::TEXT_DOMAIN)
            ];
        }

        return self::$siteContacts;
    }

    protected static $settingsIconsFa = [
        SettingsSite::ADDRESS => "fa fa-map-marker",
        SettingsSite::EMAIL   => "fa fa-envelope",
        SettingsSite::PHONES  => "fa fa-phone"
    ];

    static function getSettingsIconFa($settings)
    {
        return self::$settingsIconsFa[$settings];
    }

    /**
     * https://developer.apple.com/library/content/featuredarticles/iPhoneURLScheme_Reference/MapLinks/MapLinks.html
     * for maps: "http://maps.apple.com/?q=" - apple / "https://www.google.com/maps/?q=" - google
     */
    protected static $referencePrefixes;

    static function getReferencePrefixes($settings)
    {
        if (!self::$referencePrefixes) {
            //TODO For case when don't have contact page on site load with google maps
            self::$referencePrefixes = [
                //			SettingsSite::ADDRESS => "https://www.google.com/maps/?q=",
                SettingsSite::ADDRESS => get_home_url(null, 'contacts/'),
                SettingsSite::EMAIL   => "mailto:",
                SettingsSite::PHONES  => "tel:"
            ];
        }

        return self::$referencePrefixes[$settings];
    }

    function initFields()
    {
        $this->addField(new WidgetField(WidgetField::SELECT_MULTIPLE, WidgetContacts::SITE_CONTACTS,
            __("Site Contacts", WpApp::TEXT_DOMAIN), WidgetContacts::getSiteContacts(), ""));
        parent::initFields();
    }

    function widget($args, $instance)
    {
        $content = "";
        $contacts = (array)$instance[self::SITE_CONTACTS];
        if (count($contacts) == 0) {
            $contacts = array_keys(WidgetContacts::getSiteContacts());
        }
        foreach ($contacts as $key) {
            $contactValue = get_option($key);
            if ($contactValue) {
                if ($key == SettingsSite::PHONES) {
                    $phones = explode(",", $contactValue);
                    foreach ($phones as $contactValue) {
                        $phone = preg_replace('/[^0-9]/', '', $contactValue);
                        $hrefPrefix = WidgetContacts::getReferencePrefixes($key);
                        $classIcon = WidgetContacts::getSettingsIconFa($key);
                        $content .= "<a href='{$hrefPrefix}$phone' class='{$key} btn-custom' rel='nofollow'>
                        <i class='{$classIcon}'></i><span>{$contactValue}</span></a>";
                    }
                } else if ($key == SettingsSite::ADDRESS) {
                    $hrefPrefix = WidgetContacts::getReferencePrefixes($key);
                    $classIcon = WidgetContacts::getSettingsIconFa($key);
                    $content .= "<a href='{$hrefPrefix}' class='{$key} btn-custom'><i class='{$classIcon}'></i>
                    <span>{$contactValue}</span></a>";
                } else {
                    $hrefPrefix = WidgetContacts::getReferencePrefixes($key);
                    $classIcon = WidgetContacts::getSettingsIconFa($key);
                    $content .= "<a href='{$hrefPrefix}{$contactValue}' class='{$key} btn-custom' rel='nofollow'>
                    <i class='{$classIcon}'></i><span>{$contactValue}</span></a>";
                }
            }
        }
        $args[WPSidebar::CONTENT] = $content;
        parent::widget($args, $instance);
    }
}