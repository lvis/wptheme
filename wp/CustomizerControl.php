<?php namespace wp;

use WP_Customize_Control;
use WP_Customize_Manager;

class CustomizerControl extends WP_Customize_Control {
    static $uriToDirCustomizer = '';
    static $registeredClasses = [];
    protected $translations = [];

    /**
     * @inheritdoc
     */
    public function __construct(WP_Customize_Manager $manager, $id, $args = []) {
        parent::__construct($manager, $id, $args);
        if (empty(self::$registeredClasses[static::class])) {
            self::$registeredClasses[static::class] = static::class;
            $manager->register_control_type(static::class);
        }
        if (empty(self::$uriToDirCustomizer)) {
            self::$uriToDirCustomizer = UtilsWp::getUriToLibsDir() . '/customizer/';
        }
    }

    /**
     * Returns an array of translation strings.
     *
     * @param string|false $id The string-ID.
     *
     * @return array
     */
    protected function l10n($id = false) {
        if (false === $id) {
            return $this->translations;
        } else {
            return $this->translations[$id];
        }
    }
}