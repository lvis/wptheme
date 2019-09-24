<?php
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/6/18
 * Time: 10:10 AM
 */

namespace wp;

/**
 * This field does NOT save term IDs in post meta, instead of that, it only set post terms.
 */
class MetaBoxFieldTaxonomy extends MetaBoxFieldSelect
{
    /**
     * @const array or string of taxonomy slug(s)
     * @link https://metabox.io/docs/define-fields/#section-taxonomy
     */
    const TAXONOMY = 'taxonomy';
    /**
     * @const
     */
    const FLATTEN = 'flatten';
    /**
     * @const how to show taxonomy: [ select (default),  select_tree, select_advanced, checkbox_list, checkbox_tree]
     */
    const FIELD_TYPE = 'field_type';
    /**
     * @const additional query arguments, like in get_terms() function. Optional.
     */
    const QUERY_ARGS = 'query_args';
}