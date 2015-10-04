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

namespace Manager\Controller;

use Manager\Config\Node;
use Manager\Db\Adapter\AdapterInterface;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Jefersson Nathan <malukenho@phpse.net>
 */
final class ManagerControllerProvider implements ControllerProviderInterface
{
    /**
     * @var AdapterInterface
     */
    private $db;

    /**
     * @param AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->db = $adapter;
    }

    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->get('/{dbTable}/page/{page}', [$this, 'index'])->bind('manager-index')->value('page', 1);
        $controllers->get('/{dbTable}/new',         [$this, 'create'])->bind('manager-new');
        $controllers->post('/{dbTable}/new',        [$this, 'createSave']);
        $controllers->get('/{dbTable}/edit/{id}',   [$this, 'edit'])->bind('manager-edit');
        $controllers->post('/{dbTable}/edit/{id}',  [$this, 'editSave']);
        $controllers->get('/{dbTable}/delete/{id}', [$this, 'delete'])->bind('manager-delete');
        $controllers->get('/{dbTable}/active/{id}', [$this, 'active'])->bind('manager-active');

        return $controllers;
    }

    /**
     * @param Application $app
     * @param Request     $request
     * @param string      $dbTable
     * @param string      $page
     *
     * @return string
     */
    public function index(Application $app, Request $request, $dbTable, $page)
    {
        $config = new Node($app, $dbTable, 'index');
        $columns = $config->getColumns();

        if ($request->getQueryString() && $config->getSearch()) {
            foreach ($config->getSearchInputs() as $input) {
                $this->db->whereLike($input['name'], $request->get(str_replace('.', '_', $input['name'])));
            }
        }

        $total = $this->db->count($config);

        list($pagination, $pages) = $config->getPagination()
            ? $this->db->limit($total, $config->getItemPerPage(), $page)
            : ['', 0];

        $result = $this->db->fetchByConfig($config, $pagination);

        foreach ($result as $key => $row) {
            foreach ($columns as $name => $value) {
                if (isset($action['modifier'][$name])) {
                    $callable = $action['modifier'][$name];
                    $result[$key][$name] = $callable($result[$key]);
                }
            }
        }

        return $app['twig']->render($app['manager-config']['view']['index'], [
            'rows' => $result,
            'title' => $columns,
            'action' => $config->getAction(),
            'header' => $config->getHeader(),
            'icon' => $config->getIcon(),
            'total' => $total,
            'pages' => $pages,
            'pagination' => $pagination,
            'currentTable' => $dbTable,
            'search' => $config->getSearch(),
            'currentPage' => $page,
        ]);
    }

    public function create(Application $app, $dbTable)
    {
        $config = new Node($app, $dbTable, 'new');
        $fields = $config->getColumns();
        $pk = $config->getPrimaryKey();
        $header = $config->getHeader() ?: sprintf('Create: %s', $dbTable);
        $icon = $config->getIcon() ?: 'edit';

        /** @var \Symfony\Component\Form\Form $form */
        $form = $app['form.factory']->createBuilder('form');

        foreach ($fields as $name => $field) {
            $type = isset($field['type']) ? $field['type'] : null;
            $options = isset($field['options']) ? $field['options'] : [];

            $form->add($name, $type, $options);
        }

        return $app['twig']->render($app['manager-config']['view']['new'], [
            'title' => $fields,
            'pk' => $pk,
            'header' => $header,
            'icon' => $icon,
            'form' => $form->getForm()->createView(),
            'currentTable' => $dbTable,
        ]);
    }

    public function createSave(Application $app, Request $request, $dbTable)
    {
        $config = new Node($app, $dbTable, 'new');
        $fields = $config->getColumns();
        $pk = $config->getPrimaryKey();
        $header = $config->getHeader() ?: sprintf('Create: %s', $dbTable);
        $icon = $config->getIcon() ?: 'edit';
        $after = isset($action['after']) ? $action['after'] : '';
        $before = isset($action['before']) ? $action['before'] : '';

        /** @var \Symfony\Component\Form\Form $form */
        $form = $app['form.factory']->createBuilder('form');

        foreach ($fields as $name => $field) {
            $type = isset($field['type']) ? $field['type'] : null;
            $options = isset($field['options']) ? $field['options'] : [];

            $form->add($name, $type, $options);
        }

        $form = $form->getForm();
        $form->handleRequest($request);

        // Save if valid
        if (!$form->isValid()) {
            return $app['twig']->render($app['manager-config']['view']['new'], [
                'title' => $fields,
                'pk' => $pk,
                'header' => $header,
                'icon' => $icon,
                'form' => $form->createView(),
                'currentTable' => $dbTable,
            ]);
        }

        $requestData = $request->request->all();
        if (isset($requestData['form']['_token'])) {
            unset($requestData['form']['_token']);
        }

        if ($before && is_callable($before)) {
            $before($requestData['form']);
        }

        foreach ($requestData['form'] as $key => $row) {
            if (isset($action['modifier'][$key])) {
                $callable = $action['modifier'][$key];
                $requestData['form'][$key] = $callable($requestData['form'][$key], $requestData['form']);
            }
        }

        $requestData['form'] = array_filter($requestData['form']);

        $query = sprintf(
            'INSERT INTO %s(%s) VALUES(%s)',
            $config->getDbTable(),
            implode(', ', array_keys($requestData['form'])),
            '"'.implode('", "', array_map('addslashes', $requestData['form'])).'"'
        );

        if ($this->db->execute($query)) {
            if ($after && is_callable($after)) {
                $after($requestData['form']);
            }

            $app['session']
                ->getFlashBag()
                ->add('messageSuccess', 'Added with success');

            return $app->redirect(
                $app['url_generator']->generate('manager-index', [
                    'dbTable' => $dbTable,
                ])
            );
        }
    }

