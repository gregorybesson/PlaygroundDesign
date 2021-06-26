<?php
namespace PlaygroundDesign\Service\Factory;

use PlaygroundDesign\Controller\Admin\DashboardController;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class AdminDashboardControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $controller = new DashboardController($container);

        return $controller;
    }
}
