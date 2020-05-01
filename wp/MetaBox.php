<?php namespace wp;
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 */
final class MetaBox extends MetaBoxBase
{
    /**
     * @const
     */
    const ID = 'id';
    /**
     * @const Custom post types which the meta box is for. There can be an array of multiple custom post types or
     * a string for the single post type. Must be in lowercase (like the slug).
     * Optional. Default: post
     */
    const POST_TYPES = 'post_types';
    /**
     * @const Part of the page where the meta box is displayed (normal, advanced or side).
     * Optional. Default: normal.
     */
    const CONTEXT = 'context';
    /**
     * @const Priority within the context where the box is displayed (high or low).
     * Optional. Default: high.
     */
    const PRIORITY = 'priority';
}