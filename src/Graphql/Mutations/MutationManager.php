<?php

namespace Zngly\Graphql\Db\Graphql\Mutations;

use Zngly\Graphql\Db\Model\Table;

class MutationManager
{

    public function __construct(private Table $model)
    {
        add_action('graphql_register_types', function () {
            new CreateMutation($this->model);
            new UpdateMutation($this->model);
            new DeleteMutation($this->model);
        });
    }
}
