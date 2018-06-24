<?php
namespace PlaygroundDesign\Service\Factory;

use PlaygroundDesign\Controller\Admin\SystemController;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class AdminSystemControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $controller = new SystemController($container);

        return $controller;
    }
}
