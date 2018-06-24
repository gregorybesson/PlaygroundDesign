<?php
namespace PlaygroundDesign\View\Helper;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use PlaygroundDesign\View\Helper\RouteMatchWidget;

class RouteMatchWidgetFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $router = $container->get('router');
        $request = $container->get('request');

        return new RouteMatchWidget($router, $request);
    }

}
