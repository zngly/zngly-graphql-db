<?php

namespace Zngly\Graphql\Db\Graphql\Mutations;

use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\Utils\Utils;
use WPGraphQL\AppContext;
use Zngly\Graphql\Db\Database\DatabaseManager;
use Zngly\Graphql\Db\Model\Table;

class UpdateMutation
{
    public function __construct(private Table $model)
    {
        $mutation_name = "update" . $model->graphql_single_name();
        register_graphql_mutation($mutation_name, [
            'inputFields' => $this->input_fields(),
            'outputFields' => $this->output_fields(),
            'mutateAndGetPayload' => self::mutate_and_get_payload(),
        ]);
    }

    private function input_fields()
    {
        return array_merge(
            $this->model->get_input_fields(),
            [
                'id' => [
                    'type'        => [
                        'non_null' => 'ID',
                    ],
                    // translators: the placeholder is the name of the type of post object being updated
                    'description' => sprintf(__('The ID of the %1$s object', 'wp-graphql'), $this->model->graphql_single_name()),
                ],
            ]
        );
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

            // initialize the database manager
            $db_instance = DatabaseManager::get_instance();
            $db = $db_instance->get($this->model->table_single_name());

            // Get the database ID for the comment.
            $post_id       = Utils::get_database_id_from_id($input['id']);
            $input['id']   = $post_id;

            $existing_post = !empty($post_id) ? $db->get($post_id) : null;

            /**
             * If there's no existing post, throw an exception
             */
            if (null === $existing_post || false === $existing_post) {
                // translators: the placeholder is the name of the type of post being updated
                throw new UserError(sprintf(__('No %1$s could be found to update', 'wp-graphql'), $this->model->graphql_single_name()));
            }

            $result = $db->update($post_id, $input);

            if ($result === false)
                // nothing was updated
                return [
                    $this->model->table_single_name() => $existing_post
                ];

            if (empty($result))
                throw new UserError(sprintf(__('There was an error updating the %1$s', 'wp-graphql'), $this->model->graphql_single_name()));


            $loader = $context->get_loader($this->model->table_plural_name());

            return [
                $this->model->table_single_name() => $loader->load_deferred($post_id),
            ];
        };
    }
}
