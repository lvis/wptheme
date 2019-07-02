<?php

use wp\QueryPost;
use wp\WidgetArea;
use wp\WidgetPosts;
use wp\WidgetPost;
use wp\WPUtils;

$content = '';
if (post_password_required()) {
    $content .= get_the_password_form();
} else {
    $content .= WPUtils::getSidebarContent(WidgetArea::CONTENT_TOP);
    $content .= WPUtils::getSidebarContent(WidgetArea::CONTENT_MAIN);
    $content .= WPUtils::getSidebarContent(WidgetArea::CONTENT_BOTTOM);
}
//TODO Investigate case when user not use any widget what to display for different page type
if (empty($content)) {
    ob_start();
    the_widget(WidgetPost::class);
    echo WPUtils::getSidebar(WidgetArea::CONTENT_MAIN, ob_get_clean(), 'main', ['container']);
} else {
    echo $content;
}