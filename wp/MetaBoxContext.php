<?php namespace wp;
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Part of the page where the meta box is displayed (normal, advanced or side).
 * Optional. Default: normal.
 * @see https://metabox.io/docs/registering-meta-boxes/
 */
final class MetaBoxContext
{
    const NORMAL = 'normal';
    const SIDE = 'side';
    const ADVANCED = 'advanced';
}