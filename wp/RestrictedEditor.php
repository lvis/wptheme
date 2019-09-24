<?php namespace wp;
/** Author: Vitali Lupu <vitaliix@gmail.com> */

use WP_User;

/**
 * Prevent editor from deleting, editing, or creating an administrator
 * only needed if the editor was given right to edit users
 */
final class RestrictedEditor {
    protected static $instances;

    protected function __construct() {
        add_action(WPActions::INIT, [&$this, 'isa_editor_manage_users']);
        add_action(WPActions::PRE_USER_QUERY, [&$this, 'isa_pre_user_query']);
        add_filter('editable_roles', [&$this, 'editable_roles']);
        add_filter('map_meta_cap', [&$this, 'map_meta_cap'], 10, 4);
        add_filter('manage_users_columns', [&$this, 'remove_users_columns']);
        add_filter('views_users', [&$this, 'remove_users_views']);
    }

    final public static function i() {
        if (!isset(static::$instances)) {
            static::$instances = new static();
        }
        return static::$instances;
    }

    //Docs: https://www.role-editor.com/remove-column-from-wordpress-users-list/
    function remove_users_columns($column_headers) {
        $user = wp_get_current_user();
        if ($user->ID != 1) {
            unset($column_headers['role']);
            unset($column_headers['posts']);
        }
        return $column_headers;
    }

    function remove_users_views($column_headers) {
        $user = wp_get_current_user();
        if ($user->ID != 1) {
            unset($column_headers);
        }
    }

    // Hide all administrators from user list.
    function isa_pre_user_query($user_search) {
        if (!current_user_can('manage_options')) {
            global $wpdb;
            $user_search->query_where = str_replace('WHERE 1=1', "WHERE 1=1 AND {$wpdb->users}.ID IN (
                    SELECT {$wpdb->usermeta}.user_id FROM $wpdb->usermeta 
                    WHERE {$wpdb->usermeta}.meta_key = '{$wpdb->prefix}capabilities'
                    AND {$wpdb->usermeta}.meta_value NOT LIKE '%administrator%')", $user_search->query_where);
        }
    }

    /*
     * Let Editors manage users, and run this only once.
     */
    function isa_editor_manage_users() {
        if (get_option('isa_add_cap_editor_once') != 'done') {
            // let editor manage users
            $edit_editor = get_role('editor'); // Get the user role
            $edit_editor->add_cap('edit_users');
            $edit_editor->add_cap('list_users');
            $edit_editor->add_cap('promote_users');
            $edit_editor->add_cap('create_users');
            $edit_editor->add_cap('add_users');
            $edit_editor->add_cap('delete_users');
            update_option('isa_add_cap_editor_once', 'done');
        }
    }

    // Remove 'Administrator' from the list of roles if the current user is not an admin
    function editable_roles($roles) {
        if (isset($roles['administrator']) && !current_user_can('administrator')) {
            unset($roles['administrator']);
        }
        return $roles;
    }
    // If someone is trying to edit or delete an
    // admin and that user isn't an admin, don't allow it
    function map_meta_cap($caps, $cap, $user_id, $args) {
        switch ($cap) {
        case 'edit_user':
        case 'remove_user':
        case 'promote_user':
            if (isset($args[0]) && $args[0] == $user_id) {
                break;
            }
            else if (!isset($args[0])) {
                $caps[] = 'do_not_allow';
            }
            $other = new WP_User(absint($args[0]));
            if ($other->has_cap('administrator')) {
                if (!current_user_can('administrator')) {
                    $caps[] = 'do_not_allow';
                }
            }
            break;
        case 'delete_user':
        case 'delete_users':
            if (!isset($args[0])) {
                break;
            }
            $other = new WP_User(absint($args[0]));
            if ($other->has_cap('administrator')) {
                if (!current_user_can('administrator')) {
                    $caps[] = 'do_not_allow';
                }
            }
            break;
        default:
            break;
        }
        return $caps;
    }
}