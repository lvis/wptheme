<?php

/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/5/18
 * Time: 6:32 PM
 */

namespace wp;

abstract class WidgetDialogBase extends Widget
{
    const INLINE = "inline";
    const DIALOG = "dialog";
    const FORM_TYPE = "formType";
    const MODAL_TOGGLE_NAME = "modalToggleName";
    const MODAL_TOGGLE_ICON = "modalToggleIcon";
    protected $iconModalToggle = "";
    protected $nameModalToggle = "";
    protected $modalDialogId = "";

    function __construct($name, $description = "")
    {
        parent::__construct($name, $description);
        $this->modalDialogId = uniqid("formModal");
    }

    function initFields()
    {
        parent::initFields();
        $fieldName = __("Form Type");
        $fieldOptions = [
            self::INLINE => __("Inline"),
            self::DIALOG => __("Dialog"),
        ];
        $field = new WidgetField(WidgetField::SELECT, self::FORM_TYPE, $fieldName, $fieldOptions, self::DIALOG);
        $this->addField($field);

        $fieldName = __("Modal Toggle Name", WpApp::TEXT_DOMAIN);
        $field = new WidgetField(WidgetField::TEXT, self::MODAL_TOGGLE_NAME, $fieldName, [], $this->nameModalToggle);
        $this->addField($field);

        $fieldName = __("Modal Toggle Icon", WpApp::TEXT_DOMAIN);
        $field = new WidgetField(WidgetField::TEXT, self::MODAL_TOGGLE_ICON, $fieldName, [], $this->iconModalToggle);
        $this->addField($field);
    }

    function enqueueScriptsTheme()
    {
        $uriToDirLibs = UtilsWp::getUriToLibsDir();
        wp_deregister_script('jquery-form');
        wp_enqueue_script('jquery-form', includes_url('js/jquery/jquery.form.js'), ['jquery'],
            false, true);
        parent::enqueueScriptsTheme();
    }


    function widget($args, $instance)
    {
        $userFormType = self::getInstanceValue($instance, self::FORM_TYPE, $this);
        if ($userFormType == self::DIALOG) {
            $toggleName = __(self::getInstanceValue($instance, self::MODAL_TOGGLE_NAME, $this));
            $toggleIcon = self::getInstanceValue($instance, self::MODAL_TOGGLE_ICON, $this);
            $dialogId = $this->modalDialogId;
            $dialogContent = $args[WPSidebar::CONTENT];
            $args[WPSidebar::CONTENT] = "<a href='#{$dialogId}' id='a{$dialogId}'>
            <i class='fa {$toggleIcon}'></i> <span>{$toggleName}</span></a>
            <div id='{$dialogId}' class='modal fade' role='dialog' tabindex='-1'>
            <div class='modal-dialog' role='document'>
            <div class='modal-content'><a href='#a{$dialogId}' class='button modal-close'>×</a>{$dialogContent}</div>
            <a class='modal-backdrop' href='#a{$dialogId}'></a></div></a>";
        }
        parent::widget($args, $instance);
    }
}