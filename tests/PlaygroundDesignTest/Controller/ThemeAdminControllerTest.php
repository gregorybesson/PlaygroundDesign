<?php
namespace PlaygroundDesignTest\Controller\Frontend;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

use PlaygroundDesignTest\Bootstrap;
use PlaygroundDesign\Entity\Theme;

class ThemeAdminControllerTest extends AbstractHttpControllerTestCase
{
    protected $traceError = true;

    protected $themeId;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ . '/../../TestConfig.php'
        );

        parent::setUp();
    }


    public function testIndexActionWithoutAutomaticThemeAdd()
    {
        
        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);

        $service = $this->getMockBuilder('PlaygroundDesign\Service\Theme')
            ->setMethods(array('findActiveTheme', 'findThemeByAreaPackageAndBase'))
            ->disableOriginalConstructor()
            ->getMock();
        $serviceManager->setService('playgrounddesign_theme_service', $service);

        $return = array();

        $service->expects($this->any())
            ->method('findActiveTheme')
            ->will($this->returnValue($return));
        $service->expects($this->any())
            ->method('findThemeByAreaPackageAndBase')
            ->will($this->returnValue(array_push($return, new Theme)));

        $this->dispatch('/admin/theme');
        $this->assertModuleName('playgrounddesign');
        $this->assertControllerName('playgrounddesign\controller\ThemeAdmin');
        $this->assertControllerClass('ThemeAdminController');
        $this->assertActionName('list');
        $this->assertMatchedRouteName('admin/playgrounddesign_themeadmin');

    }

    public function testIndexActionWithAutomaticThemeAdd()
    {
        
        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);

        $service = $this->getMockBuilder('PlaygroundDesign\Service\Theme')
            ->setMethods(array('findActiveTheme', 'findThemeByAreaPackageAndBase', 'insert'))
            ->disableOriginalConstructor()
            ->getMock();
        $serviceManager->setService('playgrounddesign_theme_service', $service);

        $return = array();

        $service->expects($this->any())
            ->method('findActiveTheme')
            ->will($this->returnValue($return));
        $service->expects($this->any())
            ->method('findThemeByAreaPackageAndBase')
            ->will($this->returnValue($return));

        $this->dispatch('/admin/theme');
        $this->assertModuleName('playgrounddesign');
        $this->assertControllerName('playgrounddesign\controller\ThemeAdmin');
        $this->assertControllerClass('ThemeAdminController');
        $this->assertActionName('list');
        $this->assertMatchedRouteName('admin/playgrounddesign_themeadmin');

    }

    /*public function testEditAction() {

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);

        $form = $this->getMockBuilder('PlaygroundDesign\Form\Theme')
            ->setMethods(array('bind', 'prepare', 'get'))
            ->disableOriginalConstructor()
            ->getMock();
        $serviceManager->setService('playgrounddesign_theme_form', $form);

        $service = $this->getMockBuilder('PlaygroundDesign\Service\Theme')
            ->setMethods(array('findById'))
            ->disableOriginalConstructor()
            ->getMock();
        $serviceManager->setService('playgrounddesign_theme_service', $service);

        $id = 1;
        $theme = new Theme;
        $theme->setId($id);

        $service->expects($this->any())
            ->method('findById')
            ->will($this->returnValue($theme));

        $this->dispatch('/admin/theme/'.$id.'/update');
        $this->assertModuleName('playgrounddesign');
        $this->assertControllerName('playgrounddesign\controller\ThemeAdmin');
        $this->assertControllerClass('ThemeAdminController');
        $this->assertActionName('edit');
        $this->assertMatchedRouteName('admin/playgrounddesign_themeadmin_edit');
    }*/

    public function testDeleteAction()
    {
        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);

        $service = $this->getMockBuilder('PlaygroundDesign\Service\Theme')
            ->setMethods(array('findById', 'remove'))
            ->disableOriginalConstructor()
            ->getMock();
        $serviceManager->setService('playgrounddesign_theme_service', $service);

        $id = 1;
        $theme = new Theme;
        $theme->setId($id)
            ->setTitle("Ceci est un titre");

        $service->expects($this->any())
            ->method('findById')
            ->will($this->returnValue($theme));
        $service->expects($this->any())
            ->method('remove')
            ->will($this->returnValue(null));

        $this->dispatch('/admin/theme/'.$theme->getId().'/delete');
        $this->assertModuleName('playgrounddesign');
        $this->assertControllerName('playgrounddesign\controller\ThemeAdmin');
        $this->assertControllerClass('ThemeAdminController');
        $this->assertActionName('delete');
        $this->assertMatchedRouteName('admin/playgrounddesign_themeadmin_delete');

        $this->assertRedirectTo('/admin/theme');
    }

    public function testActivateAction()
    {
        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);

        $service = $this->getMockBuilder('PlaygroundDesign\Service\Theme')
            ->setMethods(array('findById', 'update', 'findActiveThemeByArea'))
            ->disableOriginalConstructor()
            ->getMock();
        $serviceManager->setService('playgrounddesign_theme_service', $service);

        $id = 1;
        $theme = new Theme;
        $theme->setId($id)
            ->setArea("ceciestunearea")
            ->setTitle("Ceci est un titre");

        $service->expects($this->any())
            ->method('findById')
            ->will($this->returnValue($theme));
        $service->expects($this->any())
            ->method('update')
            ->will($this->returnValue(null));
        $service->expects($this->any())
            ->method('findActiveThemeByArea')
            ->will($this->returnValue(array()));

        $this->dispatch('/admin/theme/'.$id.'/activate');
        $this->assertModuleName('playgrounddesign');
        $this->assertControllerName('playgrounddesign\controller\ThemeAdmin');
        $this->assertControllerClass('ThemeAdminController');
        $this->assertActionName('activate');
        $this->assertMatchedRouteName('admin/playgrounddesign_themeadmin_activate');

        $this->assertRedirectTo('/admin/theme');
    }

    public function testActivateActionWithAllreadyActivedTheme()
    {
        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);

        $service = $this->getMockBuilder('PlaygroundDesign\Service\Theme')
            ->setMethods(array('findById', 'update', 'findActiveThemeByArea'))
            ->disableOriginalConstructor()
            ->getMock();
        $serviceManager->setService('playgrounddesign_theme_service', $service);

        $id = 1;
        $theme = new Theme;
        $theme->setId($id)
            ->setArea("ceciestunearea")
            ->setTitle("Ceci est un titre");

        $service->expects($this->any())
            ->method('findById')
            ->will($this->returnValue($theme));
        $service->expects($this->any())
            ->method('update')
            ->will($this->returnValue(null));
        $service->expects($this->any())
            ->method('findActiveThemeByArea')
            ->will($this->returnValue(array($theme)));

        $this->dispatch('/admin/theme/'.$id.'/activate');
        $this->assertModuleName('playgrounddesign');
        $this->assertControllerName('playgrounddesign\controller\ThemeAdmin');
        $this->assertControllerClass('ThemeAdminController');
        $this->assertActionName('activate');
        $this->assertMatchedRouteName('admin/playgrounddesign_themeadmin_activate');

        $this->assertRedirectTo('/admin/theme');
    }
}
