<?php

namespace wp;

class CustomizerControlIconSelect extends CustomizerControl
{
    /**
     * @inheritdoc
     */
    public $type = 'oceanwp-icon';
    /**
     * @inheritdoc
     */
    public function enqueue()
    {
        wp_enqueue_script('oceanwp-icon-select', self::$uriToDirCustomizer . 'icon-select.js',
            ['jquery', 'customize-base'], false, true);
        wp_enqueue_style('oceanwp-icon-select', self::$uriToDirCustomizer . 'icon-select.css');
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

    }
    /**
     * @inheritdoc
     */
    protected function content_template()
    {
        echo '<label class="customizer-text">
            <# if ( data.label ) { #>
            <span class="customize-control-title">{{{ data.label }}}</span>
            <# } #>
            <# if ( data.description ) { #>
            <span class="description customize-control-description">{{{ data.description }}}</span>
            <# } #>
        </label>
        <div id="input_{{ data.id }}" class="icon-select clr">
            <# for ( key in data.choices ) { #>
            <label>
                <input class="icon-select-input" type="radio" value="{{ key }}"
                       name="_customize-icon-select-{{ data.id }}" {{{ data.link }}}<# if ( data.value === key ) { #>
                checked<# } #> />
                <span class="icon-select-label"><i class="{{ key }}"></i></span>
            </label>
            <# } #>
        </div>';
    }
}