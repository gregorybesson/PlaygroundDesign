<?php
namespace PlaygroundDesign\Service\Factory;

use PlaygroundDesign\Controller\Admin\ThemeController;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class AdminThemeControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $controller = new ThemeController($container);

        return $controller;
    }
}
