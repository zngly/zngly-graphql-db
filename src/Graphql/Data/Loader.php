<?php

namespace Zngly\Graphql\Db\Graphql\Data;

use Zngly\Graphql\Db\Model\Table;
use Zngly\Graphql\Db\Utils;

class GenericLoader
{
    public function __construct(Table $model)
    {
        add_filter('graphql_data_loaders', function ($loaders, $context) use ($model) {
            $table_name = $model::table_plural_name();

            $loaders[$table_name] = $this->generate_loader($model, $context);

            return $loaders;
        }, 10, 2);
    }

    private function generate_loader(Table $model, $context)
    {
        $class_name = Utils::runtime_class_name($model->graphql_single_name() . 's' . 'Loader');

        // if class is defined
        if (class_exists($class_name))
            return new $class_name($context, $model);

        $class = new class($context, $model) extends \WPGraphQL\Data\Loader\AbstractDataLoader
        {
            public function __construct($context, private Table $model)
            {
                parent::__construct($context);
            }

            public function loadKeys(array $keys)
            {
                if (empty($keys) || !is_array($keys)) {
                    return [];
                }

                $fields = [];
                foreach ($this->model->graphql_fields() as $field)
                    $fields[] = $field->name;

                $table_name = $this->model::table_plural_name();

                global $wpdb;

                // @todo: properly load the keys
                $results = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT * FROM `" . $wpdb->prefix . $table_name . "`"
                    )
                );

                $results_by_id = [];
                foreach ($results as $result) {
                    $data = [];
                    foreach ($fields as $field) {
                        $data[$field] = $result->$field;
                    }

                    $results_by_id[(int) $result->id] = $data;
                }

                $data_array = [];
                foreach ($keys as $key) {
                    if (isset($results_by_id[$key])) {
                        $data_array[$key] = $results_by_id[$key];
                    }
                }

                return $data_array;
            }
        };

        $_class = get_class($class);
        class_alias($_class, $class_name);

        return $class;
    }
}
