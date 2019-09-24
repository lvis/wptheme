<?php

namespace wp;

class CustomizerControlDimensions extends CustomizerControl
{
    /**
     * @inheritdoc
     */
    public $type = 'oceanwp-dimensions';
    /**
     * @inheritdoc
     */
    protected function l10n($id = false)
    {
        $this->translations = [
            'desktop_top' => __('Top'),
            'desktop_right' => __('Right'),
            'desktop_bottom' => __('Bottom'),
            'desktop_left' => __('Left'),
            'tablet_top' => __('Top'),
            'tablet_right' => __('Right'),
            'tablet_bottom' => __('Bottom'),
            'tablet_left' => __('Left'),
            'mobile_top' => __('Top'),
            'mobile_right' => __('Right'),
            'mobile_bottom' => __('Bottom'),
            'mobile_left' => __('Left'),
        ];
        return parent::l10n($id);
    }
    /**
     * @inheritdoc
     */
    public function enqueue()
    {
        wp_enqueue_script('oceanwp-dimensions', self::$uriToDirCustomizer . 'dimensions.js',
            ['jquery', 'customize-base'], false, true);
        wp_localize_script('oceanwp-dimensions', 'oceanwpL10n', $this->l10n());
        wp_enqueue_style('oceanwp-dimensions', self::$uriToDirCustomizer . 'dimensions.css');
    }
    /**
     * @inheritdoc
     */
    protected function render()
    {
        $id = esc_attr('customize-control-' . str_replace(array('[', ']'), array('-', ''), $this->id));
        $class = esc_attr('customize-control has-switchers customize-control-' . $this->type);
        ob_start();
        $this->render_content();
        $content = ob_get_clean();
        echo "<li id='{$id}' class='{$class}'>{$content}</li>";
    }
    /**
     * @inheritdoc
     */
    public function to_json()
    {
        parent::to_json();
        $this->json['id'] = $this->id;
        $this->json['l10n'] = $this->l10n();
        $this->json['title'] = esc_html__('Link values together', 'oceanwp');
        $this->json['inputAttrs'] = '';
        foreach ($this->input_attrs as $attr => $value) {
            $this->json['inputAttrs'] .= $attr . '="' . esc_attr($value) . '" ';
        }
        $this->json['desktop'] = array();
        $this->json['tablet'] = array();
        $this->json['mobile'] = array();
        foreach ($this->settings as $setting_key => $setting) {

            list($_key) = explode('_', $setting_key);
            $this->json[$_key][$setting_key] = array(
                'id' => $setting->id,
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

        <ul class="desktop control-wrap active">
            <# _.each( data.desktop, function( args, key ) { #>
            <li class="dimension-wrap {{ key }}">
                <input {{{ data.inputAttrs }}} type="number" class="dimension-{{ key }}" {{{ args.link }}}
                       value="{{{ args.value }}}"/>
                <span class="dimension-label">{{ data.l10n[ key ] }}</span>
            </li>
            <# } ); #>

            <li class="dimension-wrap">
                <div class="link-dimensions">
                    <span class="dashicons dashicons-admin-links oceanwp-linked" data-element="{{ data.id }}"
                          title="{{ data.title }}"></span>
                    <span class="dashicons dashicons-editor-unlink oceanwp-unlinked" data-element="{{ data.id }}"
                          title="{{ data.title }}"></span>
                </div>
            </li>
        </ul>

        <ul class="tablet control-wrap">
            <# _.each( data.tablet, function( args, key ) { #>
            <li class="dimension-wrap {{ key }}">
                <input {{{ data.inputAttrs }}} type="number" class="dimension-{{ key }}" {{{ args.link }}}
                       value="{{{ args.value }}}"/>
                <span class="dimension-label">{{ data.l10n[ key ] }}</span>
            </li>
            <# } ); #>

            <li class="dimension-wrap">
                <div class="link-dimensions">
                    <span class="dashicons dashicons-admin-links oceanwp-linked" data-element="{{ data.id }}_tablet"
                          title="{{ data.title }}"></span>
                    <span class="dashicons dashicons-editor-unlink oceanwp-unlinked" data-element="{{ data.id }}_tablet"
                          title="{{ data.title }}"></span>
                </div>
            </li>
        </ul>

        <ul class="mobile control-wrap">
            <# _.each( data.mobile, function( args, key ) { #>
            <li class="dimension-wrap {{ key }}">
                <input {{{ data.inputAttrs }}} type="number" class="dimension-{{ key }}" {{{ args.link }}}
                       value="{{{ args.value }}}"/>
                <span class="dimension-label">{{ data.l10n[ key ] }}</span>
            </li>
            <# } ); #>

            <li class="dimension-wrap">
                <div class="link-dimensions">
                    <span class="dashicons dashicons-admin-links oceanwp-linked" data-element="{{ data.id }}_mobile"
                          title="{{ data.title }}"></span>
                    <span class="dashicons dashicons-editor-unlink oceanwp-unlinked" data-element="{{ data.id }}_mobile"
                          title="{{ data.title }}"></span>
                </div>
            </li>
        </ul>';
    }
}