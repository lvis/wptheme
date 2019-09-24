<?php

namespace wp;
class CustomizerControlTypography extends CustomizerControl
{
    /**
     * Returns the available font families.
     * @since  1.0.0
     * @access public
     * @return array
     */
    public function get_font_families()
    {
        $fonts = [
            '' => __('Default'),
            'Arial, Helvetica, sans-serif' => 'Arial, Helvetica, sans-serif',
            'Arial Black, Gadget, sans-serif' => 'Arial Black, Gadget, sans-serif',
            'Bookman Old Style, serif' => 'Bookman Old Style, serif',
            'Comic Sans MS, cursive' => 'Comic Sans MS, cursive',
            'Courier, monospace' => 'Courier, monospace',
            'Georgia, serif' => 'Georgia, serif',
            'Garamond, serif' => 'Garamond, serif',
            'Impact, Charcoal, sans-serif' => 'Impact, Charcoal, sans-serif',
            'Lucida Console, Monaco, monospace' => 'Lucida Console, Monaco, monospace',
            'Lucida Sans Unicode, Lucida Grande, sans-serif' => 'Lucida Sans Unicode, Lucida Grande, sans-serif',
            'MS Sans Serif, Geneva, sans-serif' => 'MS Sans Serif, Geneva, sans-serif',
            'MS Serif, New York, sans-serif' => 'MS Serif, New York, sans-serif',
            'Palatino Linotype, Book Antiqua, Palatino, serif' => 'Palatino Linotype, Book Antiqua, Palatino, serif',
            'Tahoma, Geneva, sans-serif' => 'Tahoma, Geneva, sans-serif',
            'Times New Roman, Times, serif' => 'Times New Roman, Times, serif',
            'Trebuchet MS, Helvetica, sans-serif' => 'Trebuchet MS, Helvetica, sans-serif',
            'Verdana, Geneva, sans-serif' => 'Verdana, Geneva, sans-serif',
            'Paratina Linotype' => 'Paratina Linotype',
            'Trebuchet MS' => 'Trebuchet MS',
        ];
        $fonts = array_merge($fonts, WpApp::getGoogleFonts());
        return $fonts;
    }
    /**
     * Array
     * @since  1.0.0
     * @access public
     * @var    string
     */
    public $l10n = [];
    /**
     * @inheritdoc
     */
    public function __construct($manager, $id, $args = array())
    {

        // Let the parent class do its thing.
        parent::__construct($manager, $id, $args);
        // Make sure we have labels.
        $this->l10n = wp_parse_args(
            $this->l10n,
            [
                'family' => __('Font Family'),
                'size' => __('Font Size'),
                'style' => __('Font Style'),
                'line_height' => __('Line Height'),
                'weight' => __('Font Weight'),
                'spacing' => __('Letter Spacing'),
                'transform' => __('Text Transform'),
            ]
        );
    }
    /**
     * @inheritdoc
     */
    public $type = 'oceanwp-typo';
    /**
     * @inheritdoc
     */
    public function enqueue()
    {
        wp_enqueue_script('oceanwp-select2', self::$uriToDirCustomizer . 'select2.js',
            ['jquery'], false, true);
        wp_enqueue_script('oceanwp-typo-js', self::$uriToDirCustomizer . 'typo.js',
            ['jquery', 'customize-base', 'select2'], false, true);
        wp_enqueue_style('select2', self::$uriToDirCustomizer . 'select2.css');
        wp_enqueue_style('oceanwp-typo', self::$uriToDirCustomizer . 'typo.css');
    }
    /**
     * @inheritdoc
     */
    public function to_json()
    {
        parent::to_json();
        // Loop through each of the settings and set up the data for it.
        foreach ($this->settings as $setting_key => $setting_id) {
            $this->json[$setting_key] = array(
                'link' => $this->get_link($setting_key),
                'value' => $this->value($setting_key),
                'label' => isset($this->l10n[$setting_key]) ? $this->l10n[$setting_key] : ''
            );
            if ('family' === $setting_key) {
                $this->json[$setting_key]['choices'] = $this->get_font_families();
            } elseif ('weight' === $setting_key) {
                $this->json[$setting_key]['choices'] = [
                    '' => esc_html__('Default'),
                    '100' => __('Thin: 100', 'oceanwp'),
                    '200' => __('Light: 200', 'oceanwp'),
                    '300' => __('Book: 300', 'oceanwp'),
                    '400' => __('Normal: 400', 'oceanwp'),
                    '500' => __('Medium: 500', 'oceanwp'),
                    '600' => __('Semibold: 600', 'oceanwp'),
                    '700' => __('Bold: 700', 'oceanwp'),
                    '800' => __('Extra Bold: 800', 'oceanwp'),
                    '900' => __('Black: 900', 'oceanwp'),
                ];
            } elseif ('style' === $setting_key) {
                $this->json[$setting_key]['choices'] = [
                    '' => __('Default'),
                    'normal' => __('Normal'),
                    'italic' => __('Italic'),
                ];
            } elseif ('transform' === $setting_key) {
                $this->json[$setting_key]['choices'] = [
                    '' => __('Default'),
                    'capitalize' => __('Capitalize', 'oceanwp'),
                    'lowercase' => __('Lowercase', 'oceanwp'),
                    'uppercase' => __('Uppercase', 'oceanwp')
                ];
            }
        }

    }
    /**
     * @inheritdoc
     */
    protected function content_template()
    {
        echo '<# if ( data.label ) { #>
        <span class="customize-control-title">{{ data.label }}</span>
        <# } #>
        <# if ( data.description ) { #>
        <span class="description customize-control-description">{{{ data.description }}}</span>
        <# } #>
        <ul class="oceanwp-typo-wrap">
            <# if ( data.family && data.family.choices ) { #>
            <li class="typography-font-family">
                <# if ( data.family.label ) { #>
                <span class="label">{{ data.family.label }}</span>
                <# } #>
                <select {{{ data.family.link }}}>
                    <# _.each( data.family.choices, function( label, choice ) { #>
                    <option value="{{ choice }}"
                    <# if ( choice === data.family.value ) { #> selected="selected" <# } #>>{{ label }}</option>
                    <# } ) #>
                </select>
            </li>
            <# } #>
            <# if ( data.size ) { #>
            <li class="typography-font-size">
                <# if ( data.size.label ) { #>
                <span class="label">{{ data.size.label }}</span>
                <# } #>
                <input type="text" name="{{ data.size.name }}" value="{{ data.size.value }}"
                       placeholder="px - em - rem">
            </li>
            <# } #>
            <# if ( data.style && data.style.choices ) { #>
            <li class="typography-font-style">
                <# if ( data.style.label ) { #>
                <span class="label">{{ data.style.label }}</span>
                <# } #>
                <select {{{ data.style.link }}}>
                    <# _.each( data.style.choices, function( label, choice ) { #>
                    <option value="{{ choice }}"
                    <# if ( choice === data.style.value ) { #> selected="selected" <# } #>>{{ label }}</option>
                    <# } ) #>
                </select>
            </li>
            <# } #>
            <# if ( data.line_height ) { #>
            <li class="typography-line-height">
                <# if ( data.line_height.label ) { #>
                <span class="label">{{ data.line_height.label }}</span>
                <# } #>
                <input type="text" name="{{ data.line_height.name }}" value="{{ data.line_height.value }}"
                       placeholder="px - em - rem">
            </li>
            <# } #>
            <# if ( data.weight && data.weight.choices ) { #>
            <li class="typography-font-weight">
                <# if ( data.weight.label ) { #>
                <span class="label">{{ data.weight.label }}</span>
                <# } #>
                <select {{{ data.weight.link }}}>
                    <# _.each( data.weight.choices, function( label, choice ) { #>
                    <option value="{{ choice }}"
                    <# if ( choice === data.weight.value ) { #> selected="selected" <# } #>>{{ label }}</option>
                    <# } ) #>
                </select>
            </li>
            <# } #>
            <# if ( data.spacing ) { #>
            <li class="typography-letter-spacing">
                <# if ( data.spacing.label ) { #>
                <span class="label">{{ data.spacing.label }}</span>
                <# } #>
                <input type="text" name="{{ data.spacing.name }}" value="{{ data.spacing.value }}"
                       placeholder="px - em - rem">
            </li>
            <# } #>
            <# if ( data.transform && data.transform.choices ) { #>
            <li class="typography-text-transform">
                <# if ( data.transform.label ) { #>
                <span class="label">{{ data.transform.label }}</span>
                <# } #>
                <select {{{ data.transform.link }}}>
                    <# _.each( data.transform.choices, function( label, choice ) { #>
                    <option value="{{ choice }}"
                    <# if ( choice === data.transform.value ) { #> selected="selected" <# } #>>{{ label }}</option>
                    <# } ) #>
                </select>
            </li>
            <# } #>
        </ul>';
    }
}