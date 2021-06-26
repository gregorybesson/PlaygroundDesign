<?php

namespace PlaygroundDesignTest\Entity;

use PlaygroundDesignTest\Bootstrap;
use \PlaygroundDesign\Entity\Theme as ThemeEntity;

class ThemeTest extends \PHPUnit\Framework\TestCase
{

    protected $themeData;

    protected function setUp(): void
    {
        $this->themeData = array(
            'title' => 'Theme 1',
            'image' => 'Ceciestuneimage',
            'area'  => 'admin',
            'package' => 'default',
            'theme' => 'base',
            'author' => 'troger',
            'is_active' => true,
        );

        parent::setUp();
    }

    public function testCreateAuthor()
    {
        $theme = new ThemeEntity;
        $theme->createAuthor();
        $this->assertEquals(ThemeEntity::AUTHOR, $theme->getAuthor());
    }

    // public function testCreateChrono()
    // {
    //     $theme = new ThemeEntity;
    //     $theme->createChrono();
    //     $this->assertEqualsWithDelta(new \DateTime("now"), $theme->getCreatedAt(), 1);
    //     $this->assertEqualsWithDelta(new \DateTime("now"), $theme->getUpdatedAt(), 1);
    // }

    public function testPopulate()
    {
        $theme = new ThemeEntity;
        $theme->populate($this->themeData);
        $this->assertEquals($this->themeData["title"], $theme->getTitle());
        $this->assertEquals($this->themeData["image"], $theme->getImage());
        $this->assertEquals($this->themeData["area"], $theme->getArea());
        $this->assertEquals($this->themeData["package"], $theme->getPackage());
        $this->assertEquals($this->themeData["theme"], $theme->getTheme());
        $this->assertEquals($this->themeData["author"], $theme->getAuthor());
        $this->assertEquals($this->themeData["is_active"], $theme->getIsActive());
    }

    public function testGetUrlBase()
    {
        $theme = new ThemeEntity;
        $theme->populate($this->themeData);
        $this->assertEquals(ThemeEntity::BASE.$this->themeData['area'].'/'.$this->themeData['package'].'/'.$this->themeData['theme'].'/', $theme->getUrlBase());
    }

    public function testGetFilePath()
    {
        $theme = new ThemeEntity;
        $theme->populate($this->themeData);
        $this->assertEquals($theme->getBasePath().$this->themeData['area'].'/'.$this->themeData['package'].'/'.$this->themeData['theme'].'/', $theme->getFilePath());
    }
}
