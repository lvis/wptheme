<?php
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/5/18
 * Time: 6:56 PM
 */

namespace wp;
final class WPTaxonomyLabels extends WPLabels
{
    /**
     * @const
     */
    const NAME_IN_MENU = 'menu_name';
    /**
     * @const
     */
    const CHOSE_FROM_MOST_USED = 'choose_from_most_used';
    /**
     * @const
     */
    const ITEM_PARENT = 'parent_item';
    /**
     * @const
     */
    const ITEM_UPDATE = 'update_item';

    /**
     * @const
     */
    const ITEM_NEW_NAME = 'new_item_name';
    /**
     * @const
     */
    const ITEMS_ALL = 'all_items';

    /**
     * @const
     */
    const ITEMS_POPULAR = 'popular_items';
    /**
     * @const
     */
    const ITEMS_SEPARATE_WITH_COMMAS = 'separate_items_with_commas';
    /**
     * @const
     */
    const ITEMS_ADD_OR_REMOVE = 'add_or_remove_items';
}