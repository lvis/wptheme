<?php namespace wp;
class CustomizerControlColorPicker extends CustomizerControl {
    /**
     * Add support for palettes to be passed in.
     * Supported palette values are true, false, or an array of RGBa and Hex colors.
     */
    public $palette;
    /**
     * @var bool Add support for showing the opacity value on the slider handle.
     */
    public $show_opacity;
    /**
     * @inheritdoc
     */
    public $type = 'alpha-color';

    /**
     * @inheritdoc
     */
    public function enqueue() {
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_script('oceanwp-color', self::$uriToDirCustomizer . 'color.js', ['jquery',
                                                                                    'customize-base',
                                                                                    'wp-color-picker'], false, true);
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_style('oceanwp-color', self::$uriToDirCustomizer . 'color.css', ['wp-color-picker']);
        wp_localize_script('oceanwp-color', 'oceanwpLocalize', ['colorPalettes' => ['#000000',
                                                                                    '#ffffff',
                                                                                    '#dd3333',
                                                                                    '#dd9933',
                                                                                    '#eeee22',
                                                                                    '#81d742',
                                                                                    '#1e73be',
                                                                                    '#8224e3']]);
    }

    /**
     * @inheritdoc
     */
    public function to_json() {
        parent::to_json();
        $this->json['default'] = $this->setting->default;
        $this->json['show_opacity'] = (false === $this->show_opacity || 'false' === $this->show_opacity) ? 'false' : 'true';
        $this->json['value'] = $this->value();
        $this->json['link'] = $this->get_link();
        $this->json['id'] = $this->id;
    }

    /**
     * @inheritdoc
     */
    protected function content_template() {
        echo '<label>
            <# if ( data.label ) { #>
            <span class="customize-control-title">{{{ data.label }}}</span>
            <# } #>
            <# if ( data.description ) { #>
            <span class="description customize-control-description">{{{ data.description }}}</span>
            <# } #>
            <div>
                <input class="alpha-color-control"
                       type="text"
                       value="{{ data.value }}"
                       data-show-opacity="{{ data.show_opacity }}"
                       data-default-color="{{ data.default }}" {{{ data.link }}}>
            </div>
        </label>';
    }
}