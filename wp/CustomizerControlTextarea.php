<?php

namespace wp;

class CustomizerControlTextarea extends CustomizerControl
{
    /**
     * @inheritdoc
     */
    public $type = 'oceanwp-textarea';
    /**
     * @inheritdoc
     */
    public function enqueue()
    {
        wp_enqueue_script('oceanwp-textarea', self::$uriToDirCustomizer . 'textarea.js',
            ['jquery', 'customize-base'], false, true);
        wp_enqueue_style('oceanwp-textarea', self::$uriToDirCustomizer . 'textarea.css');
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
            <textarea rows="3" {{{ data.inputAttrs }}} {{{ data.link }}}>{{ data.value }}</textarea>
            <# if ( data.description ) { #>
            <span class="description customize-control-description">{{{ data.description }}}</span>
            <# } #>
        </label>';
    }
}