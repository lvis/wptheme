<?php

use wp\WPImages;
use wp\UtilsWp;

?>
<div class="col-md-4 col-sm-6 col-xs-12">
    <div class="card card-plain card-blog">
        <div class="card-image">
            <a href="<?= get_the_permalink(); ?>" class="d-xs-block">
                <?= UtilsWp::getThumbnail(WPImages::THUMB,
                                          ["class" => "img img-raised", "alt" => get_the_title(), "title" => get_the_title()]); ?>
            </a>
        </div>
        <div class="card-content">
            <?php //TODO Here If current category is same as post Show the Tags instead or Disable link  ?>
            <h5 class="card-title text-hide-overflow">
                <a href="<?= get_the_permalink(); ?>">
                    <?= get_the_title(); ?>
                </a>
            </h5>
            <h6 class="category">
                <?php
                if (is_category() == false) {
                    foreach ((get_the_category()) as $category) {
                        $categoryLink = get_term_link($category->cat_ID);
                        echo "<a href='{$categoryLink}' class='text-info'>{$category->name}</a>";
                    }
                }
                ?>
            </h6>
            <div class="card-description">
                <time datetime="<?= get_the_modified_time('c'); ?>">
                    <?= UtilsWp::getPostAuthorAndDate(); ?>
                </time>
            </div>
        </div>
    </div>
</div>