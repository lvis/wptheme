<?php
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/6/18
 * Time: 10:13 AM
 */

namespace wp;


class MetaBoxFieldFile extends MetaBoxField
{
    /**
     * @const Max number of uploaded files. Optional.
     */
    const MAX_UPLOADS = 'max_file_uploads';
    /**
     * @const Whether or not delete the files from Media Library when deleting them from post meta (true or false)?
     * Optional. Default false.
     */
    const FORCE_DELETE = 'force_delete';
}