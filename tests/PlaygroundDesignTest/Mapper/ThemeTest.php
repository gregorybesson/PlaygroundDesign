<?php

namespace PlaygroundDesginTest\Maper;

use PlaygroundDesignTest\Bootstrap;
use \PlaygroundDesign\Entity\Theme as ThemeEntity;

class ThemeTest extends \PHPUnit_Framework_TestCase
{
    protected $traceError = true;

    protected $themeData;

    protected $theme;

    protected $themeMapper;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->em = $this->sm->get('doctrine.entitymanager.orm_default');
        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $classes = $this->em->getMetadataFactory()->getAllMetadata();
        $tool->dropSchema($classes);
        $tool->createSchema($classes);

        $this->themeData = array(
            'title' => 'Theme 1',
            'image' => '/theme/images/screenshots/1-Penguins.jpg',
            'type'  => 'admin',
            'package' => 'default',
            'theme' => 'base',
            'author' => 'troger',
        );
        $theme = new ThemeEntity();
        $theme->populate($this->themeData);
        $this->getThemeMapper()->insert($theme);
        $this->theme = $theme;

        parent::setUp();
    }

    public function testInsert()
    {
        $theme = new ThemeEntity();
        $theme->populate($this->themeData);
        $this->getThemeMapper()->insert($theme);

        $this->assertEquals($this->themeData['title'], $theme->getTitle());
        $this->assertEquals($this->themeData['author'], $theme->getAuthor());

        unset($this->themeData['author']);

        $theme = new ThemeEntity();
        $theme->populate($this->themeData);
        $this->getThemeMapper()->insert($theme);

        $this->theme = $theme;
        $this->assertEquals($this->themeData['title'], $theme->getTitle());
        $this->assertEquals(ThemeEntity::AUTHOR, $theme->getAuthor());

    }


    public function testUpdate()
    {
        $newTitle = "Title 2";
        $theme = $this->theme;
        $this->assertEquals($this->themeData['title'], $theme->getTitle());
        $theme->setTitle($newTitle);
        $theme = $this->getThemeMapper()->update($theme);
        $this->assertEquals($newTitle, $theme->getTitle());

    }

    public function testFindAll()
    {
        $theme = new ThemeEntity();
        $theme->populate($this->themeData);
        $this->getThemeMapper()->insert($theme);

        $themes = $this->getThemeMapper()->findAll();
        $this->assertEquals("array", gettype($themes));
        $this->assertEquals("2", sizeof($themes));

    }

    public function testFindBy()
    {
        $themes = $this->getThemeMapper()->findBy(array('author' => 'troger'));
        $this->assertEquals("array", gettype($themes));
        $this->assertEquals("1", sizeof($themes));

    }

    public function testFindById()
    {
        $theme = new ThemeEntity();
        $theme->populate($this->themeData);
        $theme = $this->getThemeMapper()->insert($theme);
        $themes = $this->getThemeMapper()->findById($theme->getId());
        $this->assertEquals("object", gettype($themes));
        $this->assertEquals("PlaygroundDesign\Entity\Theme", get_class($themes));
        $this->assertEquals($theme->getId(), $themes->getId());

    }

    public function testRemove()
    {
        $theme = new ThemeEntity();
        $theme->populate($this->themeData);
        $theme = $this->getThemeMapper()->insert($theme);
        $themes = $this->getThemeMapper()->findAll();
        $this->assertEquals("2", sizeof($themes));
        $this->getThemeMapper()->remove($theme);
        $themes = $this->getThemeMapper()->findAll();
        $this->assertEquals("1", sizeof($themes));

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
