<?php

use wp\PostProperty;
use wp\UtilsWp;
use wp\WPImages;

$postId = get_the_ID();
$postName = get_the_title();
$postLink = get_permalink();
$argsThumb = ['class' => 'img-responsive', 'alt' => $postName, 'title' => $postName];
$postImage = UtilsWp::getThumbnail(WPImages::THUMB, $argsThumb);
$textCode = __('Code');
$content = '';
$contentOfferStatus = PostProperty::getOfferStatusMarkup();
$contentFormattedStatus = PostProperty::getStatusFormatted();
$contentFormattedPrice = PostProperty::getPriceFormatted();
$contentFormattedLocation = PostProperty::getLocationFormatted();
/** @var $typeTerm WP_Term */
$typeTerm = PostProperty::getTaxonomyFirstTerm(PostProperty::TAX_TYPE);
$plotsList = ['land-for-construction', 'plot-of-land', 'agricultural-plot-of-land'];
if ($typeTerm && in_array($typeTerm->slug, $plotsList)) {
    $content .= PostProperty::getSizeOfLandFormatted();
} else {
    $content .= PostProperty::getRoomsFormatted();
    $content .= PostProperty::getSizeFormatted();
}
echo "<div class='col-md-4 col-sm-4 col-xs-6 wrapper-panel-advertise'>
<div class='panel panel-advertise'>
    <div class='panel-heading'>
        <figure>
            <a href='{$postLink}'>{$postImage}{$contentOfferStatus}</a>
            <figcaption>
                <h5 class='label-price'><small>{$contentFormattedStatus}</small> {$contentFormattedPrice}</h5>
            </figcaption>
        </figure>
    </div>
    <div class='panel-body'>
        <aside>
            <h5 class='col-sm-8 col-xs-9 no-overflow'>{$contentFormattedLocation}</h5>
            <h5 class='col-sm-4 col-xs-3 text-muted text-right'><span>{$textCode}:</span>{$postId}</h5>
        </aside>
    </div>
    <div class='panel-footer text-center'>{$content}</div>
</div></div>";