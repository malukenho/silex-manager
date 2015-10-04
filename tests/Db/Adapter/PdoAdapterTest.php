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

namespace ManagerTest\Db\Adapter;

use Manager\Db\Adapter\PdoAdapter;

/**
 * Tests for {@see \Manager\Db\Adapter\PdoAdapter}.
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 *
 * @group  Unitary
 * @covers \Manager\Db\Adapter\PdoAdapter
 */
class PdoAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function testPdoInterfaceIsAValidAdapter()
    {
        /** @var \PDO|\PHPUnit_Framework_MockObject_MockObject $pdo */
        $pdo = $this->getMockBuilder('\PDO')
            ->disableOriginalConstructor()
            ->getMock();

        $adapter = new PdoAdapter($pdo);

        $this->assertInstanceOf('Manager\Db\Adapter\PdoAdapter', $adapter);
    }

    public function testShouldMountCorrectWhereLike()
    {
        /** @var \PDO|\PHPUnit_Framework_MockObject_MockObject $pdo */
        $pdo = $this->getMockBuilder('PDO')
            ->disableOriginalConstructor()
            ->getMock();

        $pdo->expects($this->never())
            ->method('query');

        $adapter = new PdoAdapter($pdo);

        $this->assertAttributeEmpty('where', $adapter);

        $return = $adapter->whereLike('column', 'value');

        $this->assertSame($return, $adapter);
        $this->assertAttributeCount(1, 'where', $adapter);
    }

    public function testShouldFetchCorrectData()
    {
        $expected = 'expected';

        /** @var \PDO|\PHPUnit_Framework_MockObject_MockObject $pdo */
        $pdo = $this->getMockBuilder('PDO')
            ->setMethods(['fetch', 'query'])
            ->disableOriginalConstructor()
            ->getMock();

        $pdo->expects($this->once())
            ->method('query')
            ->willReturnSelf();

        $pdo->expects($this->once())
            ->method('fetch')
            ->will($this->returnValue($expected));

        $adapter = new PdoAdapter($pdo);
        $result = $adapter->fetch('SQL QUERY');

        $this->assertSame($expected, $result);
    }

    public function testShouldGetLimitCorrectly()
    {
        $expected = [
            ' LIMIT -2,2',
            5.0,
        ];

        /** @var \PDO|\PHPUnit_Framework_MockObject_MockObject $pdo */
        $pdo = $this->getMockBuilder('PDO')
            ->disableOriginalConstructor()
            ->getMock();

        $adapter = new PdoAdapter($pdo);
        $result = $adapter->limit(10, 2, 0);

        $this->assertSame($expected, $result);
    }

    public function testShouldFetchAllData()
    {
        $expected = 'expected';
        $params = [
            'twitter' => '@malukenho',
        ];

        /** @var \PDO|\PHPUnit_Framework_MockObject_MockObject $pdo */
        $pdo = $this->getMockBuilder('PDO')
            ->setMethods(['fetchAll', 'query', 'execute'])
            ->disableOriginalConstructor()
            ->getMock();

        $pdo->expects($this->once())
            ->method('query')
            ->willReturnSelf();

        $pdo->expects($this->once())
            ->method('execute')
            ->with($params)
            ->willReturnSelf();

        $pdo->expects($this->once())
            ->method('fetchAll')
            ->with(\PDO::FETCH_ASSOC)
            ->will($this->returnValue($expected));

        $adapter = new PdoAdapter($pdo);
        $result = $adapter->fetchAll('SQL QUERY', $params);

        $this->assertSame($expected, $result);
    }

    public function testShouldCountRows()
    {
        /* @var \Manager\Config\Node|\PHPUnit_Framework_MockObject_MockObject $pdo */
        $config = $this->getMockBuilder('Manager\Config\Node')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \PDO|\PHPUnit_Framework_MockObject_MockObject $pdo */
        $pdo = $this->getMockBuilder('PDO')
            ->setMethods(['fetch', 'query', 'execute'])
            ->disableOriginalConstructor()
            ->getMock();

        $pdo->expects($this->once())
            ->method('query')
            ->willReturnSelf();

        $pdo->expects($this->once())
            ->method('fetch')
            ->with(\PDO::FETCH_ASSOC)
            ->will($this->returnValue(1));

        $adapter = new PdoAdapter($pdo);
        $result = $adapter->count($config);

        $this->assertNull($result);
    }

    public function testShouldCountRowsUsingSpecifiedQuery()
    {
        /* @var \Manager\Config\Node|\PHPUnit_Framework_MockObject_MockObject $pdo */
        $config = $this->getMockBuilder('Manager\Config\Node')
            ->disableOriginalConstructor()
            ->getMock();

        $config->expects($this->exactly(2))
            ->method('getQuery')
            ->will($this->returnValue('SELECT * FROM user'));

        $config->expects($this->once())
            ->method('getWhere')
            ->will($this->returnValue('WHERE is_admin = 1'));

        /** @var \PDO|\PHPUnit_Framework_MockObject_MockObject $pdo */
        $pdo = $this->getMockBuilder('PDO')
            ->setMethods(['fetch', 'query', 'execute'])
            ->disableOriginalConstructor()
            ->getMock();

        $pdo->expects($this->once())
            ->method('query')
            ->willReturnSelf();

        $pdo->expects($this->once())
            ->method('fetch')
            ->with(\PDO::FETCH_ASSOC)
            ->will($this->returnValue(1));

        $adapter = new PdoAdapter($pdo);
        $result = $adapter->count($config);

        $this->assertNull($result);
    }

    public function testShouldFetchConfig()
    {
        /* @var \Manager\Config\Node|\PHPUnit_Framework_MockObject_MockObject $pdo */
        $config = $this->getMockBuilder('Manager\Config\Node')
            ->disableOriginalConstructor()
            ->getMock();

        $config->expects($this->once())
            ->method('getColumns')
            ->will($this->returnValue(['github' => 'malukenho']));

        /** @var \PDO|\PHPUnit_Framework_MockObject_MockObject $pdo */
        $pdo = $this->getMockBuilder('PDO')
            ->setMethods(['fetchAll', 'query', 'execute'])
            ->disableOriginalConstructor()
            ->getMock();

        $pdo->expects($this->once())
            ->method('query')
            ->willReturnSelf();

        $pdo->expects($this->once())
            ->method('fetchAll')
            ->with(\PDO::FETCH_ASSOC)
            ->will($this->returnValue(1));

        $adapter = new PdoAdapter($pdo);
        $result = $adapter->fetchByConfig($config, $pagination = 1);

        $this->assertSame(1, $result);
    }

    public function testShouldFetchConfigUsingSpecifiedQuery()
    {
        /* @var \Manager\Config\Node|\PHPUnit_Framework_MockObject_MockObject $pdo */
        $config = $this->getMockBuilder('Manager\Config\Node')
            ->disableOriginalConstructor()
            ->getMock();

        $config->expects($this->exactly(2))
            ->method('getQuery')
            ->will($this->returnValue('SELECT * FROM user'));

        $config->expects($this->once())
            ->method('getWhere');

        /** @var \PDO|\PHPUnit_Framework_MockObject_MockObject $pdo */
        $pdo = $this->getMockBuilder('PDO')
            ->setMethods(['fetchAll', 'query', 'execute'])
            ->disableOriginalConstructor()
            ->getMock();

        $pdo->expects($this->once())->method('query')->willReturnSelf();

        $pdo->expects($this->once())->method('fetchAll')
            ->with(\PDO::FETCH_ASSOC)
            ->will($this->returnValue(1));

        $adapter = new PdoAdapter($pdo);
        $result = $adapter->fetchByConfig($config, $pagination = 1);

        $this->assertSame(1, $result);
    }

    public function testShouldExecuteSql()
    {
        $expected = 1;
        $params = [
            'twitter' => '@malukenho',
        ];

        /** @var \PDO|\PHPUnit_Framework_MockObject_MockObject $pdo */
        $pdo = $this->getMockBuilder('PDO')
            ->setMethods(['prepare', 'execute'])
            ->disableOriginalConstructor()
            ->getMock();

        $pdo->expects($this->once())
            ->method('prepare')
            ->willReturnSelf();

        $pdo->expects($this->once())
            ->method('execute')
            ->with($params)
            ->will($this->returnValue(1));

        $adapter = new PdoAdapter($pdo);
        $result = $adapter->execute('SQL QUERY', $params);

        $this->assertSame($expected, $result);
    }
}
