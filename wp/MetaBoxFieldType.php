<?php
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/6/18
 * Time: 10:08 AM
 */

namespace wp;


final class MetaBoxFieldType
{
    /**
     * @const Output custom HTML content
     * @url https://docs.metabox.io/fields/custom-html/
     */
    const CUSTOM_HTML = 'custom_html';
    /**
     * @const Input field with hidden type
     */
    const HIDDEN = 'hidden';
    /**
     * @const Similar to file but used for images only. Allow drag and drop to reorder images. Inherits file.
     */
    const IMAGE = 'image';
    /**
     * @const Similar to file but used for images only. Allow drag and drop to reorder images. Inherits file.
     */
    const IMAGE_UPLOAD = 'image_upload';
    /**
     * @const Similar to file_advanced, but used for images only. Inherits file_advanced.
     */
    const IMAGE_ADVANCED = 'image_advanced';
    /**
     * @const Video field
     */
    const VIDEO = 'video';
    /**
     * @const Select taxonomies. Has various options to display as check box list, select dropdown
     * @link https://metabox.io/docs/define-fields/#section-taxonomy
     */
    const TAXONOMY = 'taxonomy';
    /**
     * @const This field is exactly the same as taxonomy field,
     * but it saves term IDs in post meta as a comma separated string. It does NOT set post terms. After save only
     * way to query this property is by get_post_meta
     * @link https://metabox.io/docs/define-fields/#section-taxonomy
     */
    const TAXONOMY_ADVANCED = 'taxonomy_advanced';
    /**
     * @const Select dropdown
     */
    const SELECT = 'select';
    /**
     * @const Select dropdown with Autocomplete
     * @link https://docs.metabox.io/fields/autocomplete/
     */
    const AUTOCOMPLETE = 'autocomplete';
    /**
     * @const Select post from select dropdown. Support custom post types.
     * Inherits options from select or select_advanced based on field_type parameter
     */
    const POST = 'post';
    /**
     * @const Text field
     */
    const TEXT = 'text';
    /**
     * @const Textarea field
     */
    const TEXTAREA = 'textarea';
    /**
     * @const Textarea WYSIWYG field https://docs.metabox.io/fields/wysiwyg/
     */
    const WYSIWYG = 'wysiwyg';
    /**
     * @const Input for videos, audios from Youtube, Vimeo and all supported sites by WordPress. It has a preview feature
     */
    const EMBED = 'oembed';
    /**
     * @const Input for numbers which uses new HTML5 input type="number"
     */
    const NUMBER = 'number';
    /**
     * @const
     */
    const CHECKBOX = 'checkbox';
    /**
     * @const The switch field shows a on/off switch for you to enable/disable something
     * @url https://docs.metabox.io/fields/switch/
     */
    const SWITCH = 'switch';
    /**
     * @const
     */
    const GROUP = 'group';
    /**
     * @const Google maps field
     */
    const MAP = 'map';
}