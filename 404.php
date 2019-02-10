<?php

use wp\WPUtils;
use wp\WidgetArea;

get_header();
echo WPUtils::getSidebar(WidgetArea::CONTENT_MAIN, WPUtils::getNotFoundMessage());
get_footer();