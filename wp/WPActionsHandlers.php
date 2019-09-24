<?php
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/5/18
 * Time: 8:25 PM
 */

namespace wp;
final class WPActionsHandlers
{
    /**
     * @const Adds REST API link tag into page header
     * @url https://developer.wordpress.org/reference/functions/rest_output_link_wp_head/
     */
    const ADD_LINK_OF_REST = 'rest_output_link_wp_head';

    const ADD_LINK_OF_REST_HEADER = 'rest_output_link_header';
    /**
     * @const Adds oEmbed discovery links in the website
     * @url https://developer.wordpress.org/reference/functions/wp_oembed_add_discovery_links/
     */
    const ADD_LINKS_OF_oEMBED = 'wp_oembed_add_discovery_links';
    /**
     * @const Adds Generator that is generated on the wp_head hook
     * @url https://developer.wordpress.org/reference/functions/wp_generator/
     */
    const ADD_GENERATOR = 'wp_generator';
}