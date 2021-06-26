<?php

namespace PlaygroundDesignTest\Maper;

use PlaygroundDesignTest\Bootstrap;
use \PlaygroundDesign\Entity\Theme as ThemeEntity;

class ThemeTest extends \PHPUnit\Framework\TestCase
{
    protected $traceError = true;
    protected $form;

    protected function setUp(): void
    {
        parent::setUp();
        $this->getForm();
    }

    public function testValid()
    {
        $data = array('title' => 'test');
        $this->form->setData($data);
        $this->assertTrue($this->form->isValid());
    }

    public function getForm()
    {
        if (null === $this->form) {
            $sm = Bootstrap::getServiceManager();
            $this->form = $sm->get('playgrounddesign_theme_form');
        }

        return $this->form;
    }
}
