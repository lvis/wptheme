<?php
/**
 * Author: Vitalie Lupu
 * Date: 2019-02-16
 * Time: 19:32
 */

namespace wp;

class Demolition extends WpApp
{
    public function __construct()
    {
        parent::__construct();
    }

    function enqueueScriptsTheme()
    {
        parent::enqueueScriptsTheme();
        wp_enqueue_style('demolition', $this->uriToLibs . 'demolition.css');
    }
}