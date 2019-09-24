<?php

use wp\PostProperty;
use wp\UtilsWp;

$postId = get_the_ID();
$postName = get_the_title();
$postLink = get_permalink();
$argsThumb = ['class' => 'media-object', 'alt' => $postName, 'title' => $postName];
$postImage = UtilsWp::getThumbnail([95, 64], $argsThumb);

$contentFormattedStatus = PostProperty::getStatusFormatted();
$contentFormattedPrice = PostProperty::getPriceFormatted();
$contentFormattedType = PostProperty::getTypeFormatted();

echo "<div class='media col-md-12 col-sm-4 col-xs-12'>
    <div class='media-left'>
        <a href='{$postLink}'>{$postImage}</a>
    </div>
    <div class='media-body'>
        <h4 class='media-heading'>{$contentFormattedStatus}</h4>
        <h5>{$contentFormattedType}</h5>
        <p>{$contentFormattedPrice}</p>
    </div>
</div>";