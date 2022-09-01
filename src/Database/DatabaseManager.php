<?php


namespace Zngly\Graphql\Db\Database;

use Zngly\Graphql\Db\Model\Table;
use Zngly\Graphql\Db\Utils;

class DatabaseManager
{
    private static $instance = null;

    /**
     * @param string $version
     * @param Table[] $tables
     */
    private function __construct(
        private string $version,
        private array $tables
    ) {

        foreach ($this->tables as $table)
            $this->db_table_generator($table);
    }

    /**
     * @param string $version
     * @param Table[] $tables
     */
    public static function create(string $version, array $tables): DatabaseManager
    {
        if (self::$instance == null)
            self::$instance = new DatabaseManager($version, $tables);
        return self::$instance;
    }

    public static function get_instance(): DatabaseManager
    {
        if (self::$instance == null)
            throw new \Exception("DatabaseManager is not initialized");
        return self::$instance;
    }

    public function get(string $table_name): DbModel
    {

        $valid_names = [];
        // check if table exists
        foreach ($this->tables as $table) {
            if ($table->table_single_name() == $table_name)
                return new DbModel($table);
            $valid_names[] = $table->table_single_name();
        }

        throw new \Exception("Table $table_name does not exist. Valid tables are: " . json_encode($valid_names));
    }

    /**
     * Generates the berlindb classes for a table
     * @param Table $table
     */
    private function db_table_generator(Table $table)
    {
        // generate table classes
        $this->schema_gen($table);
        $this->row_gen($table);
        $this->query_gen($table);

        // generate the new table
        $db_table = $this->table_gen($table);

        $db_table->reset_db_version();
        $db_table->maybe_upgrade();
    }

    private function table_gen(Table $table): \Zngly\Graphql\Db\Database\Table
    {
        $class_name = Utils::runtime_class_name($table->graphql_single_name() . 'Table');

        // if class is defined
        if (class_exists($class_name))
            return new $class_name();

        $class = new class($table, $this->version) extends \Zngly\Graphql\Db\Database\Table
        {
            protected $upgrades = [];
            /**
             * @param Table $table
             */
            public function __construct(
                private Table $table,
                string $version
            ) {
                $this->name = $table::table_plural_name();
                $this->db_version_key = $table->db_version_key();
                $this->description = $table::description();
                $this->version = $version;

                parent::__construct();
            }

            protected function set_schema()
            {
                $this->schema = $this->table->get_schema();
            }
        };

        $_class = get_class($class);
        class_alias($_class, $class_name);

        return $class;
    }

    private function schema_gen(Table $table)
    {
        $class_name = Utils::runtime_class_name($table->graphql_single_name() . 'Schema');

        // if class is defined
        if (class_exists($class_name))
            return;

        $columns = [];
        $fields = $table->fields();
        foreach ($fields as $field) {
            $raw_type = $field->type->type;

            // return only the string before the first "(" 
            $type_short = strpos($raw_type, "(") !== false ? substr($raw_type, 0, strpos($raw_type, "(")) : $raw_type;

            // if the type has a length, get it
            $type_len = "";
            $type_len_reg = '/\((.*)\)/';
            preg_match($type_len_reg, $raw_type, $type_len_match);
            if ($type_len_match)
                $type_len = $type_len_match[1];

            // if raw_type has signed or unsigned
            $type_unsigned = "";
            if (strpos($raw_type, "UNSIGNED") !== false)
                $type_unsigned = "true";
            else if (strpos($raw_type, "SIGNED") !== false)
                $type_unsigned = "false";

            // set searchable if it is a string or int
            $searchable = "false";
            if ($field->type->graphql_type === "String" || $field->type->graphql_type === "Int")
                $searchable = "true";

            // $short_type =
            $columns[$field->name] = [
                'name' => strtolower($field->name),
                'type' => strtolower($type_short),
                'length' => strtolower($type_len),
                'primary' => $field->is_primary_key() ? "true" : "",
                'unsigned' => $type_unsigned,
                'searchable' => $searchable,
                'sortable' => $searchable
            ];
        }


        // convert columns array to string for eval
        $columns_str = "";
        foreach ($columns as $column) {
            $fields = "";
            foreach ($column as $key => $value)
                $fields .= "'$key' => '$value', ";

            $columns_str .= "
            '{$column['name']}' => [
                {$fields}
            ],
            ";
        }

        eval("class $class_name extends \Zngly\Graphql\Db\Database\Schema {
            public \$columns = [$columns_str];
        }");
    }

    private function query_gen(Table $table)
    {
        $class_name = Utils::runtime_class_name($table->graphql_single_name() . 'Query');

        if (class_exists($class_name))
            return;

        $table_schema = Utils::runtime_class_name($table->graphql_single_name() . 'Schema');
        $item_shape = Utils::runtime_class_name($table->graphql_single_name() . 'Row');

        $table_name = $table->table_single_name();
        $table_plural_name = $table->table_plural_name();

        eval("class $class_name extends \Zngly\Graphql\Db\Database\Query {
            protected \$table_name = '$table_plural_name';
            protected \$table_alias = 'zngly_$table_name';
            protected \$table_schema = '$table_schema';
            protected \$item_name = '$table_name';
            protected \$item_name_plural = '$table_plural_name';
            protected \$item_shape = '$item_shape';
        }");
    }

    private function row_gen(Table $table)
    {
        $class_name = Utils::runtime_class_name($table->graphql_single_name() . 'Row');

        // if class is defined
        if (class_exists($class_name))
            return;

        $fields = $table->graphql_fields();
        $items = [];
        foreach ($fields as $field) {
            // if the field name is the same as the field graphql name, use the field name
            // otherwise add the graphql name as well.
            $name = $field->name;
            $gql_name = $field->get_graphql_name();

            $type_cast = strtolower($field->get_graphql_type());
            if ($type_cast === 'id') $type_cast = 'int';

            if ($name !== $gql_name)
                $items[] = "\$this->{$gql_name} = ({$type_cast}) \$this->{$name};";

            $items[] = "\$this->{$name} = ({$type_cast}) \$this->{$name};";
        }

        $items = implode("\n", $items);

        eval("class $class_name extends \Zngly\Graphql\Db\Database\Row {

            public function __construct( \$item ) {
                parent::__construct( \$item );

                {$items}
            }
        
            /**
             * Retrieves the HTML to display the information about this book.
             *
             * @since 1.0.0
             *
             * @return string HTML output to display this record's data.
             */
            public function display() {
                // \$result = '<h3>' . \$this->title . '</h3>';
                // \$result .= '<dl>';
                // \$result .= '<dt>Title: </dt><dd>' . \$this->title . '</dd>';
                // \$result .= '<dt>ISBN: </dt><dd>' . \$this->isbn . '</dd>';
                // \$result .= '<dt>Published: </dt><dd>' . date( 'M d, Y', \$this->date_published ) . '</dd>';
                // \$result .= '</dl>';
                // return \$result;
            }
        
        }");
    }
}
