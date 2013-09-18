<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace PlaygroundDesign\Mvc\Router\Http;

use Traversable;
use Zend\Mvc\Router\Exception;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\RequestInterface as Request;

/**
 * Regex route.
 */
class RegexSlash extends \Zend\Mvc\Router\Http\Regex implements \Zend\Mvc\Router\Http\RouteInterface
{


    /**
     * Create a new regex route.
     *
     * @param  string $regex
     * @param  string $spec
     * @param  array  $defaults
     */
    public function __construct($regex, $spec, array $defaults = array())
    {
        parent::__construct($regex, $spec, $defaults);
    }


    /**
     * assemble(): Defined by RouteInterface interface.
     *
     * added a special feature for removing a '/' in some specific cases
     * @see    \Zend\Mvc\Router\RouteInterface::assemble()
     * @param  array $params
     * @param  array $options
     * @return mixed
     */
    public function assemble(array $params = array(), array $options = array())
    {
        $url                   = $this->spec;
        $mergedParams          = array_merge($this->defaults, $params);
        $this->assembledParams = array();

        foreach ($mergedParams as $key => $value) {
            $spec = '%' . $key . '%';

            if (strpos($url, $spec) !== false) {
            	// $url is this form : 'spec' => '/%channel%/',
            	// if $channel == '' we must remove the last '/' too.
            	if (empty($value) && strpos($url, $spec.'/') !== false) {
            		$spec .= '/';
            	}
            	$url = str_replace($spec, rawurlencode($value), $url);
                $this->assembledParams[] = $key;
            }
        }
        // if remaining %var% in the url, I remove it
        /*$regex = '.*(?<var>[%].*[%]\/).*';
        $result = preg_match('(\G' . $regex . ')', $url, $matches);
        foreach ($matches as $key => $value) {
        	if (is_numeric($key) || is_int($key) || $value === '') {
        		unset($matches[$key]);
        	} else {
        		$url = str_replace($value, '', $url);
        	}
        }*/

        return $url;
    }
}
