<?php

use wp\WPImages;
use wp\UtilsWp;

$itemTitle = get_the_title();
$itemLink = get_the_permalink();
$itemThumbArgs = ["class" => "img img-raised", "alt" => $itemTitle, "title" => $itemTitle];
$itemThumb = UtilsWp::getThumbnail(WPImages::THUMB, $itemThumbArgs);
$itemCategory = '';
$itemDateOfUpdate = get_the_modified_time('c');
$itemAuthorAndDate = UtilsWp::getPostAuthorAndDate();
if (is_category() == false) {
    foreach ((get_the_category()) as $category) {
        $categoryLink = get_term_link($category->cat_ID);
        $itemCategory = "<a href='{$categoryLink}' class='text-info'>{$category->name}</a>";
    }
}
echo "<article class='col-md-4 col-sm-6 col-xs-12'>
<a href='{$itemLink}'>
    <figure class='d-xs-block'>{$itemThumb}<figcaption class='text-hide-overflow'>{$itemTitle}</figcaption></figure>
</a>
<p class='category'>{$itemCategory}</p>s
<time class='description' datetime='{$itemDateOfUpdate}'>{$itemAuthorAndDate}</time>
</article>";