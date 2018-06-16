<?php
namespace PlaygroundDesign\Service\Factory;

use PlaygroundDesign\Controller\Admin\ThemeController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AdminThemeControllerFactory implements FactoryInterface
{
    /**
    * @param ServiceLocatorInterface $locator
    * @return \PlaygroundDesign\Controller\Admin\ThemeController
    */
    public function createService(ServiceLocatorInterface $locator)
    {
        $controller = new ThemeController($locator);

        return $controller;
    }
}
