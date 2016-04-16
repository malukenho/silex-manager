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

/**
 * @author  Jefersson Nathan <malukenho@phpse.net>
 */
interface AdapterInterface
{
    public function whereLike($name, $value);

    /**
     * Returns a single database row or column value.
     *
     * @param string      $query
     * @param null|string $column
     *
     * @return string|int|mixed[]
     */
    public function fetch($query, $column = null);

    /**
     * @param string $query
     * @param array  $params
     *
     * @return bool
     */
    public function execute($query, $params = []);

    /**
     * Similar to `PDO::fetchAll`, Its returns an array of data.
     *
     * @param string $sql
     * @param array  $params
     *
     * @return mixed[][]
     */
    public function fetchAll($sql, $params = []);

    /**
     * @param Node   $config
     * @param string $pagination
     *
     * @return mixed
     */
    public function fetchByConfig(Node $config, $pagination);

    public function limit($total, $itemPerPage, $page);

    /**
     * Return a number from total results
     *
     * @param Node $config
     *
     * @return int
     */
    public function count(Node $config);
}
