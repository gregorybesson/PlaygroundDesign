<?php

namespace PlaygroundDesginTest\Service;

use PlaygroundDesignTest\Bootstrap;
use \PlaygroundDesign\Entity\Theme as ThemeEntity;

class ThemeTest extends \PHPUnit_Framework_TestCase
{
    protected $traceError = true;

    public function setUp()
    {
        parent::setUp();
    }

    public function testCreateTitleFalse()
    {
        $service = new \PlaygroundDesign\Service\Theme();
        $data = array("title" => "");
        $this->assertFalse($service->create($data, "playgrounddesign_theme_form"));
    }

    public function testEditTitleFalse()
    {
        $service = new \PlaygroundDesign\Service\Theme();
        $data = array("title" => "");
        $this->assertFalse($service->edit($data, new ThemeEntity, "playgrounddesign_theme_form"));
    }


    public function testCheckDirectoryThemeFalse()
    {
        $theme = new ThemeEntity();
        $data = array('area' => 'toto',
                      'package' => 'tata',
                      'theme' => 'titi');

        $ts = new \PlaygroundDesign\Service\Theme();
        $this->assertFalse($ts->checkDirectoryTheme($theme, $data));
    }

    public function testCheckDirectoryThemeTrue()
    {
        $theme = new ThemeEntity();
        $data = array('area' => 'frontend',
                      'package' => 'default',
                      'theme' => 'base');

        $ts = new \PlaygroundDesign\Service\Theme();
        $this->assertTrue($ts->checkDirectoryTheme($theme, $data));
    }

    public function testStaticFiles()
    {
        $this->assertEquals(array('assets.php', 'layout.php', 'theme.php'), \PlaygroundDesign\Service\Theme::$files);
    }

    public function testCreateFiles()
    {
        $data = array("area" => "Ceci", "package" => "Est", "theme" => "bidon", "title" => "Toto");
        $ts = new \PlaygroundDesign\Service\Theme();
        $theme = new ThemeEntity();
        mkdir($theme->getBasePath().'/'.$data['area'].'/'.$data['package'].'/'.$data['theme'], 0777, true);
        $ts->createFiles($theme, $data);
        foreach (\PlaygroundDesign\Service\Theme::$files as $file) {
            $this->assertTrue(file_exists($theme->getBasePath().$data['area'].'/'.$data['package'].'/'.$data['theme'].'/'.$file));
            unlink($theme->getBasePath().$data['area'].'/'.$data['package'].'/'.$data['theme'].'/'.$file);
        }
        rmdir($theme->getBasePath().$data['area'].'/'.$data['package'].'/'.$data['theme']);
        rmdir($theme->getBasePath().$data['area'].'/'.$data['package']);
        rmdir($theme->getBasePath().$data['area']);
    }

    public function testCreateExistFiles()
    {
        $data = array("area" => "Ceci", "package" => "Est", "theme" => "bidon", "title" => "Toto");
        $ts = new \PlaygroundDesign\Service\Theme();
        $theme = new ThemeEntity();
        mkdir($theme->getBasePath().'/'.$data['area'].'/'.$data['package'].'/'.$data['theme'], 0777, true);
        file_put_contents($theme->getBasePath().$data['area'].'/'.$data['package'].'/'.$data['theme'].'/assets.php', 'null');
        $ts->createFiles($theme, $data);
        foreach (\PlaygroundDesign\Service\Theme::$files as $file) {
            $this->assertTrue(file_exists($theme->getBasePath().$data['area'].'/'.$data['package'].'/'.$data['theme'].'/'.$file));
            unlink($theme->getBasePath().$data['area'].'/'.$data['package'].'/'.$data['theme'].'/'.$file);
        }
        rmdir($theme->getBasePath().$data['area'].'/'.$data['package'].'/'.$data['theme']);
        rmdir($theme->getBasePath().$data['area'].'/'.$data['package']);
        rmdir($theme->getBasePath().$data['area']);
    }

    public function testUploadImageFalse()
    {
        $data = array();
        $theme = new ThemeEntity();
        $ts = new \PlaygroundDesign\Service\Theme();
        $this->assertEquals($theme, $ts->uploadImage($theme, $data));
    }

