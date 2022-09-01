<?php

namespace Zngly\Graphql\Db\Graphql\Data;

use Zngly\Graphql\Db\Database\DatabaseManager;
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
                if (empty($keys)) {
                    return $keys;
                }

                $db_instance = DatabaseManager::get_instance();
                $db = $db_instance->get($this->model->table_single_name());
                $row_class_name = Utils::runtime_class_name($this->model->graphql_single_name() . 'Row');

                $loaded_posts = [];

                foreach ($keys as $key) {
                    /**
                     * The query above has added our objects to the cache
                     * so now we can pluck them from the cache to return here
                     * and if they don't exist we can throw an error, otherwise
                     * we can proceed to resolve the object via the Model layer.
                     */
                    $post_object = $db->get((int) $key);

                    if (empty($post_object) || $post_object === false || !($post_object instanceof $row_class_name)) {
                        $loaded_posts[$key] = null;
                    } else {
                        /**
                         * Once dependencies are loaded, return the Post Object
                         */
                        $loaded_posts[$key] = $post_object;
                    }
                }
                return $loaded_posts;
            }
        };

        $_class = get_class($class);
        class_alias($_class, $class_name);

        return $class;
    }
}
