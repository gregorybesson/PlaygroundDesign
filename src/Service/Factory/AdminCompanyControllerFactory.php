<?php
namespace PlaygroundDesign\Service\Factory;

use PlaygroundDesign\Controller\Admin\CompanyController;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class AdminCompanyControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $controller = new CompanyController($container);

        return $controller;
    }
}
