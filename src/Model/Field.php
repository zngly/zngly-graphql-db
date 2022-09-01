<?php


namespace Zngly\Graphql\Db\Model;

use WPGraphQL\Utils\Utils as WpUtils;

class Field
{
    public string $name;
    public string $description;
    public FieldType $type;

    // sql options
    private bool $not_null;
    private bool $primary_key;
    private bool $auto_increment;
    private bool $unique;
    private bool $collate;
    private string $default;
    private string $index;
    private string $_index;

    // extra options
    private bool $is_id;

    // graphql opts
    private string $graphql_type;
    private string $graphql_name;

    public function __construct()
    {
    }

    public static function create()
    {
        return new self;
    }

    public static function _index(string $name, array $keys)
    {
        $self = new self;
        $self->_index = "KEY `$name` (`" . implode("`, `", $keys) . "`)";
        return $self;
    }

    public function get_sql_string(): string | null
    {
        $sql = "";

        if (isset($this->_index) && !isset($this->name)) return null;

        if (isset($this->name)) $sql .= "`" . $this->name . "`";
        else throw new \Exception("Field must be set");

        // check field type is an instance of FieldType
        if (!($this->type instanceof FieldType))
            throw new \Exception("Field type must be an instance of FieldType");

        if (isset($this->type)) $sql .= " " . $this->type->type;
        else throw new \Exception("Field type must be set");

        if (isset($this->not_null)) $sql .= " NOT NULL";

        if (isset($this->auto_increment)) $sql .= " AUTO_INCREMENT";

        if (isset($this->unique)) $sql .= " UNIQUE";

        if (isset($this->collate)) $sql .= " COLLATE utf8mb4_unicode_520_ci";

        if (isset($this->default)) $sql .= " DEFAULT '" . $this->default . "'";
        else if (!isset($this->default) && !isset($this->not_null)) $sql .= " DEFAULT NULL";

        return $sql;
    }

    public function get_index_string(): string | null
    {
        if (isset($this->primary_key)) return "PRIMARY KEY (`" . $this->name . "`)";
        else if (isset($this->_index)) return $this->_index;
        else if (isset($this->index)) return "KEY `" . $this->name . "` (`" . $this->name . "`)";

        return null;
    }

    public function name(string $name)
    {
        $this->name = $name;
        return $this;
    }

    public function description(string $description)
    {
        $this->description = $description;
        return $this;
    }

    public function type(FieldType $type)
    {
        $this->type = $type;
        return $this;
    }

    public function not_null(bool $not_null = true)
    {
        $this->not_null = $not_null;
        return $this;
    }

    public function primary_key(bool $primary_key = true)
    {
        $this->primary_key = $primary_key;
        return $this;
    }

    public function auto_increment(bool $auto_increment = true)
    {
        $this->auto_increment = $auto_increment;
        return $this;
    }

    public function unique(bool $unique = true)
    {
        $this->unique = $unique;
        return $this;
    }

    public function collate(bool $collate = true)
    {
        $this->collate = $collate;
        return $this;
    }

    public function default(string $default = null)
    {
        if ($default !== null) $this->default = $default;
        else $this->default = "NULL";

        return $this;
    }

    public function index(bool $index = true)
    {
        $this->index = $index;
        return $this;
    }

    public function is_id(bool $is_id = true)
    {
        $this->is_id = $is_id;
        return $this;
    }


    public function graphql_type(string $graphql_type)
    {
        $this->graphql_type = $graphql_type;
        return $this;
    }

    public function graphql_name(string $graphql_name)
    {
        $this->graphql_name = $graphql_name;
        return $this;
    }

    public function get_graphql_name(): string
    {
        $name = "";
        if (isset($this->graphql_name)) $name =  $this->graphql_name;
        else $name = $this->name;

        return WpUtils::format_field_name($name);
    }

    public function get_graphql_type(): string
    {
        $field_type =  $this->type;

        if (isset($this->graphql_type))
            return $this->graphql_type;

        if (isset($this->is_id) && $this->is_id === true) return "ID";

        return $field_type->graphql_type;
    }

    /**
     * Get the value of primary_key
     */
    public function is_primary_key()
    {
        if (isset($this->primary_key)) return $this->primary_key;
        return false;
    }

    /**
     * Get the value of not_null
     */
    public function is_not_null()
    {
        if (isset($this->not_null)) return $this->not_null;
        return false;
    }
}
