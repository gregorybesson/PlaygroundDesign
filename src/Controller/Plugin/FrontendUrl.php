<?php

namespace PlaygroundDesign\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\Url;
use Laminas\ServiceManager\ServiceLocatorAwareInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Traversable;
use Laminas\EventManager\EventInterface;
use Laminas\Mvc\Exception;
use Laminas\Mvc\InjectApplicationEventInterface;
use Laminas\Mvc\ModuleRouteListener;
use Laminas\Mvc\MvcEvent;
use Laminas\Mvc\Router\RouteStackInterface;

class FrontendUrl extends Url
{
    /**
     *
     *
     * @param string $longUrl
     */
    public function fromRoute($route = null, $params = array(), $options = array(), $reuseMatchedParams = true)
    {
        $controller = $this->getController();
        if (!$controller instanceof InjectApplicationEventInterface) {
            throw new \Laminas\Mvc\Exception\DomainException('Url plugin requires a controller that implements InjectApplicationEventInterface');
        }
        
        if (!is_array($params)) {
            if (!$params instanceof Traversable) {
                throw new Exception\InvalidArgumentException(
                    'Params is expected to be an array or a Traversable object'
                );
            }
            $params = iterator_to_array($params);
        }

        // this parameter will be used only when $config['playgroundLocale'] is enabled
        if (!isset($params['locale'])) {
            $params['locale'] = $controller->getServiceLocator()->get('MvcTranslator')->getLocale();
        }
        
        $event   = $controller->getEvent();
        $matches = null;
        if ($event instanceof MvcEvent) {
            $matches = $event->getRouteMatch();
        } elseif ($event instanceof EventInterface) {
            $matches = $event->getParam('route-match', false);
        }
        
        if ($matches && $matches->getParam('area')) {
            if ($route && ltrim($route, '/') !== '') {
                $route = $matches->getParam('area').'/'.ltrim($route, '/');
            } else {
                $route = $matches->getParam('area');
            }
        }
        
        $link = parent::fromRoute($route, $params, $options, $reuseMatchedParams);

        return $link;
    }
}
