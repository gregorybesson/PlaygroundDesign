<?php
namespace PlaygroundDesign\Assetic;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class ServiceFactory extends \AsseticBundle\ServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $asseticConfig = $container->get('AsseticConfiguration');
        if ($asseticConfig->detectBaseUrl()) {
            /** @var $request \Zend\Http\PhpEnvironment\Request */
            $request = $container->get('Request');
            if (method_exists($request, 'getBaseUrl')) {
                $asseticConfig->setBaseUrl($request->getBaseUrl());
            }
        }

        $asseticService = new Service($asseticConfig);
        $asseticService->setAssetManager($container->get('Assetic\AssetManager'));
        $asseticService->setAssetWriter($container->get('Assetic\AssetWriter'));
        $asseticService->setFilterManager($container->get('AsseticBundle\FilterManager'));

        // Cache buster is not mandatory
        if ($container->has('AsseticCacheBuster')) {
            $asseticService->setCacheBusterStrategy($container->get('AsseticCacheBuster'));
        }

        return $asseticService;
    }
}
