<?php

namespace wp;

class CustomizerControlDropdownPages extends CustomizerControl
{
    /**
     * @inheritdoc
     */
    public $type = 'oceanwp-dropdown-pages';
    /**
     * @inheritdoc
     */
    public function enqueue()
    {
        wp_enqueue_script('oceanwp-dropdown-pages', self::$uriToDirCustomizer . 'dropdown-pages.js',
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
        $dropdown = wp_dropdown_pages(
            array(
                'name' => '_customize-dropdown-pages-' . esc_attr($this->id),
                'echo' => 0,
                'show_option_none' => '&mdash; ' . esc_html__('Select', 'oceanwp') . ' &mdash;',
                'option_none_value' => '',
                'selected' => esc_attr($this->value()),
            )
        );
        // Hackily add in the data link parameter.
        $dropdown = str_replace('<select', '<select ' . $this->get_link(), $dropdown);
        $this->json['dropdown'] = $dropdown;
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
            <# if ( data.description ) { #>
            <span class="description customize-control-description">{{{ data.description }}}</span>
            <# } #>
            <div class="customize-control-content">{{{ data.dropdown }}}</div>
        </label>';
    }
}