<?php

namespace PlaygroundDesginTest\Mapper;

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
        $this->tm = $this->sm->get('playgrounddesign_theme_mapper');
        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $classes = $this->em->getMetadataFactory()->getAllMetadata();
        $tool->dropSchema($classes);
        $tool->createSchema($classes);

        $this->themeData = array(
            'title' => 'Theme 1',
            'area'  => 'admin',
            'package' => 'default',
            'theme' => 'base',
            'author' => 'troger',
            'is_active' => true,
        );


        parent::setUp();
    }

     public function testCanInsertNewRecord()
    {
        $theme = new ThemeEntity();
        $theme->populate($this->themeData);
        // save data
        $this->tm->persist($theme);
        $this->tm->refresh($theme);

        $this->assertEquals($this->themeData['title'], $theme->getTitle());
        $this->assertEquals(0, $theme->getIsActive());

        return $theme->getId();
    }


  
    public function testFindAll()
    {   
        $theme = new ThemeEntity();
        $theme->populate($this->themeData);
        // save data
        $this->tm->persist($theme);
        $this->tm->refresh($theme);

        $themes = $this->tm->findAll();
        $this->assertEquals("array", gettype($themes));
        $this->assertEquals("1", sizeof($themes));
    }

    public function testFindBy()
    {   
        $theme = new ThemeEntity();
        $theme->populate($this->themeData);
        // save data
        $this->tm->insert($theme);
        $this->tm->refresh($theme);

        $themes = $this->tm->findBy(array('author' => 'troger'));
        $this->assertEquals("array", gettype($themes));
        $this->assertEquals("1", sizeof($themes));
    }

    /**
     * @depends testCanInsertNewRecord
     */
    public function testFindById($id)
    {
        $theme = $this->tm->findById($id);
        $this->assertEquals("object", gettype($theme));
        $this->assertEquals("PlaygroundDesign\Entity\Theme", get_class($theme));
        $this->assertEquals($id, $theme->getId());
    }

    /*public function testFindActiveTheme()
    {   
        $themes = $this->tm->findAll();
 var_dump($themes);
        $theme = $this->tm->getEntityRepository()->find($id);

        $theme->setIsActive(true);
      
        $theme = $this->tm->update($theme);
        var_dump($theme);
        $themes = $this->tm->findAll();
 var_dump($themes);
        $themes = $this->tm->findActiveTheme();

        $this->assertEquals("array", gettype($themes));
        $this->assertEquals("1", sizeof($themes));
    }*/

    /*public function testFindActiveThemeByArea()
    {
        $theme = new ThemeEntity();
        $theme->populate($this->themeData);
        $theme->setIsActive(true);
        var_dump($theme);

        $theme = $this->tm->insert($theme);
        var_dump($theme);

        $theme->setIsActive(true);
        var_dump($theme);

        $theme = $this->tm->update($theme);
        var_dump($theme);

        $this->tm->refresh($theme);
        var_dump($theme);

        $themes = $this->tm->findById(1);
        var_dump($themes);
        $themes = $this->tm->findActiveThemeByArea("admin");
        $this->assertEquals("array", gettype($themes));
        $this->assertEquals("1", sizeof($themes));
    }*/

    /**
     * @depends testCanInsertNewRecord
     */
    public function testCanUpdateInsertedRecord($id)
    {
        $data = array(
            'id' => $id
        );
        $theme = $this->tm->getEntityRepository()->find($id);
        $this->assertInstanceOf('PlaygroundDesign\Entity\Theme', $theme);
        $this->assertEquals($this->themeData['title'], $theme->getTitle());

        $theme->populate($data);
        $this->tm->update($theme);

        $this->tm->refresh($theme);

        $this->assertEquals($this->themeData['title'], $theme->getTitle());
        $this->assertEquals($this->themeData['area'], $theme->getArea());
        $this->assertEquals($this->themeData['package'], $theme->getPackage());
        $this->assertEquals($this->themeData['theme'], $theme->getTheme());
        $this->assertEquals($this->themeData['author'], $theme->getAuthor());

    }

   

    /**
     * @depends testCanInsertNewRecord
     */
    public function testCanRemoveInsertedRecord($id)
    {
        $theme = $this->tm->getEntityRepository()->find($id);
        $this->assertInstanceOf('PlaygroundDesign\Entity\Theme', $theme);

        $this->tm->remove($theme);
        $this->em->flush();

        $theme = $this->tm->getEntityRepository()->find($id);
        $this->assertEquals(false, $theme);
    }

    public function tearDown()
    {
        $dbh = $this->em->getConnection();
        unset($this->tm);
        unset($this->sm);
        unset($this->em);
        parent::tearDown();
    }


}