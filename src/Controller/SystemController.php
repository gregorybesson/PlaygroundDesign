<?php

namespace PlaygroundDesign\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

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
