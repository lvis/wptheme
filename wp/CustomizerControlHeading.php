<?php

namespace wp;

class CustomizerControlHeading extends CustomizerControl
{
    /**
     * @inheritdoc
     */
    public $type = 'heading';
    /**
     * @inheritdoc
     */
    public function enqueue()
    {
        wp_enqueue_style('heading', self::$uriToDirCustomizer . 'heading.css', [], '1.2');
    }
    /**
     * @inheritdoc
     */
    /*public function render_content()
    {
        $content = "";
        if (!empty($this->label)) {
            $contentLabel = esc_html($this->label);
            $content .= "<h2 class='customize-control-title'>{$contentLabel}</h2>";
        }
        if (!empty($this->description)) {
            $contentDescription = esc_html($this->description);
            $content .= "<span class='description customize-control-description'>{$contentDescription}</span>";
        }
        echo "<label>{$content}<hr></label>";
    }*/
    /**
     * @inheritdoc
     */
    protected function content_template()
    {
        echo '<h2 class="title">{{{ data.label }}}</h2>
        <span class="description">{{{ data.description }}}</span><hr>';
    }
}