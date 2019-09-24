<?php
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/6/18
 * Time: 10:11 AM
 */

namespace wp;


class MetaBoxFieldSelect extends MetaBoxField
{
    /**
     * @const array of 'value' => 'Label' pairs. value is stored in the custom field and Label is used to display in the dropdown.
     */
    const OPTIONS = 'options';
    /**
     * @const instruction text for users to select value, like “Please select…”
     */
    const PLACEHOLDER = 'placeholder';
    /**
     * @const allow to select multiple values or not. Can be true or false. Optional. Default false.
     */
    const MULTIPLE = 'multiple';
    /**
     * @const whether to show “Select: All | None” links that can help users select all options or clear selection. Used only when multiple is true. Optional. Default false.
     */
    const ALL_NONE = 'select_all_none';
}