<?php
namespace PlaygroundDesign\Service\Factory;

use PlaygroundDesign\Controller\Admin\CompanyController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AdminCompanyControllerFactory implements FactoryInterface
{
    /**
    * @param ServiceLocatorInterface $locator
    * @return \PlaygroundDesign\Controller\Admin\CompanyController
    */
    public function createService(ServiceLocatorInterface $locator)
    {
        $controller = new CompanyController($locator);

        return $controller;
    }
}
