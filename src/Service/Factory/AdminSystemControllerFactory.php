<?php
namespace PlaygroundDesign\Service\Factory;

use PlaygroundDesign\Controller\Admin\SystemController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AdminSystemControllerFactory implements FactoryInterface
{
    /**
    * @param ServiceLocatorInterface $locator
    * @return \PlaygroundDesign\Controller\Admin\SystemController
    */
    public function createService(ServiceLocatorInterface $locator)
    {
        $controller = new SystemController($locator);

        return $controller;
    }
}
