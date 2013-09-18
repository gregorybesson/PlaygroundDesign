<?php

namespace PlaygroundDesign\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class SystemController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }

    public function settingsAction()
    {
        return new ViewModel();
    }

    public function modulesAction()
    {
        $modules = Module::getLoadedModules();

        return array('modules' => $modules);
    }
}
