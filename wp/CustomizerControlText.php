<?php

namespace wp;
class CustomizerControlText extends CustomizerControl
{
    /**
     * @inheritdoc
     */
    public $type = 'oceanwp-text';
    /**
     * @inheritdoc
     */
    public function enqueue()
    {
        wp_enqueue_script('oceanwp-text-js', self::$uriToDirCustomizer . 'text.js',
            ['jquery', 'customize-base'], false, true);
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
            <input type="text" value="{{ data.desktop.value }}" placeholder="px - em - rem" {{{ data.desktop.link }}}>
        </div>
        <# } #>
        <# if ( data.tablet ) { #>
        <div class="tablet control-wrap">
            <input type="text" value="{{ data.tablet.value }}" placeholder="px - em - rem" {{{ data.tablet.link }}}>
        </div>
        <# } #>
        <# if ( data.mobile ) { #>
        <div class="mobile control-wrap">
            <input type="text" value="{{ data.mobile.value }}" placeholder="px - em - rem" {{{ data.mobile.link }}}>
        </div>
        <# } #>';
    }
}