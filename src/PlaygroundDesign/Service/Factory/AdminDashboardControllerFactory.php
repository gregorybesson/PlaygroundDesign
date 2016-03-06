<?php
namespace PlaygroundDesign\Service\Factory;

use PlaygroundDesign\Controller\Admin\DashboardController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AdminDashboardControllerFactory implements FactoryInterface
{
    /**
    * @param ServiceLocatorInterface $locator
    * @return \PlaygroundDesign\Controller\Admin\DashboardController
    */
    public function createService(ServiceLocatorInterface $locator)
    {
        $controller = new DashboardController($locator);

        return $controller;
    }
}
