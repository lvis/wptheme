<?php
/**
 * Author: Vitali Lupu <vitaliix@gmail.com>
 * Date: 3/5/18
 * Time: 6:32 PM
 */

namespace wp;
/**
 * @url https://codex.wordpress.org/Function_Reference/get_users
 */
final class QueryUsers
{
    /**
     * @const
     */
    const FIELDS = 'fields';
    /**
     * @const Limit the returned users to the role specified https://codex.wordpress.org/Roles_and_Capabilities#User_Levels
     */
    const ROLE = 'role';
    /**
     * @const
     */
    const ROLE_IN = 'role__in';
    /**
     * @const ASC (ascending) or DESC (descending).
     */
    const ORDER = 'order';
    /**
     * @const Sort by 'ID', 'login', 'nicename', 'email', 'url', 'registered', 'display_name', 'post_count', 'include',
     * or 'meta_value' (query must also contain a 'meta_key' - see WP_User_Query
     */
    const ORDER_BY = 'orderby';
    /**
     * @const Limit the total number of users returned.
     */
    const NUMBER = 'number';
}