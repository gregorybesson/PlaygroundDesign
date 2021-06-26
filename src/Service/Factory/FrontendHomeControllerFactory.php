<?php
namespace PlaygroundDesign\Service\Factory;

use PlaygroundDesign\Controller\Frontend\HomeController;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class FrontendHomeControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $controller = new HomeController($container);

        return $controller;
    }
}
