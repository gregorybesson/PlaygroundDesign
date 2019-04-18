<?php

namespace PlaygroundDesign\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\Url;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Traversable;
use Zend\EventManager\EventInterface;
use Zend\Mvc\Exception;
use Zend\Mvc\InjectApplicationEventInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteStackInterface;

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
            throw new \Zend\Mvc\Exception\DomainException('Url plugin requires a controller that implements InjectApplicationEventInterface');
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
