<?php namespace wp;
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * @see https://docs.metabox.io/fields/switch/
 */
class MetaBoxFieldSwitch extends MetaBoxField {
    /**
     * @const The switch style. rounded (default) or square. Optional.
     */
    const STYLE = 'style';
    /**
     * @const The label for “On” status. Can be any HTML. You can set the text “Enable” or
     * a check icon like <i class="dashicons dashicons-yes"></i>.
     * When this setting is set to empty string, it displays a style like iOS switch. Optional.
     */
    const LABEL_ON = 'on_label';
    /**
     * @const Similar to the on_label but for “Off” status.
     */
    const LABEL_OFF = 'off_label';
}