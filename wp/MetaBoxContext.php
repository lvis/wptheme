<?php
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/6/18
 * Time: 10:09 AM
 */

namespace wp;


/**
 * Part of the page where the meta box is displayed (normal, advanced or side).
 * Optional. Default: normal.
 * @url https://metabox.io/docs/registering-meta-boxes/
 */
final class MetaBoxContext
{
    /**
     * @const
     */
    const NORMAL = 'normal';
    /**
     * @const
     */
    const SIDE = 'side';
    /**
     * @const
     */
    const ADVANCED = 'advanced';
}