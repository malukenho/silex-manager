<?php

namespace Manager\Db\Adapter;

use Manager\Config\Node;

interface AdapterInterface
{
    public function whereLike($name, $value);
    public function fetch($query, $column = null);

    /**
     * @param string $query
     * @param array  $params
     *
     * @return bool
     */
    public function execute($query, $params = []);

    public function fetchAll($sql, $params = []);
    public function fetchByConfig(Node $config, $pagination);
    public function limit($total, $itemPerPage, $page);
    public function count(Node $config);
}
