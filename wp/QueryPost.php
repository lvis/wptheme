<?php
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/5/18
 * Time: 6:10 PM
 */

namespace wp;

use WP_Query;

final class QueryPost
{
    /**
     * @const Parent of current Post
     */
    const ID = 'p';
    /**
     * @const Parent of current Post
     */
    const PARENT = 'post_parent';
    /**
     * @const Type of current Post
     */
    const TYPE = 'post_type';
    /**
     * @const Title of current Post
     */
    const TITLE = 'post_title';
    /**
     * @const Status of current Post
     */
    const STATUS = 'post_status';
    /**
     * @const Query post metadata
     */
    const QUERY = 'meta_query';
    /**
     * @const Query argument that specify to  divide returned post per page
     */
    const PAGED = 'paged';
    /**
     * @const Query argument that specify number returned for one page
     */
    const PER_PAGE = 'posts_per_page';
    /**
     * @const
     */
    const NUMBER_POSTS = 'numberposts';
    /**
     * @const
     */
    const HAS_AUTHORS = 'author__in';
    /**
     * @const
     */
    const AUTHOR = 'author';
    /**
     * @const
     */
    const NOT_IN = 'post__not_in';
    /**
     * @const
     */
    const IN = 'post__in';
    /**
     * @const
     */
    const PARENT_NOT_IN = 'post_parent__not_in';
    /**
     * @const
     */
    const ORDER_BY = 'orderby';
    /**
     * @const
     */
    const ORDER = 'order';
    /**
     * @const You can only use 'ids' or 'id=>parent' for the parameter fields
     */
    const FIELDS = 'fields';
    /**
     * @const Key name of Post Meta
     */
    const META_QUERY = 'meta_query';
    /**
     * @const Key name of Post Meta
     */
    const META_KEY = 'meta_key';
    /**
     * @const
     */
    const META_INPUT = 'meta_input';

    private static $currentQuery;

    static function getCurrentQuery($args, $handler)
    {
        return self::$currentQuery;
    }

    static function query($args, $handler, &$results)
    {
        $currentQuery = new WP_Query($args);
        $results = $currentQuery->post_count;
        $result = "";
        if (function_exists($handler)) {
            while ($currentQuery->have_posts()) {
                $currentQuery->the_post();
                $handler();
            }
        }

        return $result;
    }

}