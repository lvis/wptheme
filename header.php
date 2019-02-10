<!DOCTYPE html>
<html lang="<?= get_bloginfo('language'); ?>">
<head>
<?php

use wp\WidgetArea;
use wp\WPUtils;

if (is_home() || is_front_page()) {
    $pageTitle = get_the_title(get_option('page_for_posts', true));
}
if (is_category() || is_tax() || is_tag()) {
    $termPageTitle = single_term_title('', false);
    if (empty($termPageTitle) == false) {
        $pageTitle = $termPageTitle;
    }
} else if (is_404()) {
    $pageTitle = '404';
} else {
    $pageTitle = get_the_title();
}
$blogName = get_option('blogname');
echo "<title>{$pageTitle} - {$blogName}</title>";
?>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta name="apple-mobile-web-app-title" content="<?php bloginfo('name'); ?>">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="format-detection" content="telephone=no">
<?php
$urlSiteIcon = get_site_icon_url();
if ($urlSiteIcon) {
    echo "<link rel='apple-touch-icon' href='{$urlSiteIcon}'>\n<link rel='shortcut icon' href='{$urlSiteIcon}'>\n";
}
wp_head();
?>
</head>
<body <?php body_class(); ?>>
<header>
    <?php

    use wp\WPSidebar;
    use wp\WidgetSiteBranding;

    $content = '';
    $content .= WPUtils::getSidebarContent(WidgetArea::HEADER_TOP);
    $content .= WPUtils::getSidebarContent(WidgetArea::HEADER_MAIN);
    $content .= WPUtils::getSidebarContent(WidgetArea::HEADER_BOTTOM);
    if (empty($content)) {
        ob_start();
        the_widget(WidgetSiteBranding::class, [], [
            WPSidebar::BEFORE_WIDGET => '<div class="widget %s text-xs-center">'
        ]);
        echo WPUtils::getSidebar(WidgetArea::HEADER_TOP, ob_get_clean());
    } else {
        echo $content;
    }
    ?>
</header>
<main>