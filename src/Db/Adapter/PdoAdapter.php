<?php

namespace Manager\Db\Adapter;

use Manager\Config\Node;

class PdoAdapter implements AdapterInterface
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var string[]
     */
    private $where;

    /**
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * {@inheritDoc}
     */
    public function whereLike($name, $value)
    {
        $this->where[] = $name . ' LIKE "%' . $value . '%"';

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function fetch($query, $column = null)
    {
        $stmt   = $this->pdo->query($query);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $column ? $result[$column] : $result;
    }

    /**
     * {@inheritDoc}
     */
    public function limit($total, $itemPerPage, $page)
    {
        $pages      = ceil($total / $itemPerPage);
        $offset     = ($page * $itemPerPage) - $itemPerPage;
        $pagination = ' LIMIT ' . $offset . ',' . $itemPerPage;
    }

    /**
     * {@inheritDoc}
     */
    public function fetchAll($sql, $params = [])
    {
        $statement = $this->pdo->query($sql);
        $statement->execute($params);

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * {@inheritDoc}
     */
    public function count(Node $config)
    {
        if ($config->getQuery()) {
            $queryCount = preg_replace(
                '/^SELECT (.+) FROM/',
                'SELECT COUNT(*) as total FROM',
                $config->getQuery()
            );

            return $this->fetch(sprintf('%s %s', $queryCount, $config->getWhere()), 'total');
        }

        return $this->fetch(
            sprintf(
                'SELECT COUNT(*) as total FROM %s %s',
                $config->getDbTable(),
                $config->getWhere()
            ),
            'total'
        );
    }

    /**
     * {@inheritDoc}
     */
    public function fetchByConfig(Node $config, $pagination)
    {
        if ($config->getQuery()) {
            return $this->fetchAll(
                sprintf('%s %s', $config->getQuery(), $config->getWhere(), $pagination)
            );
        }

        $sql = sprintf(
            'SELECT %s FROM %s %s ORDER BY %s %s' . $pagination,
            implode(',', array_flip($config->getColumns())),
            $config->getDbTable(),
            $config->getWhere(),
            $config->getOrderColumn(),
            $config->getOrder()
        );

        return $this->fetchAll($sql);
    }
}
