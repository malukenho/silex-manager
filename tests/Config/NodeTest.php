<?php

namespace ManagerTest\Config;

use Manager\Config\Node;
use Manager\Exception\MissingConfigException;
use Silex\Application;

class NodeTest extends \PHPUnit_Framework_TestCase
{
    public function testCheckForTheConfigKey()
    {
        /** @var \Silex\Application||PHPUnit_Framework_MockObject_MockObject $appMock */
        $appMock = $this->getMockBuilder(Application::class)
            ->disableOriginalConstructor()
            ->getMock();

        $appMock->expects($this->once())
            ->method('offsetExists')
            ->with('manager-config');

        $this->setExpectedException(MissingConfigException::class);

        new Node($appMock);
    }
}
