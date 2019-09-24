<?php
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/6/18
 * Time: 10:11 AM
 */

namespace wp;


final class MetaBoxFieldPost extends MetaBoxFieldSelectAdvanced
{
    /**
     * @const post type where posts are get from
     */
    const POST_TYPE = 'post_type';
    /**
     * @const how to show posts, can be:
     * select: simple select box of items (default). Optional. If choosing this field type, then the field can have options from select field (such as placeholder).
     * select_tree: hierachical list of select boxes which allows to select multiple items (select/deselect parent item will show/hide child items)
     * select_advanced: beautiful select box using select2 library. If choosing this field type, then the field can have options from select_advanced field (such as placeholder), please check select_advanced field for full list of params.
     * checkbox_list: flatten list of checkboxes which allows to select multiple items
     * checkbox_tree: hierachical list of checkboxes which allows to select multiple items (select/deselect parent item will show/hide child items)
     * radio_list: list of flatten radio boxes which allows to select only 1 item
     */
    const FIELD_TYPE = 'field_type';
    /**
     * @const additional query arguments, like in get_posts() function. Optional.
     */
    const QUERY_ARGS = 'query_args';
}