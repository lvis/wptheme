<?php namespace wp;
/** Author: Vitali Lupu <vitaliix@gmail.com> */
class MetaBoxShowHide {
    /**
     * @const
     */
    const HIDE = "hide";
    /**
     * @const
     */
    const SHOW = "show";
    /**
     * @const
     */
    const RELATION = "relation";
    /**
     * @const List of page templates, match if the current page has a page template in the list.
     * Array. Case insensitive. Optional.
     */
    const TEMPLATE = "template";
    /**
     * @const List of post formats, match if the current post has a format in the list.
     * Array. Case insensitive. Optional.
     */
    const POST_FORMAT = "post_format";
    /**
     * @const List of custom taxonomy terms’ IDs or names (NO slugs).
     * Here taxonomy_slug is the slug of the taxonomy (like section, region, etc.).
     * Match if the current post has a term in the list. Array. Case sensitive. Optional.
     */
    const TAXONOMY_SLUG = "taxonomy_slug";
    /**
     * @const Boolean. Match if the current page is a child page or not. Optional.
     */
    const IS_CHILD = "is_child";
    /**
     * @const Boolean. Array of pairs of CSS selectors and values.
     * Match if the inputs (with specified CSS selector) has the defined value.
     * Note: the relation is also applied to rules here. Added in version 0.2.
     */
    const INPUT_VALUE = "input_value";
    const CONDITION_AND = "AND";
    const CONDITION_OR = "OR";
}