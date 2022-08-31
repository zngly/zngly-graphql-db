<?php

namespace Zngly\Graphql\Db\Model;

abstract class Table
{
    abstract public static function table_single_name(): string;
    abstract public static function table_plural_name(): string;
    abstract public static function description(): string;
    abstract public static function graphql_single_name(): string;
    abstract public static function graphql_plural_name(): string;
    abstract public static function graphql_from_type(): string;
    // abstract public static function 

    public function db_version_key(): string
    {
        return $this->table_plural_name() . "_db_version";
    }

    /**
     * @return Field[]
     */
    abstract public static function fields(): array;

    // abstract public static function relationships(): array;

    /** */
    public function get_schema(): string
    {
        $indexes = [];
        $sql_fields = [];
        $fields = $this->fields();

        // loop through fields and generate sql fields
        foreach ($fields as $field) {
            $sql = $field->get_sql_string();
            $idx = $field->get_index_string();

            if ($sql) $sql_fields[] = $sql;
            if ($idx) $indexes[] = $idx;
        }

        // sort the indexes where the string that contains PRIMARY_KEY is first
        usort($indexes, function ($a, $b) {
            if (strpos($a, 'PRIMARY_KEY') !== false) return -1;
            if (strpos($b, 'PRIMARY_KEY') !== false) return 1;
            return 0;
        });

        $fields_str = implode(",\r\n", $sql_fields);
        $indexes_str = implode(",\r\n", $indexes);

        $final_str  = "";
        if ($fields_str) $final_str .= "$fields_str";
        if ($indexes_str) $final_str .= ",\r\n$indexes_str";

        return $final_str;
    }

    public function get_input_fields(bool $include_non_null = false): array
    {
        $input_fields = [];
        $fields = $this->graphql_fields();

        foreach ($fields as $field) {
            // if field is id, don't include
            if ($field->is_primary_key()) continue;

            $type = $field->get_graphql_type();

            if ($field->is_not_null() && $include_non_null === true) {
                $type = ["non_null" => $type];
            }

            $input_fields[$field->get_graphql_name()] = [
                'type' => $type,
                'description' => $field->description,
            ];
        }


        return $input_fields;
    }

    /**
     * @return Field[]
     */
    public function graphql_fields(): array
    {
        // update the fields to get rid of any fields with _index set
        // fields to be used with graphql only
        $fields = $this->fields();
        $updated_fields = [];
        foreach ($fields as $field)
            if (!isset($field->_index) && isset($field->name))
                $updated_fields[] = $field;

        return $updated_fields;
    }
}
