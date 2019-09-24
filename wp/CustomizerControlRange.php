<?php

namespace wp;

class CustomizerControlRange extends CustomizerControl
{
    /**
     * @inheritdoc
     */
    public $type = 'oceanwp-range';
    /**
     * @inheritdoc
     */
    public function enqueue()
    {
        wp_enqueue_script('oceanwp-range', self::$uriToDirCustomizer . 'range.js',
            ['jquery', 'customize-base'], false, true);
        wp_enqueue_style('oceanwp-range', self::$uriToDirCustomizer . 'range.css');
    }
    /**
     * @inheritdoc
     */
    public function to_json()
    {
        parent::to_json();
        if (isset($this->default)) {
            $this->json['default'] = $this->default;
        } else {
            $this->json['default'] = $this->setting->default;
        }
        $this->json['value'] = $this->value();
        $this->json['choices'] = $this->choices;
        $this->json['link'] = $this->get_link();
        $this->json['id'] = $this->id;
        $this->json['inputAttrs'] = '';
        foreach ($this->input_attrs as $attr => $value) {
            $this->json['inputAttrs'] .= $attr . '="' . esc_attr($value) . '" ';
        }

    }
    /**
     * @inheritdoc
     */
    protected function content_template()
    {
        echo '<label>
            <# if ( data.label ) { #>
            <span class="customize-control-title">{{{ data.label }}}</span>
            <# } #>
            <# if ( data.description ) { #>
            <span class="description customize-control-description">{{{ data.description }}}</span>
            <# } #>
            <div class="control-wrap">
                <input type="range" {{{ data.inputAttrs }}} value="{{ data.value }}" {{{ data.link }}}
                       data-reset_value="{{ data.default }}"/>
                <input type="number" {{{ data.inputAttrs }}} class="oceanwp-range-input" value="{{ data.value }}"/>
                <span class="oceanwp-reset-slider"><span class="dashicons dashicons-image-rotate"></span></span>
            </div>
        </label>';
    }
}