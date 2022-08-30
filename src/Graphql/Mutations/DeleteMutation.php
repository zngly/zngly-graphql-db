<?php

namespace Zngly\Graphql\Db\Graphql\Mutations;

use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use Zngly\Graphql\Db\Database\DatabaseManager;
use Zngly\Graphql\Db\Model\Table;
use GraphQLRelay\Relay;
use WPGraphQL\Utils\Utils;

class DeleteMutation
{
    public function __construct(private Table $model)
    {
        $mutation_name = "delete" . $model->graphql_single_name();
        register_graphql_mutation($mutation_name, [
            'inputFields' => $this->input_fields(),
            'outputFields' => $this->output_fields(),
            'mutateAndGetPayload' => self::mutate_and_get_payload(),
        ]);
    }

    private function input_fields(): array
    {
        return [
            'id'          => [
                'type'        => [
                    'non_null' => 'ID',
                ],
                // translators: The placeholder is the name of the post's post_type being deleted
                'description' => sprintf(__('The ID of the %1$s to delete', 'wp-graphql'), $this->model->graphql_single_name()),
            ],
        ];
    }

    private function output_fields(): array
    {
        return [
            'deletedId' => [
                'type'        => 'ID',
                'description' => __('The ID of the deleted object', 'wp-graphql'),
                'resolve'     => function ($payload) {
                    $name = lcfirst($this->model->graphql_single_name());
                    $deleted = $payload[$name];

                    return !empty($deleted->ID) ? Relay::toGlobalId('post', (string) $deleted->ID) : null;
                },
            ],
            lcfirst($this->model->graphql_single_name()) => [
                'type'        => $this->model->graphql_single_name(),
                'description' => "The object before it was deleted. " . $this->model->description(),
                'resolve'     => function ($payload) {
                    $name = lcfirst($this->model->graphql_single_name());
                    $deleted = $payload[$name];

                    return !empty($deleted->ID) ? $deleted : null;
                },
            ],
        ];
    }

    private function mutate_and_get_payload()
    {
        return function ($input, AppContext $context, ResolveInfo $info) {

            $db_instance = DatabaseManager::get_instance();
            $db = $db_instance->get($this->model->table_single_name());

            $post_id = Utils::get_database_id_from_id($input['id']);

            /**
             * Get the post object before deleting it
             */
            $post_before_delete = !empty($post_id) ? $db->get($post_id) : null;

            if (empty($post_before_delete)) {
                throw new UserError(__('The post could not be deleted, It must be already deleted', 'wp-graphql'));
            }


            $delete_id = $db->delete($input['id']);

            if (empty($delete_id) || $delete_id === false) {
                throw new UserError(__('The post could not be deleted', 'wp-graphql'));
            }

            return [
                $this->model->table_single_name() => $post_before_delete,
            ];
        };
    }
}
