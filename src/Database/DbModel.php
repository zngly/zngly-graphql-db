<?php


namespace Zngly\Graphql\Db\Database;

use Zngly\Graphql\Db\Model\Table as TableModel;
use Zngly\Graphql\Db\Utils;

class DbModel
{

    private \Zngly\Graphql\Db\Database\Query $db;

    public function __construct(private TableModel $table_model)
    {
        $query_class_name = Utils::runtime_class_name($this->table_model->graphql_single_name() . 'Query');
        $this->db = new $query_class_name();
    }

    public function new_query(array $args = []): Query
    {
        $query_class_name = Utils::runtime_class_name($this->table_model->graphql_single_name() . 'Query');
        return new $query_class_name($args);
    }

    /**
     * @see WP_Query::parse_query() for all available arguments.
     */
    public function query(array $args = []): array
    {
        $this->args_check($args);
        return $this->db->query($args);
    }

    public function insert(array $args = []): int|false
    {
        $this->args_check($args);
        return $this->db->add_item($args);
    }

    public function update(int $id, array $args = []): int|false
    {
        $this->args_check($args);
        return $this->db->update_item($id, $args);
    }

    public function delete(int $id): int|false
    {
        return $this->db->delete_item($id);
    }

    public function get(int $id)
    {
        return $this->db->get_item($id);
    }

    /**
     * check if all the args are valid
     */
    private function args_check(array $args): void
    {
        // run any check on the args
    }
}
