<?php
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/5/18
 * Time: 6:32 PM
 */

namespace wp;
class WidgetSlider extends Widget
{
    const SLIDER_LIST = "widgetSliderList";
    function __construct()
    {
        parent::__construct(__('Media Slider', 'LayerSlider'), __('Insert sliders with the Widget', 'LayerSlider'));
    }
    function initFields()
    {
        if (class_exists('LS_Sliders')) {
            $dbSliders = \LS_Sliders::find(['limit' => 100]);
            $sliders = [];
            foreach ($dbSliders as $dbSlide) {
                if (isset($dbSlide['id']) && isset($dbSlide['name'])) {
                    $sliders[$dbSlide['id']] = $dbSlide['name'];
                }
            }
            $this->addField(new WidgetField(WidgetField::SELECT, self::SLIDER_LIST,
                __('Choose a slider:', 'LayerSlider'), $sliders, 0));
        }
    }
    function widget($args, $instance)
    {
        $currentSlider = self::getInstanceValue($instance, self::SLIDER_LIST, $this);
        $shortCode = "[layerslider id='{$currentSlider}']";
        $content = do_shortcode($shortCode);
        if ($content != $shortCode){
            $args[WPSidebar::CONTENT] = $content;
            parent::widget($args, $instance);
        }
    }
}