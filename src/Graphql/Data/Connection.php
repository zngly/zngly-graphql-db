<?php

namespace Zngly\Graphql\Db\Graphql\Data;

use Zngly\Graphql\Db\Model\Table;

class GenericConnection
{
    public static function register(Table $model)
    {
        register_graphql_connection([
            'fromType' => $model->graphql_from_type(),
            'toType' => $model->graphql_single_name(),
            'fromFieldName' => $model->table_plural_name(),
            'resolve' => function ($source, $args, $context, $info) use ($model) {
                $resolver = GenericConnectionResolver::get($source, $args, $context, $info, $model);
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
}
