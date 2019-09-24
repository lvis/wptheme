<?php
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/5/18
 * Time: 6:13 PM
 */

namespace wp;
final class QueryPostMeta
{
    /**
     * @const Key name of Post Meta
     */
    const DEFINITION = 'meta_query';
    /**
     * @const Key name of Post Meta
     */
    const KEY = 'meta_key';
    /**
     * @const Value of Post Meta
     */
    const VALUE = 'meta_value';
    /**
     * @const Sign of operation used to compare Post Meta Values Ex: =, !=, >, <, >=, <=,
     */
    const COMPARISON = 'meta_compare';
    /**
     * @const
     */
    const COMPARE = 'compare';
    /**
     * @const
     */
    const RELATION = 'relation';
    /**
     * @const
     */
    const TYPE = 'type';
}