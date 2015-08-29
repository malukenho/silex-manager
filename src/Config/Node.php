<?php

namespace Manager\Config;

use Silex\Application;

final class Node // implements ConfigNode
{
    public function __construct(Application $app)
    {
        if (isset($app['manager-config'])) {

        }
        // $action      = $app['manager-config']['manager'][$dbTable]['index'];
        // $fields      = $app['manager-config']['manager'][$dbTable]['index']['columns'];
        // $order       = isset($action['order']) ? $action['order'] : 'DESC';
        // $orderColumn = isset($action['orderColumn']) ? $action['orderColumn'] : 'id';
        // $pk          = isset($action['pk']) ? $action['pk'] : 'id';
        // $actions     = isset($action['action']) ? $action['action'] : false;
        // $header      = isset($action['header']) ? $action['header'] : sprintf('Manager: %s', $dbTable);
        // $icon        = isset($action['icon']) ? $action['icon'] : 'setting';
        // $pagination  = '';
        // $itemPerPage = isset($action['item_per_page']) ? $action['item_per_page'] : 10;
        // $pages       = 0;
        // $search      = isset($action['search']) ? $action['search'] : null;
        // $where       = '';
        // $query       = isset($action['query']) ? $action['query'] : '';

        // if ($request->getQueryString()) {
        //     if (isset($search['input'])) {
        //         $where .= ' WHERE ';
        //         foreach ($search['input'] as $input) {
        //             $where .= $input['name'] . ' LIKE "%' . $request->get(str_replace('.', '_', $input['name'])) . '%" AND ';
        //         }

        //         $where = trim($where, ' AND ');
        //     }
        // }

        // if ($query) {
        //     $queryCount = preg_replace('/^SELECT (.+) FROM/', 'SELECT COUNT(*) as total FROM', $query);
        //     $stmt       = $this->pdo->query($queryCount . ' ' . $where);
        //     $result     = $stmt->fetch(\PDO::FETCH_ASSOC);
        //     $total      = $result['total'];
        // } else {
        //     $stmt   = $this->pdo->query(sprintf('SELECT COUNT(*) as total FROM %s %s', $dbTable, $where));
        //     $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        //     $total  = $result['total'];
        // }

        // if (isset($action['pagination'])) {
        //     $pages      = ceil($total / $itemPerPage);
        //     $offset     = ($page * $itemPerPage) - $itemPerPage;
        //     $pagination = ' LIMIT ' . $offset . ',' . $itemPerPage;
        // }

        // if ($active = isset($actions['active'])) {
        //     $fields['active'] = 'active';
        // }

        // if ($query) {
        //     $stmt = $this->pdo->query($query . ' ' . $where . $pagination);
        // } else {
        //     $stmt = $this->pdo->query(
        //         sprintf(
        //             'SELECT %s FROM %s %s ORDER BY %s %s' . $pagination,
        //             implode(',', array_flip($fields)),
        //             $dbTable,
        //             $where,
        //             $orderColumn,
        //             $order
        //         )
        //     );
        // }

        // $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // foreach ($result as $key => $row) {
        //     foreach ($fields as $name => $value) {
        //         if (isset($action['modifier'][$name])) {
        //             $callable            = $action['modifier'][$name];
        //             $result[$key][$name] = $callable($result[$key]);
        //         }
        //     }
        // }

        // return $app['twig']->render($app['manager-config']['view']['index'], [
        //     'rows'         => $result,
        //     'title'        => $fields,
        //     'action'       => $actions,
        //     'pk'           => $pk,
        //     'header'       => $header,
        //     'icon'         => $icon,
        //     'total'        => $total,
        //     'pages'        => $pages,
        //     'pagination'   => $pagination,
        //     'currentTable' => $dbTable,
        //     'search'       => $search,
        //     'currentPage'  => $page,
        // ]);
    }
}
