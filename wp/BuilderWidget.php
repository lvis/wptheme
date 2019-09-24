<?php


namespace wp;


class BuilderWidget
{
    /**
     * @const A category for displaying Basic widgets
     */
    const CATEGORY_BASIC = 'basic';
    /**
     * @const A category for displaying General widgets
     */
    const CATEGORY_GENERAL = 'general';
    /**
     * @const A category for displaying Wordpress widgets
     */
    const CATEGORY_WORDPRESS = 'wordpress';
    /**
     * @const A category for displaying Advanced widgets (Builder Pro plugin needed)
     */
    const CATEGORY_PRO_ELEMENTS = 'pro-elements';
    /**
     * @const A category for displaying Theme widgets (Builder Pro plugin needed)
     */
    const CATEGORY_THEME_ELEMENTS = 'theme-elements';
    /**
     * @const A category for displaying WooCommerce widgets (Builder Pro plugin needed)
     */
    const CATEGORY_WOO_ELEMENTS = 'woocommerce-elements';
    /**
     * @const A category for displaying Pojo widgets (Pojo theme needed)
     */
    const CATEGORY_POJO_ELEMENTS = 'pojo';

}