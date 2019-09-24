<?php

namespace wp;

class CustomizerControlMultipleSelect extends CustomizerControl
{
    /**
     * @inheritdoc
     */
    public $type = 'oceanwp-multiple-select';
    /**
     * @inheritdoc
     */
    public function enqueue()
    {
        wp_enqueue_script('select2', self::$uriToDirCustomizer . 'select2.js',
            ['jquery'], false, true);
        wp_enqueue_style('select2', self::$uriToDirCustomizer . 'select2.css', null);
        wp_enqueue_script('oceanwp-multiple-select', self::$uriToDirCustomizer . 'multiple-select.js',
            ['jquery', 'customize-base', 'select2'], false, true);
        wp_enqueue_style('oceanwp-multiple-select', self::$uriToDirCustomizer . 'multiple-select.css');
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
        $this->json['value'] = (array)$this->value();
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
        echo '<# if ( ! data.choices ) { return; } #>
		<# if ( data.label ) { #>
			<span class="customize-control-title">{{ data.label }}</span>
		<# } #>
		<# if ( data.description ) { #>
			<span class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>
		<select {{{ data.inputAttrs }}} multiple="multiple" {{{ data.link }}}>
			<# _.each( data.choices, function( label, choice ) { #>
				<option value="{{ choice }}" <# if ( -1 !== data.value.indexOf( choice ) ) { #> selected="selected" <# } #>>{{ label }}</option>
			<# } ) #>
		</select>';
    }
}