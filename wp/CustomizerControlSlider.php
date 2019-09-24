<?php

namespace wp;

class CustomizerControlSlider extends CustomizerControl
{
    /**
     * @inheritdoc
     */
    public $type = 'oceanwp-slider';
    /**
     * @inheritdoc
     */
    public function enqueue()
    {
        wp_enqueue_script('oceanwp-slider', self::$uriToDirCustomizer . 'slider.js',
            ['jquery', 'customize-base', 'jquery-ui-slider'], false, true);
        wp_enqueue_style('oceanwp-slider', self::$uriToDirCustomizer . 'slider.css');
    }
    /**
     * @inheritdoc
     */
    protected function render()
    {
        $id = 'customize-control-' . str_replace(array('[', ']'), array('-', ''), $this->id);
        $class = 'customize-control has-switchers customize-control-' . $this->type;
        ?>
        <li id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($class); ?>">
        <?php $this->render_content(); ?>
        </li><?php
    }
    /**
     * @inheritdoc
     */
    public function to_json()
    {
        parent::to_json();
        $this->json['id'] = $this->id;
        $this->json['inputAttrs'] = '';
        foreach ($this->input_attrs as $attr => $value) {
            $this->json['inputAttrs'] .= $attr . '="' . esc_attr($value) . '" ';
        }
        $this->json['desktop'] = array();
        $this->json['tablet'] = array();
        $this->json['mobile'] = array();
        foreach ($this->settings as $setting_key => $setting) {
            $this->json[$setting_key] = array(
                'id' => $setting->id,
                'default' => $setting->default,
                'link' => $this->get_link($setting_key),
                'value' => $this->value($setting_key),
            );
        }

    }
    /**
     * @inheritdoc
     */
    protected function content_template()
    {
        echo '<# if ( data.label ) { #>
			<span class="customize-control-title">
				<span>{{{ data.label }}}</span>

				<ul class="responsive-switchers">
					<li class="desktop">
						<button type="button" class="preview-desktop active" data-device="desktop">
							<i class="dashicons dashicons-desktop"></i>
						</button>
					</li>
					<li class="tablet">
						<button type="button" class="preview-tablet" data-device="tablet">
							<i class="dashicons dashicons-tablet"></i>
						</button>
					</li>
					<li class="mobile">
						<button type="button" class="preview-mobile" data-device="mobile">
							<i class="dashicons dashicons-smartphone"></i>
						</button>
					</li>
				</ul>

			</span>
		<# } #>

		<# if ( data.description ) { #>
			<span class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>

		<# if ( data.desktop ) { #>
			<div class="desktop control-wrap active">
				<div class="oceanwp-slider desktop-slider"></div>
				<div class="oceanwp-slider-input">
					<input {{{ data.inputAttrs }}} type="number" class="slider-input desktop-input" value="{{ data.desktop.value }}" {{{ data.desktop.link }}} />
				</div>
	    	</div>
	    <# } #>

		<# if ( data.tablet ) { #>
			<div class="tablet control-wrap">
				<div class="oceanwp-slider tablet-slider"></div>
				<div class="oceanwp-slider-input">
					<input {{{ data.inputAttrs }}} type="number" class="slider-input tablet-input" value="{{ data.tablet.value }}" {{{ data.tablet.link }}} />
				</div>
	    	</div>
	    <# } #>

		<# if ( data.mobile ) { #>
			<div class="mobile control-wrap">
				<div class="oceanwp-slider mobile-slider"></div>
				<div class="oceanwp-slider-input">
					<input {{{ data.inputAttrs }}} type="number" class="slider-input mobile-input" value="{{ data.mobile.value }}" {{{ data.mobile.link }}} />
				</div>
	    	</div>
	    <# } #>';
    }
}