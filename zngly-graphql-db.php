<?php


/**
 * Plugin Name:       Zngly GraphQL DB
 * Description:       A plugin that allows you to create custom database tables and use them in GraphQL queries & mutations
 * Author:            Vlad-Anton Medves
 * Text Domain:       zngly-graphql-db
 * Version:           0.0.1@alpha
 * Requires PHP:      8.0
 *
 * @package           zngly-graphql-db
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
