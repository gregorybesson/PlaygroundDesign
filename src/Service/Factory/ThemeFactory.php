<?php
namespace PlaygroundDesign\Service\Factory;

use PlaygroundDesign\Service\Theme;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class ThemeFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $service = new Theme($container);

        return $service;
    }
}
