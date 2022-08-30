<?php

namespace Zngly\Graphql\Db;

use Zngly\Graphql\Db\Database\DatabaseManager;
use Zngly\Graphql\Db\Graphql\GraphqlManager;

class ZnglyDb
{
    /**
     * @param string $version
     * @param Table[] $models
     */
    public function __construct(
        private string $version,
        private array $models
    ) {
        // generate database classes
        DatabaseManager::create($version, $models);

        // register the graphql logic
        new GraphqlManager($models);
    }
}
