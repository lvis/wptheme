<div class="col-md-4 col-sm-6 col-xs-12">
    <a href="<?= get_the_permalink(); ?>" class="d-xs-block" title="<?= get_the_title(); ?>">
        <?= \wp\UtilsWp::getThumbnail(\wp\WPImages::THUMB,
                                      ["class" => "img img-raised", "alt" => get_the_title(), "title" => get_the_title()]); ?>
    </a>
</div>