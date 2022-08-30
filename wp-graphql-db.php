<?php


/**
 * Plugin Name:       Zngly GraphQL DB
 * Description:       Add Description Here
 * Author:            Vlad-Anton Medves
 * Text Domain:       zngly-graphql-db
 * Version:           0
 * Requires PHP:      7.0
 *
 * @package         Zngly_Graphql_Db
 */

namespace Zngly\Graphql\Db;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}


/**
 * Check whether WPGraphQL is active
 * @return bool
 */
function can_load_plugin()
{
    // Is WPGraphQL active?
    if (!class_exists('WPGraphQL')) {
        return false;
    }

    return true;
}


// function zngly_graphql()
// {
//     echo "<script>console.log('Hello from zngly-graphql-db')</script>";
// }
// zngly_graphql();
