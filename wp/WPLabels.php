<?php
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/5/18
 * Time: 6:55 PM
 */

namespace wp;
class WPLabels
{
    /**
     * @const
     */
    const NAME_PLURAL = 'name';
    /**
     * @const
     */
    const NAME_SINGULAR = 'singular_name';
    /**
     * @const
     */
    const ITEM_ADD_NEW = 'add_new_item';
    /**
     * @const
     */
    const ITEM_EDIT = 'edit_item';
    /**
     * @const
     */
    const ITEMS_SEARCH = 'search_items';
    /**
     * @const This string isn't used on non-hierarchical types or taxonomies.
     * In hierarchical ones the default is __( 'Parent Page:' ) or __( 'Parent Category:' )
     */
    const ITEM_PARENT_COLON = 'parent_item_colon';
    /**
     * @const
     */
    const NOT_FOUND = 'not_found';
    /**
     * @const Default is __( 'View Post' ) or __( 'View Page' ) or  __( 'View Tag' ) or __( 'View Category' )
     */
    const ITEM_VIEW = 'view_item';
}