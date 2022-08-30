<?php

namespace Zngly\Graphql\Db\Graphql\Mutations;

use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use Zngly\Graphql\Db\Database\DatabaseManager;
use Zngly\Graphql\Db\Model\Table;

class CreateMutation
{
    public function __construct(private Table $model)
    {
        $mutation_name = "create" . $model->graphql_single_name();
        register_graphql_mutation($mutation_name, [
            'inputFields' => $model->get_input_fields(true),
            'outputFields' => $this->output_fields(),
            'mutateAndGetPayload' => self::mutate_and_get_payload(),
        ]);
    }

    private function output_fields(): array
    {
        return [
            lcfirst($this->model->graphql_single_name()) => [
                'type'        => $this->model->graphql_single_name(),
                'description' => "Create Mutation. " . $this->model->description(),
            ],
        ];
    }

    private function mutate_and_get_payload()
    {
        return function ($input, AppContext $context, ResolveInfo $info) {

            $db_instance = DatabaseManager::get_instance();
            $db = $db_instance->get($this->model->table_single_name());

            $insert_id = $db->insert($input);

            if (empty($insert_id) || $insert_id === false) {
                throw new UserError("Could not create {$this->model->graphql_single_name()}");
            }

            $loader = $context->get_loader($this->model->table_plural_name());

            return [
                $this->model->table_single_name() => $loader->load_deferred($insert_id),
            ];
        };
    }
}
