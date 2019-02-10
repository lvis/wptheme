<?php
/**
 * The Template for displaying all single products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see        https://docs.woocommerce.com/document/template-structure/
 * @author     WooThemes
 * @package    WooCommerce/Templates
 * @version    1.6.4
 */
defined( 'ABSPATH' ) || exit;
get_header('shop');
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
echo '<div class="container">';
/**
 * woocommerce_before_main_content hook.
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 */
do_action('woocommerce_before_main_content');
while (have_posts()) {
    the_post();
    wc_get_template_part('content', 'single-product');
}
remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
/**
 * woocommerce_after_main_content hook.
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action('woocommerce_after_main_content');
echo '</div>';
/**
 * woocommerce_sidebar hook.
 * @hooked woocommerce_get_sidebar - 10
 */
//do_action('woocommerce_sidebar');
get_footer('shop');