    public function testUploadImageTrue() 
    {
        $fileName = "/CeciEstBidon.tmp";
        file_put_contents($fileName, 'test');
        $data = array("area" => "Ceci", "package" => "Est", "theme" => "bidon", "title" => "Toto","uploadImage" => array("tmp_name" => $fileName, "name" => "CeciEstBidon"));
        
        $theme = new ThemeEntity();
        $theme->setId('12');
        $ts = new \PlaygroundDesign\Service\Theme();
        $ts->setServiceManager(Bootstrap::getServiceManager());
        $theme = $ts->uploadImage($theme, $data);
        $this->assertEquals('/theme/images/screenshots/12-CeciEstBidon', $theme->getImage());       
        
        rmdir( $ts->getOptions()->getMediaPath() . $data['area'] . DIRECTORY_SEPARATOR . $data['package'] . DIRECTORY_SEPARATOR . $data['theme'] . '/assets/images/screenshots/');
        rmdir( $ts->getOptions()->getMediaPath() . $data['area'] . DIRECTORY_SEPARATOR . $data['package'] . DIRECTORY_SEPARATOR . $data['theme'] . '/assets/images/');
        rmdir( $ts->getOptions()->getMediaPath() . $data['area'] . DIRECTORY_SEPARATOR . $data['package'] . DIRECTORY_SEPARATOR . $data['theme'] . '/assets/');
        rmdir( $ts->getOptions()->getMediaPath() . $data['area'] . DIRECTORY_SEPARATOR . $data['package'] . DIRECTORY_SEPARATOR . $data['theme'] . '/');
        rmdir( $ts->getOptions()->getMediaPath() . $data['area'] . DIRECTORY_SEPARATOR . $data['package'] . DIRECTORY_SEPARATOR);
        rmdir( $ts->getOptions()->getMediaPath() . $data['area'] . DIRECTORY_SEPARATOR);
        rmdir( $ts->getOptions()->getMediaPath());
    }

    public function testCreateTrue()
    {

        $service = new \PlaygroundDesign\Service\Theme();
        $data = array("area" => "Ceci", "package" => "Est", "theme" => "bidon", "title" => "Toto");
        $service->setServiceManager(Bootstrap::getServiceManager());

        $themePostUpdate = new ThemeEntity;
        $themePostUpdate->setTitle($data['title']);

        $service->setServiceManager(Bootstrap::getServiceManager());
        $mapper = $this->getMockBuilder('PlaygroundDesign\Mapper\Theme')
            ->disableOriginalConstructor()
            ->getMock();
        $mapper->expects($this->any())
            ->method('insert')
            ->will($this->returnValue($themePostUpdate));
        $mapper->expects($this->any())
            ->method('update')
            ->will($this->returnValue($themePostUpdate));
        $service->setThemeMapper($mapper);

        $theme = $service->create($data, "playgrounddesign_theme_form");
        $this->assertEquals($data['title'], $themePostUpdate->getTitle());

        foreach (\PlaygroundDesign\Service\Theme::$files as $file) {
            $this->assertTrue(file_exists($theme->getBasePath().$data['area'].'/'.$data['package'].'/'.$data['theme'].'/'.$file));
            unlink($theme->getBasePath().$data['area'].'/'.$data['package'].'/'.$data['theme'].'/'.$file);
        }
        rmdir($theme->getBasePath().$data['area'].'/'.$data['package'].'/'.$data['theme']);
        rmdir($theme->getBasePath().$data['area'].'/'.$data['package']);
        rmdir($theme->getBasePath().$data['area']);
    }

    public function testEditFalse()
    {

        $service = new \PlaygroundDesign\Service\Theme();
        $data = array("area" => "admin", "package" => "default", "theme" => "base", "title" => "Toto2");
        $theme = new ThemeEntity();

        $themePostUpdate = new ThemeEntity;
        $themePostUpdate->setTitle($data['title']);

        $service->setServiceManager(Bootstrap::getServiceManager());
        $mapper = $this->getMockBuilder('PlaygroundDesign\Mapper\Theme')
            ->disableOriginalConstructor()
            ->getMock();
        $mapper->expects($this->any())
            ->method('update')
            ->will($this->returnValue(new ThemeEntity));
        $service->setThemeMapper($mapper);

        $theme = $service->edit($data, $theme, "playgrounddesign_theme_form");
        $this->assertEquals($data['title'], $themePostUpdate->getTitle());
    } 

