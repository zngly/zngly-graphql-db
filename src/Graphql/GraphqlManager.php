<?php


namespace Zngly\Graphql\Db\Graphql;


use Zngly\Graphql\Db\Graphql\Mutations\MutationManager;
use Zngly\Graphql\Db\Graphql\Data\GenericConnection;
use Zngly\Graphql\Db\Graphql\Data\GenericLoader;
use Zngly\Graphql\Db\Model\Table;

class GraphqlManager
{

    /**
     * @param Table[] $models
     */
    public function __construct(
        private array $models
    ) {

        foreach ($this->models as $model) {
            new GenericLoader($model);

            add_action('graphql_register_types', function () use ($model) {
                $this->register_graphql_object_type($model);

                GenericConnection::register($model);
            });

            new MutationManager($model);
        }
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
}
