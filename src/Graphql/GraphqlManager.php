<?php


namespace Zngly\Graphql\Db\Graphql;

use WPGraphQL\Data\Connection\AbstractConnectionResolver;
use Zngly\Graphql\Db\Graphql\Mutations\MutationManager;
use Zngly\Graphql\Db\Model\Table;
use Zngly\Graphql\Db\Utils;

class GraphqlManager
{

    /**
     * @param Table[] $models
     */
    public function __construct(
        private array $models
    ) {

        foreach ($this->models as $model) {
            add_action('graphql_init', function () use ($model) {
                $this->data_loaders($model);
            });

            add_action('graphql_register_types', function () use ($model) {
                $this->register_graphql_object_type($model);

                $this->register_graphql_connection($model);
            });

            new MutationManager($model);
        }
    }

    private function data_loaders(Table $model)
    {
        add_filter('graphql_data_loaders', function ($loaders, $context) use ($model) {
            $table_name = $model::table_plural_name();

            $loaders[$table_name] = $this->get_loader($model, $context);

            return $loaders;
        }, 10, 2);
    }

    private function get_loader(Table $model, $context)
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

    private function register_graphql_object_type(Table $model)
    {
        $fields = [];

        foreach ($model->graphql_fields() as $field) {
            $field_name = $field->get_graphql_name();

            $fields[$field_name] = [
                'type' => $field->get_graphql_type(),
                'description' => $field->description,
            ];
        }


        register_graphql_object_type($model->graphql_single_name(), [
            'description' => $model->description(),
            'fields' => $fields
        ]);
    }

    private function register_graphql_connection(Table $model)
    {
        register_graphql_connection([
            'fromType' => $model->graphql_from_type(),
            'toType' => $model->graphql_single_name(),
            'fromFieldName' => $model->table_plural_name(),
            'resolve' => function ($source, $args, $context, $info) use ($model) {
                $resolver = $this->get_connection_resolver($source, $args, $context, $info, $model);
                return $resolver->get_connection();
            },
            // We may need to add connections to this later
            // https://kasn.dev/add-custom-wp-graphql-types/
            // 'connectionFields' => [
            //     'answers' => [
            //         'type'        => [
            //             'list_of' => 'PollAnswer',
            //         ],
            //         'description' => __('The nodes of the connection, without the edges', 'wp-graphql'),
            //         'resolve'     => function ($source, $args, $context, $info) {
            //             return  !empty($source['nodes']) ? $source['nodes'] : [];
            //         },
            //     ],
            // ],
            // 'connectionArgs'   => [],
        ]);
    }

    private function get_connection_resolver($source, $args, $context, $info, Table $model,)
    {
        $class_name = Utils::runtime_class_name($model->graphql_single_name() . 's' . 'ConnectionResolver');

        // if class is defined
        if (class_exists($class_name))
            return new $class_name($context, $model);

        $class = new class($source, $args, $context, $info, $model) extends AbstractConnectionResolver
        {

            // inherit from parent and add $model to our class
            public function __construct(
                $source,
                $args,
                $context,
                $info,
                private Table $model,
            ) {
                parent::__construct($source, $args, $context, $info);
            }

            public function get_loader_name()
            {
                return $this->model->table_plural_name();
            }

            public function get_query_args()
            {
                return [];
            }

            public function get_query()
            {
                global $wpdb;
                $current_user_id = get_current_user_id();

                // @todo
                // $tmp = sprintf(
                //     'SELECT id FROM %1$s%2$s WHERE id=%3$d LIMIT 10',
                //     $wpdb->prefix,
                //     $this->model->table_plural_name(),
                //     $current_user_id
                // );

                $ids_array = $wpdb->get_results(
                    $wpdb->prepare(
                        sprintf(
                            'SELECT * FROM %1$s%2$s',
                            $wpdb->prefix,
                            $this->model->table_plural_name()
                        )
                    )
                );

                $ids = !empty($ids_array) ? array_values(array_column($ids_array, 'id')) : [];
                return $ids;
            }

            public function get_ids()
            {
                return $this->get_query();
            }

            public function is_valid_offset($offset)
            {
                return true;
            }

            public function is_valid_model($model)
            {
                return true;
            }

            public function should_execute()
            {
                return true;
            }
        };

        $_class = get_class($class);
        class_alias($_class, $class_name);

        return $class;
    }
}
