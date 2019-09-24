<?php
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/5/18
 * Time: 8:29 PM
 */

namespace wp;
final class MetaPartner
{
    const ID_BOX = 'partnerIdBox';
    const URL = "partnerUrl";

    static function getPartnerUrl()
    {
        return esc_url(get_post_meta(get_the_ID(), MetaPartner::URL, true));
    }
}