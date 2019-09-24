<?php

namespace wp;

class CustomizerControlRadioImage extends CustomizerControl
{
    /**
     * @inheritdoc
     */
    public $type = 'oceanwp-radio-image';
    /**
     * @inheritdoc
     */
    public function enqueue()
    {
        wp_enqueue_script('oceanwp-radio-image', self::$uriToDirCustomizer . 'radio-image.js',
            array('jquery', 'customize-base'), false, true);
        wp_localize_script('oceanwp-radio-image', 'oceanwpL10n', $this->l10n());
        wp_enqueue_style('oceanwp-radio-image', self::$uriToDirCustomizer . 'radio-image.css');
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
        $this->json['l10n'] = $this->l10n();
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
        <div id="input_{{ data.id }}" class="image">
            <# for ( key in data.choices ) { #>
            <input {{{ data.inputAttrs }}} class="image-select" type="radio" value="{{ key }}"
                   name="_customize-radio-{{ data.id }}" id="{{ data.id }}{{ key }}" {{{ data.link }}}<# if ( data.value
            === key ) { #> checked="checked"<# } #>>
            <label for="{{ data.id }}{{ key }}" title="{{ data.l10n[ key ] }}">
                <img src="{{ data.choices[ key ] }}">
                <span class="image-clickable"></span>
            </label>
            </input>
            <# } #>
        </div>';
    }
    /**
     * @inheritdoc
     */
    protected function l10n($id = false)
    {
        $this->translations = [
            'right-sidebar' => __('Sidebar') . ': ' . __('Right'),
            'left-sidebar' => __('Sidebar') . ': ' . __('Left'),
            'full-width' => __('Full width'),
            'full-screen' => __('Fullscreen'),
        ];
        return parent::l10n($id);
    }
}