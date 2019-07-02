<?php
/**
 * Created by IntelliJ IDEA.
 * User: lvis
 * Date: 2019-02-16
 * Time: 19:32
 */

namespace wp;

class PartyMaker extends WpApp
{
    public function __construct()
    {
        PostEvent::i();
        parent::__construct();
        add_action('elementor/controls/controls_registered', [$this,'handleBuilderControlRegistered'], 10, 1);
        add_action('elementor/editor/after_enqueue_styles', [$this, 'enqueueFontelloIcons']);
    }

    function enqueueScriptsTheme()
    {
        parent::enqueueScriptsTheme();
        $this->enqueueFontelloIcons();
        wp_enqueue_style('partymaker', $this->uriToLibs . 'partymaker.css');
    }
    function handleBuilderControlRegistered($controls_registry)
    {
        // Get existing icons
        $iconsDefault = $controls_registry->get_control('icon')->get_settings('options');
        // Append new icons
        $iconsUpdated = array_merge([
            'icon-service1' => 'service-decor',
            'icon-service2' => 'service-floral',
            'icon-service3' => 'service-media',
            'icon-service4' => 'service-invitation',
            'icon-service5' => 'service-entertainment',
            'icon-service6' => 'service-venue',
        ], $iconsDefault);
        // Then we set a new list of icons as the options of the icon control
        $controls_registry->get_control('icon')->set_settings('options', $iconsUpdated);
    }
    function enqueueFontelloIcons()
    {
        wp_enqueue_style('fontello', $this->uriToLibs . 'fonts/fontello/fontello-embedded.css');
    }
}