<div class="card card-plain card-blog">
    <div class="col-md-4">
        <div class="card-image">
            <a href="<?= get_the_permalink(); ?>">
                <?= \wp\UtilsWp::getThumbnail(\wp\WPImages::THUMB,
                                              ["class" => "img img-raised", "alt" => get_the_title(), "title" => get_the_title()]); ?>
            </a>
            <div class="ripple-container"></div>
        </div>
    </div>
    <div class="col-md-8">
        <?php //TODO Here If current category is same as post Show the Tags instead or Disable link  ?>
        <h6 class="category">
            <?php
            foreach ((get_the_category()) as $category) {
                $categoryLink = get_term_link($category->cat_ID);
                echo "<a href='{$categoryLink}' class='text-info'>{$category->name}</a>";
            } ?>
        </h6>
        <h5 class="card-title text-hide-overflow">
            <a href="<?= get_the_permalink(); ?>">
                <?= get_the_title(); ?>
            </a>
        </h5>
        <p class="card-description">
            <?= get_the_excerpt(); ?>
            <a href="<?= get_the_permalink(); ?>">
                    <span>
                        <?= __('Know More', WpApp::TEXT_DOMAIN); ?>
                    </span>
            </a>
        </p>
        <p class="card-author">
            <time datetime="<?= get_the_modified_time('c'); ?>">
                <?= \wp\UtilsWp::getPostAuthorAndDate(); ?>
            </time>
        </p>
    </div>
</div>