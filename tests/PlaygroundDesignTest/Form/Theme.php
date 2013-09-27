<?php

namespace PlaygroundDesginTest\Maper;

use PlaygroundDesignTest\Bootstrap;
use \PlaygroundDesign\Entity\Theme as ThemeEntity;

class ThemeTest extends \PHPUnit_Framework_TestCase
{
    protected $traceError = true;
    protected $form;

    public function setUp()
    {
        parent::setUp();
    }

    public function testValid()
    {
        $data = array('title' => '');
        $this->form->setData($data);
        $this->assertFalse($this->form->isValid());


        $data = array('title' => 'test');
        $this->form->setData($data);
        $this->assertTrue($this->form->isValid());
    }

    public function getForm() 
    {
        if (null === $this->form) {
            $sm = Bootstrap::getServiceManager();
            $this->themeMapper = $sm->get('playgrounddesign_theme_form');
        }

        return $this->form;
    }


}
