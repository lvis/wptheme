<div class='col-xl-3 col-md-4 col-sm-6 col-xs-12 woocommerce product'>
    <?php

    use wp\UtilsWooCommerce;

    do_action(UtilsWooCommerce::SHOP_LOOP);
    do_action(UtilsWooCommerce::SHOP_LOOP_ITEM_BEFORE);
    do_action(UtilsWooCommerce::SHOP_LOOP_ITEM_TITLE_BEFORE);
    do_action(UtilsWooCommerce::SHOP_LOOP_ITEM_TITLE);
    do_action(UtilsWooCommerce::SHOP_LOOP_ITEM_TITLE_AFTER);
    do_action(UtilsWooCommerce::SHOP_LOOP_ITEM_AFTER);
    ?>
</div>