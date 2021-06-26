<?php

namespace PlaygroundDesign\Controller\Plugin;

use PlaygroundDesign\Controller\Plugin\AdminUrl;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Laminas\Mvc\Service\AbstractPluginManagerFactory;

class AdminUrlFactory extends AbstractPluginManagerFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        return new AdminUrl($container);
    }
}
