<?php
namespace PlaygroundDesign\Service\Factory;

use PlaygroundDesign\Service\Company;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CompanyFactory implements FactoryInterface
{
    /**
    * @param ServiceLocatorInterface $locator
    * @return \PlaygroundDesign\Service\Company
    */
    public function createService(ServiceLocatorInterface $locator)
    {
        $service = new Company($locator);

        return $service;
    }
}