    public function edit(Application $app, $dbTable, $id)
    {
        $action = $app['manager-config']['manager'][$dbTable]['edit'];
        $fields = $app['manager-config']['manager'][$dbTable]['edit']['columns'];

        if (isset($fields['use_new_form']) && true === $fields['use_new_form']) {
            $fields = $app['manager-config']['manager'][$dbTable]['new']['columns'];
        }

        $pk = isset($action['pk']) ? $action['pk'] : 'id';
        $header = isset($action['header']) ? $action['header'] : sprintf('Edit: %s', $dbTable);
        $icon = isset($action['icon']) ? $action['icon'] : 'edit';

        $result = $this->db->fetch(sprintf('SELECT * FROM %s WHERE %s = %s', $dbTable, $pk, $id));

        /** @var \Symfony\Component\Form\Form $form */
        $form = $app['form.factory']->createBuilder('form');

        foreach ($fields as $name => $field) {
            $type = isset($field['type']) ? $field['type'] : null;
            $options = isset($field['options']) ? $field['options'] : [];

            $form->add($name, $type, $options);
        }

        $form = $form->getForm();
        $form->setData($result);

        return $app['twig']->render($app['manager-config']['view']['edit'], [
            'title' => $fields,
            'pk' => $pk,
            'header' => $header,
            'icon' => $icon,
            'form' => $form->createView(),
            'currentTable' => $dbTable,
        ]);
    }

    public function editSave(Application $app, Request $request, $dbTable, $id)
    {
        $action = $app['manager-config']['manager'][$dbTable]['edit'];
        $fields = $app['manager-config']['manager'][$dbTable]['edit']['columns'];

        if (isset($fields['use_new_form']) && true === $fields['use_new_form']) {
            $action = $app['manager-config']['manager'][$dbTable]['new'];
            $fields = $app['manager-config']['manager'][$dbTable]['new']['columns'];
        }
        $pk = isset($action['pk']) ? $action['pk'] : 'id';
        $header = isset($action['header']) ? $action['header'] : sprintf('Edit: %s', $dbTable);
        $icon = isset($action['icon']) ? $action['icon'] : 'edit';

        /** @var \Symfony\Component\Form\Form $form */
        $form = $app['form.factory']->createBuilder('form');

        foreach ($fields as $name => $field) {
            $type = isset($field['type']) ? $field['type'] : null;
            $options = isset($field['options']) ? $field['options'] : [];

            $form->add($name, $type, $options);
        }

        $form = $form->getForm();
        $form->handleRequest($request);

        // Save if valid
        if (!$form->isValid()) {
            return $app['twig']->render($app['manager-config']['view']['edit'], [
                'title' => $fields,
                'pk' => $pk,
                'header' => $header,
                'icon' => $icon,
                'form' => $form->createView(),
                'currentTable' => $dbTable,
            ]);
        }

        $requestData = $request->request->all();
        if (isset($requestData['form']['_token'])) {
            unset($requestData['form']['_token']);
        }

        foreach ($requestData['form'] as $key => $row) {
            if (isset($action['modifier'][$key])) {
                $callable = $action['modifier'][$key];
                $requestData['form'][$key] = $callable($requestData['form'][$key], $requestData['form']);
            }
        }

        $requestData['form'] = array_filter($requestData['form']);

        $insert = '';
        foreach ($requestData['form'] as $column => $value) {
            $insert .= sprintf('%s="%s", ', $column, addslashes($value));
        }

        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s = %s',
            $dbTable,
            rtrim($insert, ', '),
            $pk,
            $id
        );

        if ($this->db->execute($sql)) {
            $app['session']
                ->getFlashBag()
                ->add('messageSuccess', 'Edited with success');

            return $app->redirect(
                $app['url_generator']->generate('manager-index', [
                    'dbTable' => $dbTable,
                ])
            );
        }
    }

    public function delete(Application $app, $dbTable, $id)
    {
        $config = new Node($app, $dbTable, 'index');

        $sql = sprintf(
            'DELETE FROM %s WHERE %s = %s',
            $config->getDbTable(),
            $config->getPrimaryKey(),
            $id
        );

        if ($this->db->execute($sql)) {
            $app['session']
                ->getFlashBag()
                ->add('messageSuccess', 'Deleted with success');

            return $app->redirect(
                $app['url_generator']->generate('manager-index', [
                    'dbTable' => $dbTable,
                ])
            );
        }
    }
}
