<?php

namespace PlaygroundDesignTest\Controller\Frontend;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

use PlaygroundDesignTest\Bootstrap;
use PlaygroundDesign\Entity\Theme;

class DashboardControllerTest extends AbstractHttpControllerTestCase
{
    protected $traceError = true;

   /**
    * @var $themeMapper mapper de l'entity theme
    */
    protected $themeMapper;

    protected $themeId;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ . '/../../TestConfig.php'
        );

        parent::setUp();
    }

    public function testIndexAction()
    {
        $this->dispatch('/admin/theme');
        
        $this->assertModuleName('playgrounddesign');
        $this->assertControllerName('playgrounddesign\controller\ThemeAdmin');
        $this->assertControllerClass('ThemeAdminController');
        $this->assertActionName('list');
        $this->assertMatchedRouteName('admin/playgrounddesign_themeadmin');
    }

    public function testeditAction()
    {

        $theme = new Theme();
        $theme->setTitle('Theme 1');
        $theme->setImage('/theme/images/screenshots/1-Penguins.jpg');
        $theme->setType('admin');
        $theme->setPackage('default');
        $theme->setTheme('base');
        $theme->setAuthor('system');
        $this->getThemeMapper()->insert($theme);

        // Afin de pouvoir l'utiliser pour l'activation ou la suppression :)
        $this->themeId = $theme->getId();
        $this->dispatch('/admin/theme/'.$theme->getId().'/update');
        
        $this->assertModuleName('playgrounddesign');
        $this->assertControllerName('playgrounddesign\controller\ThemeAdmin');
        $this->assertControllerClass('ThemeAdminController');
        $this->assertActionName('edit');
        $this->assertMatchedRouteName('admin/playgrounddesign_themeadmin_edit');
    }

    public function testnewAction()
    {
        $this->dispatch('/admin/theme/new');
        
        $this->assertModuleName('playgrounddesign');
        $this->assertControllerName('playgrounddesign\controller\ThemeAdmin');
        $this->assertControllerClass('ThemeAdminController');
        $this->assertActionName('new');
        $this->assertMatchedRouteName('admin/playgrounddesign_themeadmin_new');
    }

    public function testactivateAction()
    {
        $themes = $this->getThemeMapper()->findAll();
        $theme = $themes[0];
        $this->assertEquals(0, $theme->getIsActive());

        $this->dispatch('/admin/theme/'.$theme->getId().'/activate');
        $this->assertModuleName('playgrounddesign');
        $this->assertControllerName('playgrounddesign\controller\ThemeAdmin');
        $this->assertControllerClass('ThemeAdminController');
        $this->assertActionName('activate');
        $this->assertMatchedRouteName('admin/playgrounddesign_themeadmin_activate');

        $this->getThemeMapper()->refresh($theme);
        $this->assertEquals(1, $theme->getIsActive());
    }

    public function testdeleteAction()
    {
        $themes = $this->getThemeMapper()->findAll();
        $count = sizeof($themes);
        $theme = $themes[0];

        $this->dispatch('/admin/theme/'.$theme->getId().'/delete');
        $this->assertModuleName('playgrounddesign');
        $this->assertControllerName('playgrounddesign\controller\ThemeAdmin');
        $this->assertControllerClass('ThemeAdminController');
        $this->assertActionName('delete');
        $this->assertMatchedRouteName('admin/playgrounddesign_themeadmin_delete');

        $themes = $this->getThemeMapper()->findAll();
        $this->assertEquals($count - 1 ,  sizeof($themes));
    }

    


    public function getThemeMapper()
    {

        if (null === $this->themeMapper) {
            $sm = Bootstrap::getServiceManager();
            $this->themeMapper = $sm->get('playgrounddesign_theme_mapper');
        }

        return $this->themeMapper;
    }
}