    public function testSetMapper()
    {
        
        $service = new \PlaygroundDesign\Service\Theme();
        $service->setServiceManager(Bootstrap::getServiceManager());
        $mapper = $this->getMockBuilder('PlaygroundDesign\Mapper\Theme')
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertEquals($service, $service->setThemeMapper($mapper));
    }

    public function testFindById() 
    {
        $service = new \PlaygroundDesign\Service\Theme();
        $service->setServiceManager(Bootstrap::getServiceManager());

        $theme = new ThemeEntity();

        $mapper = $this->getMockBuilder('PlaygroundDesign\Mapper\Theme')
            ->disableOriginalConstructor()
            ->getMock();
        $mapper->expects($this->any())
            ->method('findById')
            ->will($this->returnValue($theme));
        $service->setThemeMapper($mapper);

        $this->assertEquals($theme, $service->findById(1));
    }

    public function testInsert() 
    {
        $service = new \PlaygroundDesign\Service\Theme();
        $service->setServiceManager(Bootstrap::getServiceManager());

        $theme = new ThemeEntity();

        $mapper = $this->getMockBuilder('PlaygroundDesign\Mapper\Theme')
            ->disableOriginalConstructor()
            ->getMock();
        $mapper->expects($this->any())
            ->method('insert')
            ->will($this->returnValue($theme));
        $service->setThemeMapper($mapper);

        $this->assertEquals($theme, $service->insert($theme));
    }

    public function testUpdate() 
    {
        $service = new \PlaygroundDesign\Service\Theme();
        $service->setServiceManager(Bootstrap::getServiceManager());

        $theme = new ThemeEntity();

        $mapper = $this->getMockBuilder('PlaygroundDesign\Mapper\Theme')
            ->disableOriginalConstructor()
            ->getMock();
        $mapper->expects($this->any())
            ->method('update')
            ->will($this->returnValue($theme));
        $service->setThemeMapper($mapper);

        $this->assertEquals($theme, $service->update($theme));
    }

    public function testFindActiveTheme() 
    {
        $service = new \PlaygroundDesign\Service\Theme();
        $service->setServiceManager(Bootstrap::getServiceManager());

        $theme = new ThemeEntity();

        $mapper = $this->getMockBuilder('PlaygroundDesign\Mapper\Theme')
            ->disableOriginalConstructor()
            ->getMock();
        $mapper->expects($this->any())
            ->method('findActiveTheme')
            ->will($this->returnValue($theme));
        $service->setThemeMapper($mapper);

        $this->assertEquals($theme, $service->findActiveTheme());
        $this->assertEquals($theme, $service->findActiveTheme(false));
    }

    public function testFindActiveThemeByArea() 
    {
        $service = new \PlaygroundDesign\Service\Theme();
        $service->setServiceManager(Bootstrap::getServiceManager());

        $theme = new ThemeEntity();

        $mapper = $this->getMockBuilder('PlaygroundDesign\Mapper\Theme')
            ->disableOriginalConstructor()
            ->getMock();
        $mapper->expects($this->any())
            ->method('findActiveThemeByArea')
            ->will($this->returnValue($theme));
        $service->setThemeMapper($mapper);

        $this->assertEquals($theme, $service->findActiveThemeByArea("area"));
        $this->assertEquals($theme, $service->findActiveThemeByArea("area", false));
    }

    public function testFindThemeByAreaPackageAndBase() 
    {
        $service = new \PlaygroundDesign\Service\Theme();
        $service->setServiceManager(Bootstrap::getServiceManager());

        $theme = new ThemeEntity();

        $mapper = $this->getMockBuilder('PlaygroundDesign\Mapper\Theme')
            ->disableOriginalConstructor()
            ->getMock();
        $mapper->expects($this->any())
            ->method('findThemeByAreaPackageAndBase')
            ->will($this->returnValue($theme));
        $service->setThemeMapper($mapper);

        $this->assertEquals($theme, $service->findThemeByAreaPackageAndBase("area", "package", "base"));
    }

}
