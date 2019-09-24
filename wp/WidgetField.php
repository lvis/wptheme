<?php
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/5/18
 * Time: 11:55 PM
 */

namespace wp;
final class WidgetField
{
    const TEXT = "text";
    const TEXT_MULTIPLE = "text_multiple";
    const NUMBER = "number";
    const IMAGES = "images";
    const IMAGES_WITH_URL = "imagesWithUrl";
    const RANGE = "range";
    const RADIO = "radio";
    const SELECT = "select";
    const SELECT_MULTIPLE = "select_multiple";
    const CHECKBOX = "checkbox";
    const CHECKBOX_MULTIPLE = "checkbox_multiple";
    public $type = "";
    public $name = "";
    public $title = "";
    public $options = [];
    public $defaultValue = "";

    function __construct($type, string $name, string $title, array $options = [], $defaultValue = '')
    {
        $this->type = $type;
        $this->name = $name;
        $this->title = $title;
        $this->options = $options;
        $this->defaultValue = $defaultValue;
    }
}