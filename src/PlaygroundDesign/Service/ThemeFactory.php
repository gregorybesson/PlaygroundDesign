<?php
namespace PlaygroundDesign\Service;

use PlaygroundDesign\Service\Theme;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ThemeFactory implements FactoryInterface
{
    /**
    * @param ServiceLocatorInterface $locator
    * @return \PlaygroundDesign\Service\Theme
    */
    public function createService(ServiceLocatorInterface $locator)
    {
        $service = new Theme($locator);

        return $service;
    }
}
