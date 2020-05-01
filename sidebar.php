<?php

use wp\QueryPost;
use wp\WidgetArea;
use wp\WidgetPosts;
use wp\WidgetPost;
use wp\UtilsWp;

$content = '';
if (post_password_required()) {
    $content .= get_the_password_form();
} else {
    $content .= UtilsWp::getSidebarContent(WidgetArea::CONTENT_TOP);
    $content .= UtilsWp::getSidebarContent(WidgetArea::CONTENT_MAIN);
    $content .= UtilsWp::getSidebarContent(WidgetArea::CONTENT_BOTTOM);
}
//TODO Investigate case when user not use any widget what to display for different page type
if (empty($content)) {
    ob_start();
    the_widget(WidgetPost::class);
    echo UtilsWp::getSidebar(WidgetArea::CONTENT_MAIN, ob_get_clean(), ['container']);
} else {
    echo $content;
}