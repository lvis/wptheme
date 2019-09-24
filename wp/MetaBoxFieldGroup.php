<?php
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/6/18
 * Time: 10:12 AM
 */

namespace wp;


class MetaBoxFieldGroup extends MetaBoxField
{
    /**
     * @const
     */
    const DUPLICATABLE = 'clone';
    /**
     * @const
     */
    const SORT_DUPLICATES = 'sort_clone';
    /**
     * @const
     */
    const FIELDS = 'fields';
    /**
     * @const
     */
    const COLLAPSIBLE = 'collapsible';
    /**
     * @const
     */
    const SAVE_STATE = 'save_state';
    /**
     * @const
     */
    const GROUP_TITLE = 'group_title';
}