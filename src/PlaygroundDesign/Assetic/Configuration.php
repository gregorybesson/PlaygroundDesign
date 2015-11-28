<?php
namespace PlaygroundDesign\Assetic;

use Zend\Stdlib;
use AsseticBundle\Exception;

class Configuration extends \AsseticBundle\Configuration
{

    /**
     * Adding possibility to create routes based also on parameters (game id ...)
     * @see \AsseticBundle\Configuration::getRoute()
     */
    public function getRoute($name, $params = null, $default = null)
    {
        $assets = array();
        $routeMatched = false;
        
        $routes = $this->routes;
        $customs = isset($routes['custom'])?$routes['custom']:array();
        unset($routes['custom']);

        // Merge all assets configuration for which regular expression matches route
        foreach ($routes as $spec => $config) {
            if (preg_match('(^' . $spec . '$)i', $name)) {
                $routeMatched = true;
                $assets = Stdlib\ArrayUtils::merge($assets, (array) $config);
            }
        }
        
        // Merge all assets configuration for custom routes (limited to parameters of a route)
        foreach ($customs as $k => $custom) {
            foreach ($custom['routes'] as $spec => $config) {
                if (preg_match('(^' . $spec . '$)i', $name)) {
                    if (isset($custom['params'])) {
                        if ($params) {
                            unset($params['action']);
                            unset($params['controller']);
                            // If the game theme don't cascade from the original theme
                            if (isset($custom['params']['cascade']) && $custom['params']['cascade'] == false) {
                                $assets = array();
                                unset($custom['params']['cascade']);
                            }
                            if (count(array_diff_assoc($custom['params'], $params)) == 0) {
                                unset($custom['params']);
                                $routeMatched = true;
                                $assets = Stdlib\ArrayUtils::merge($assets, (array) $config);
                            }
                        }
                    } else {
                        $routeMatched = true;
                        $assets = Stdlib\ArrayUtils::merge($assets, (array) $config);
                    }
                }
            }
        }
        
        // Only return default if none regular expression matched
        return $routeMatched ? $assets : $default;
    }
}
