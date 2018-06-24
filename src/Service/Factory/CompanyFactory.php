<?php
namespace PlaygroundDesign\Service\Factory;

use PlaygroundDesign\Service\Company;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class CompanyFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $service = new Company($container);

        return $service;
    }
}
