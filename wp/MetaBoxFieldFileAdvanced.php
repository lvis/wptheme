<?php
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/6/18
 * Time: 10:13 AM
 */

namespace wp;


class MetaBoxFieldFileAdvanced extends MetaBoxFieldFile
{
    /**
     * @const Mime type of files which we want to show in Media Library.
     * Note: this is a filter to list items in Media popup, it doesn’t restrict file types when upload.
     */
    const MIME_TYPE = 'mime_type';
    /**
     * @const Whether to show the status of number of uploaded files when max_file_uploads is defined (xx/xx files uploaded).
     * Optional. Default true.
     */
    const MAX_STATUS = 'max_status';
}