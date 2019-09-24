<?php
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/6/18
 * Time: 10:10 AM
 */

namespace wp;


class MetaBoxFieldTaxonomyType
{
    /**
     * @const simple select box of items (default).
     * Optional. If choosing this field type, then the field can have options from select field (such as placeholder).
     * @link https://metabox.io/docs/define-fields/#section-taxonomy
     */
    const SELECT = 'select';
    /**
     * @const hierachical list of select boxes which allows to select multiple items
     * (select/deselect parent item will show/hide child items)
     */
    const SELECT_TREE = 'select_tree';
    /**
     * @const beautiful select box using select2 library. If choosing this field type, then the field can have options
     * from select_advanced field (such as placeholder), please check select_advanced field for full list of params.
     */
    const SELECT_ADVANCED = 'select_advanced';
    /**
     * @const flatten list of checkboxes which allows to select multiple items
     */
    const CHECKBOX_LIST = 'checkbox_list';
    /**
     * @const hierachical list of checkboxes which allows to select multiple items (select/deselect parent item
     * will show/hide child items)
     */
    const CHECKBOX_TREE = 'checkbox_tree';
}