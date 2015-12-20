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
        $manager = $this->getServiceLocator()->get('ModuleManager');
        $modules = $manager->getLoadedModules();

        return array('modules' => $modules);
    }
}
