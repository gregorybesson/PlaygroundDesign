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

    public function testCreateFalse()
    {
        $data = array('area' => 'frontend',
                      'package' => 'default',
                      'theme' => 'base',
                      'title' => '');

        $ts = new \PlaygroundDesign\Service\Theme();
        $ts->setServiceManager(Bootstrap::getServiceManager());
        $this->assertFalse($ts->create($data, "playgrounddesign_theme_form"));


        $data = array('area' => 'frontend',
                     'package' => 'default',
                     'theme' => 'bas',
                     'title' => 'test');

        $ts = new \PlaygroundDesign\Service\Theme();
        $ts->setServiceManager(Bootstrap::getServiceManager());
        $this->assertFalse($ts->create($data, "playgrounddesign_theme_form"));
    }


    public function testEditFalse()
    {
        $data = array('area' => 'frontend',
                      'package' => 'default',
                      'theme' => 'base',
                      'title' => '');

        $ts = new \PlaygroundDesign\Service\Theme();
        $ts->setServiceManager(Bootstrap::getServiceManager());
        $this->assertFalse($ts->create($data, "playgrounddesign_theme_form"));


        $data = array('area' => 'frontend',
                      'package' => 'default',
                      'theme' => 'bas',
                      'title' => 'test');

        $ts = new \PlaygroundDesign\Service\Theme();
        $ts->setServiceManager(Bootstrap::getServiceManager());
        $this->assertFalse($ts->create($data, "playgrounddesign_theme_form"));
    }

}
