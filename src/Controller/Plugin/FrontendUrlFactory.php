<?php

namespace PlaygroundDesign\Controller\Plugin;

use PlaygroundDesign\Controller\Plugin\FrontendUrl;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Zend\Mvc\Service\AbstractPluginManagerFactory;

class FrontendUrlFactory extends AbstractPluginManagerFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        return new FrontendUrl($container);
    }
}
