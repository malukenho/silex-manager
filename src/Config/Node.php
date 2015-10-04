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

namespace Manager\Config;

use Manager\Exception\MissingConfigException;
use Silex\Application;

class Node
{
    const ACTION_INDEX = 'index';

    /**
     * @var string
     */
    private $primaryKey;
    /**
     * @var string
     */
    private $dbTable;
    /**
     * @var string
     */
    private $order;
    /**
     * @var string
     */
    private $orderColumn;
    /**
     * @var mixed[]
     */
    private $columns;
    /**
     * @var string
     */
    private $header;
    /**
     * @var string
     */
    private $icon;
    /**
     * @var string
     */
    private $pagination;
    /**
     * @var int
     */
    private $itemPerPage;
    /**
     * @var null
     */
    private $search;
    /**
     * @var string
     */
    private $query;
    /**
     * @var string
     */
    private $where;

    /**
     * @param Application $app
     * @param string      $dbTable
     * @param string      $action
     */
    public function __construct(Application $app, $dbTable, $action)
    {
        if (!isset($app['manager-config'])) {
            throw new MissingConfigException('The key "manager-config" was not found on $app.');
        }

        if (!isset($app['manager-config']['manager'])) {
            throw new MissingConfigException('The key "manager-config.manager" was not found on $app.');
        }

        if (!isset($app['manager-config']['manager'][$dbTable])) {
            throw new MissingConfigException(sprintf(
                'The key "manager-config.manager.%s" was not found on $app.',
                $dbTable
            ));
        }

        if (!isset($app['manager-config']['manager'][$dbTable][$action])) {
            throw new MissingConfigException(sprintf(
                'The key "manager-config.manager.%s.%s" was not found on $app.',
                $dbTable,
                $action
            ));
        }

        $tableConfig = $app['manager-config']['manager'][$dbTable][$action];

        if (!isset($tableConfig['columns'])) {
            throw new MissingConfigException(sprintf(
                'The key "manager-config.manager.%s.%s.columns" was not found on $app.',
                $dbTable,
                $action
            ));
        }

        $this->dbTable = $dbTable;
        $this->columns = $tableConfig['columns'];
        $this->order = isset($tableConfig['order']) ? $tableConfig['order'] : 'DESC';
        $this->orderColumn = isset($tableConfig['orderColumn']) ? $tableConfig['orderColumn'] : 'id';
        $this->primaryKey = isset($tableConfig['pk']) ? $tableConfig['pk'] : 'id';
        $this->action = isset($tableConfig['action']) ? $tableConfig['action'] : false;
        $this->header = isset($tableConfig['header']) ? $tableConfig['header'] : sprintf('Manager: %s', $dbTable);
        $this->icon = isset($tableConfig['icon']) ? $tableConfig['icon'] : 'setting';
        $this->itemPerPage = isset($tableConfig['item_per_page']) ? $tableConfig['item_per_page'] : 10;
        $this->search = isset($tableConfig['search']) ? $tableConfig['search'] : null;
        $this->query = isset($tableConfig['query']) ? $tableConfig['query'] : '';
    }

    /**
     * @return bool|array
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey ?: 'id';
    }

    /**
     * @return string
     */
    public function getDbTable()
    {
        return $this->dbTable;
    }

    /**
     * @return string
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return string
     */
    public function getOrderColumn()
    {
        return $this->orderColumn;
    }

    /**
     * @return \mixed[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @return string
     */
    public function getPagination()
    {
        return $this->pagination;
    }

    /**
     * @return int
     */
    public function getItemPerPage()
    {
        return $this->itemPerPage;
    }

    /**
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return string
     */
    public function getWhere()
    {
        return $this->where;
    }

    public function getSearchInputs()
    {
        return $this->search['input'];
    }
}
