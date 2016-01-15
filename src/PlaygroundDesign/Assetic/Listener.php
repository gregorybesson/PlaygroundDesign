<?php
namespace PlaygroundDesign\Assetic;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\CallbackHandler;

class Listener extends \AsseticBundle\Listener implements ListenerAggregateInterface
{

    public function renderAssets(MvcEvent $e)
    {
        $sm     = $e->getApplication()->getServiceManager();
        /** @var Configuration $config */
        $config = $sm->get('AsseticConfiguration');
        if ($e->getName() === MvcEvent::EVENT_DISPATCH_ERROR) {
            $error = $e->getError();
            if ($error && !in_array($error, $config->getAcceptableErrors())) {
                // break if not an acceptable error
                return;
            }
        }

        $response = $e->getResponse();
        if (!$response) {
            $response = new Response();
            $e->setResponse($response);
        }

        /** @var $asseticService \AsseticBundle\Service */
        $asseticService = $sm->get('AsseticService');

        // setup service if a matched route exist
        $router = $e->getRouteMatch();

        if ($router) {
            $asseticService->setRouteName($router->getMatchedRouteName());
            $asseticService->setControllerName($router->getParam('controller'));
            $asseticService->setActionName($router->getParam('action'));
            $asseticService->setParams($router->getParams());
        } else {
            $asseticService->setRouteName('error_404');
            $asseticService->setControllerName('Application\Controller\Index');
            $asseticService->setActionName('error_' . uniqid());
        }

        // Create all objects
        $asseticService->build();

        // Init assets for modules
        $asseticService->setupRenderer($sm->get('ViewRenderer'));
    }
}
