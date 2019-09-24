<?php namespace wp;
class CustomizerControlSortable extends CustomizerControl {
    /**
     * @inheritdoc
     */
    public $type = 'oceanwp-sortable';

    /**
     * @inheritdoc
     */
    public function enqueue() {
        $dependencyScripts = ['jquery',
                              'customize-base',
                              'jquery-ui-core',
                              'jquery-ui-sortable'];
        wp_enqueue_script('control-sortable', self::$uriToDirCustomizer . 'sortable.js', $dependencyScripts, false, true);
        wp_enqueue_style('control-sortable', self::$uriToDirCustomizer . 'sortable.css');
    }

    /**
     * @inheritdoc
     */
    public function to_json() {
        parent::to_json();
        $this->json['default'] = $this->setting->default;
        if (isset($this->default)) {
            $this->json['default'] = $this->default;
        }
        $this->json['value'] = maybe_unserialize($this->value());
        $this->json['choices'] = $this->choices;
        $this->json['link'] = $this->get_link();
        $this->json['id'] = $this->id;
        $this->json['inputAttrs'] = '';
        foreach ($this->input_attrs as $attr => $value) {
            $this->json['inputAttrs'] .= $attr . '="' . esc_attr($value) . '" ';
        }
        $this->json['inputAttrs'] = maybe_serialize($this->input_attrs());
    }

    /**
     * @inheritdoc
     */
    protected function content_template() {
        echo '<label class="oceanwp-sortable">
			<span class="customize-control-title">
				{{{ data.label }}}
			</span>
			<# if ( data.description ) { #>
				<span class="description customize-control-description">{{{ data.description }}}</span>
			<# } #>

			<ul class="sortable">
				<# _.each( data.value, function( choiceID ) { #>
					<li {{{ data.inputAttrs }}} class="oceanwp-sortable-item" data-value="{{ choiceID }}">
						<i class="dashicons dashicons-menu"></i>
						<i class="dashicons dashicons-visibility visibility"></i>
						{{{ data.choices[ choiceID ] }}}
					</li>
				<# }); #>
				<# _.each( data.choices, function( choiceLabel, choiceID ) { #>
					<# if ( -1 === data.value.indexOf( choiceID ) ) { #>
						<li {{{ data.inputAttrs }}} class="oceanwp-sortable-item invisible" data-value="{{ choiceID }}">
							<i class="dashicons dashicons-menu"></i>
							<i class="dashicons dashicons-visibility visibility"></i>
							{{{ data.choices[ choiceID ] }}}
						</li>
					<# } #>
				<# }); #>
			</ul>
		</label>';
    }
}