<?php

namespace PlaygroundDesignTest\Controller\Frontend;

use Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class DashboardControllerTest extends AbstractHttpControllerTestCase
{
    protected $traceError = true;

    protected function setUp(): void
    {
        $this->setApplicationConfig(
            include __DIR__ . '/../../TestConfig.php'
        );

        parent::setUp();
    }

    public function testIndexAction()
    {
        $this->dispatch('/admin');
        
        $this->assertModuleName('playgrounddesign');
        $this->assertControllerName('playgrounddesign\controller\admin\dashboard');
        $this->assertControllerClass('DashboardController');
        $this->assertActionName('index');
        $this->assertMatchedRouteName('admin');
    }
}
