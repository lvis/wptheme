<?php
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/5/18
 * Time: 6:55 PM
 */

namespace wp;
final class WPostLabels extends WPLabels
{
    /**
     * @const
     */
    const ITEMS_ALL = 'all_items';
    /**
     * @const Default is __( 'New Post' ) or __( 'New Page' ).
     */
    const ITEM_NEW = 'new_item';
    /**
     * @const The add new text. The default is __( "Add New" )
     */
    const ADD_NEW = 'add_new';
    /**
     * @const
     */
    const NOT_FOUND_IN_TRASH = 'not_found_in_trash';
}