<?php
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/5/18
 * Time: 6:46 PM
 */

namespace wp;
final class WPostStatus
{
    /**
     * @const
     */
    const PUBLISH = 'publish';
    /**
     * @const
     */
    const PENDING = 'pending';
    /**
     * @const
     */
    const DRAFT = 'draft';
    /**
     * @const
     */
    const AUTO_DRAFT = 'auto-draft';
    /**
     * @const
     */
    const PERSONAL = 'private';
    /**
     * @const
     */
    const TRASH = 'trash';
    /**
     * @const
     */
    const INHERIT = 'inherit';
}