<?php

namespace wp;

class CustomizerControlButtonset extends CustomizerControl
{
    /**
     * @inheritdoc
     */
    public $type = 'oceanwp-buttonset';
    /**
     * @inheritdoc
     */
    public function enqueue()
    {
        wp_enqueue_script('oceanwp-buttonset', self::$uriToDirCustomizer . 'buttonset.js',
            ['jquery', 'customize-base'], false, true);
        wp_enqueue_style('oceanwp-buttonset', self::$uriToDirCustomizer . 'buttonset.css');
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
        echo '<# if ( data.label ) { #>
        <span class="customize-control-title">{{{ data.label }}}</span>
        <# } #>
        <# if ( data.description ) { #>
        <span class="description customize-control-description">{{{ data.description }}}</span>
        <# } #>
        <div id="input_{{ data.id }}" class="buttonset">
            <# for ( key in data.choices ) { #>
            <input {{{ data.inputAttrs }}} class="switch-input" type="radio" value="{{ key }}"
                   name="_customize-radio-{{{ data.id }}}" id="{{ data.id }}{{ key }}" {{{ data.link }}}<# if ( key ===
            data.value ) { #> checked="checked" <# } #>>
            <label class="switch-label switch-label-<# if ( key === data.value ) { #>on <# } else { #>off<# } #>"
                   for="{{ data.id }}{{ key }}">
                {{ data.choices[ key ] }}
            </label>
            </input>
            <# } #>
        </div>';
    }
}