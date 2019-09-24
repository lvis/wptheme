<?php

namespace wp;

class CustomizerControlMulticheck extends CustomizerControl
{
    /**
     * @inheritdoc
     */
    public $type = 'oceanwp-multi-check';
    /**
     * @inheritdoc
     */
    public function enqueue()
    {
        wp_enqueue_script('oceanwp-multicheck', self::$uriToDirCustomizer . 'multicheck.js',
            ['jquery', 'customize-base'], false, true);
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
        echo '<# if ( ! data.choices ) { return; } #>
		<# if ( data.label ) { #>
			<span class="customize-control-title">{{ data.label }}</span>
		<# } #>
		<# if ( data.description ) { #>
			<span class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>
		<ul>
			<# for ( key in data.choices ) { #>
				<li>
					<label>
						<input {{{ data.inputAttrs }}} type="checkbox" value="{{ key }}"<# if ( _.contains( data.value, key ) ) { #> checked<# } #> />
						{{ data.choices[ key ] }}
					</label>
				</li>
			<# } #>
		</ul>';
    }
}