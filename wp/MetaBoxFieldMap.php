<?php
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/6/18
 * Time: 10:10 AM
 */

namespace wp;


final class MetaBoxFieldMap extends MetaBoxField
{
    /**
     * @const The ID of the text field above. Required.
     */
    const ADDRESS_FIELD = 'address_field';
    //std The initial position of the map and the marker in format ’latitude,longitude[,zoom]’ (zoom is optional). Optional.
    /**
     * @const
     */
    const API_KEY = 'api_key';
    /**
     * @const The region code, specified as a ccTLD (country code top-level domain). See: https://developers.google.com/maps/documentation/geocoding/intro#RegionCodes
     * @link https://github.com/rilwis/meta-box/blob/master/demo/map.php
     */
    const REGION = 'region';
}