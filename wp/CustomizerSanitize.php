<?php
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/5/18
 * Time: 8:42 PM
 */

namespace wp;
final class CustomizerSanitize
{
    /**
     * @const
     */
    const HEX_COLOR = 'sanitize_hex_color';
    /**
     * @const
     */
    const TEXT = 'sanitize_text_field';
    /**
     * @const
     */
    const EMAIL = 'sanitize_email';
    /**
     * @const
     */
    const KEY = 'sanitize_key';
    /**
     * @const
     */
    const URL = 'esc_url_raw';

}