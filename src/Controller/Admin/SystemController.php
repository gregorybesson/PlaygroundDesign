<?php

namespace PlaygroundDesign\Controller\Admin;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorInterface;

class SystemController extends AbstractActionController
{
    /**
     *
     * @var ServiceManager
     */
    protected $serviceLocator;

    public function __construct(ServiceLocatorInterface $locator)
    {
        $this->serviceLocator = $locator;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
    
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
