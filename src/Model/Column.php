<?php

namespace Zngly\Graphql\Db\Model;

class Column
{

    // extra class which determines what wordpress capabilites the field possesses

    /** Methods ***************************************************************/

    /**
     * Sets up the order query, based on the query vars passed.
     *
     * @internal string|array $args {
     *     Optional. Array or query string of order query parameters. Default empty.
     *
     *     @type string   $name           Name of database column
     *     @type string   $type           Type of database column
     *     @type int      $length         Length of database column
     *     @type bool     $unsigned       Is integer unsigned?
     *     @type bool     $zerofill       Is integer filled with zeroes?
     *     @type bool     $binary         Is data in a binary format?
     *     @type bool     $allow_null     Is null an allowed value?
     *     @type mixed    $default        Typically empty/null, or date value
     *     @type string   $extra          auto_increment, etc...
     *     @type bool     $pattern        What is the string-replace pattern?
     *     @type bool     $primary        Is this the primary column?
     *     @type bool     $created        Is this the column used as a created date?
     *     @type bool     $modified       Is this the column used as a modified date?
     *     @type bool     $uuid           Is this the column used as a universally unique identifier?
     *     @type bool     $searchable     Is this column searchable?
     *     @type bool     $sortable       Is this column used in orderby?
     *     @type bool     $date_query     Is this column a datetime?
     *     @type bool     $in             Is __in supported?
     *     @type bool     $not_in         Is __not_in supported?
     *     @type bool     $cache_key      Is this column queried independently?
     *     @type bool     $transition     Does this column transition between changes?
     *     @type string   $validate       A callback function used to validate on save.
     *     @type array    $caps           Array of capabilities to check.
     *     @type array    $aliases        Array of possible column name aliases.
     *     @type array    $relationships  Array of columns in other tables this column relates to.
     * }
     */
}
