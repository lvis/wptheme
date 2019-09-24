<?php

use wp\UtilsWp;
use wp\WidgetArea;

get_header();
echo UtilsWp::getSidebar(WidgetArea::CONTENT_MAIN, UtilsWp::getNotFoundMessage());
get_footer();