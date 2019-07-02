<?php

use wp\WidgetArea;
use wp\WPUtils;
use wp\WPSidebar;
use wp\WidgetSiteBranding;

$siteName = get_bloginfo('name', 'display');
$siteLanguage = get_bloginfo('language');
$siteCharset = get_bloginfo('charset', 'display');
//TODO Check if urlSiteIcon is empty then apply theme site icon
$urlSiteIcon = get_site_icon_url();
$contentHead = WPUtils::doAction('wp_head');
$contentBody = WPUtils::doAction('wp_body_open');
$bodyClasses = join(' ', get_body_class());
$content = '';
$content .= WPUtils::getSidebarContent(WidgetArea::HEADER_TOP);
$content .= WPUtils::getSidebarContent(WidgetArea::HEADER_MAIN);
$content .= WPUtils::getSidebarContent(WidgetArea::HEADER_BOTTOM);
if (empty($content)) {
    ob_start();
    the_widget(WidgetSiteBranding::class, [], [
        WPSidebar::BEFORE_WIDGET => '<div class="widget %s text-xs-center">'
    ]);
    $content = WPUtils::getSidebar(WidgetArea::HEADER_TOP, ob_get_clean());
}
echo "<!DOCTYPE html><html lang='{$siteLanguage}'>
<head>
    <meta charset='{$siteCharset}'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=no'>
    <meta name='apple-mobile-web-app-title' content='{$siteName}'>
    <meta name='apple-mobile-web-app-status-bar-style' content='black'>
    <meta name='apple-mobile-web-app-capable' content='yes'>
    <meta name='format-detection' content='telephone=no'>
    <link rel='apple-touch-icon' href='{$urlSiteIcon}'>
    <link rel='shortcut icon' href='{$urlSiteIcon}'>
    {$contentHead}
</head>
<body class='{$bodyClasses}'>
{$contentBody}
<header>{$content}</header>
<main>";