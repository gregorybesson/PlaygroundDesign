<?php
namespace PlaygroundDesign\Assetic;

use Zend\Stdlib;
use AsseticBundle\Exception;

class Configuration extends \AsseticBundle\Configuration
{

    /**
     * Adding possibility to create routes based also on parameters (channel, game id ...)
     * @see \AsseticBundle\Configuration::getRoute()
     */
    public function getRoute($name, $params = null, $default = null)
    {
        $assets = array();
        $routeMatched = false;

        // Merge all assets configuration for which regular expression matches route
        foreach ($this->routes as $spec => $config) {
            if (preg_match('(^' . $spec . '$)i', $name)) {
                if (isset($config['params'])) {
                    if($params){
                        if (isset($config['params']) && count(array_diff_assoc($config['params'], $params )) == 0){
                            unset($config['params']);
                            $routeMatched = true;
                            $assets = Stdlib\ArrayUtils::merge($assets, (array) $config);
                        }
                    }
                }else{
                    $routeMatched = true;
                    $assets = Stdlib\ArrayUtils::merge($assets, (array) $config);
                }
            }
        }

        // Only return default if none regular expression matched
        return $routeMatched ? $assets : $default;
    }
}
