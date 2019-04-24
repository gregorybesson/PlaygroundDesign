<?php
namespace PlaygroundDesign\Service;

use PlaygroundDesign\Service\Settings;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class SettingsFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $service = new Settings($container);

        return $service;
    }
}
