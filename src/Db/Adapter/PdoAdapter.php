<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

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
        $pagination = sprintf(' LIMIT %s,%s', $offset, $itemPerPage);

        return [
            $pagination,
            $pages
        ];
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
                sprintf('%s %s', $config->getQuery(), $config->getWhere()),
                $pagination
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

    /**
     * {@inheritDoc}
     */
    public function execute($query, $params = [])
    {
        $statement = $this->pdo->prepare($query);

        return $statement->execute($params);
    }
}
