<?php

namespace wp;
class CustomizerControlSeparator extends \WP_Customize_Control
{
    public function render_content()
    {
        $content = "";
        if (!empty($this->label)) {
            $content .= sprintf('<h2 class="customize-control-title">%s</h2>',
                esc_html($this->label));
        }
        if (!empty($this->description)) {
            $content .= sprintf('<span class="description customize-control-description">%s</span>',
                esc_html($this->description));
        }
        printf('<label>%s<hr></label>', $content);
    }
}