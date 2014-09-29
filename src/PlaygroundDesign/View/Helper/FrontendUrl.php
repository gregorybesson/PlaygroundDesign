<?php
namespace PlaygroundDesign\View\Helper;

use Zend\View\Helper\Url as ZendUrl;

/**
 * This Class will reuse the params from the current page url (channel + area)
 * It will then prepend the area with the name ('treasurehunt/play' will become 'frontend/treasurehunt/play')
 * This will make the creation of an url much easier for the front dev !
 * 
 * @author greg
 *
 */
class FrontendUrl extends ZendUrl {

    public function __invoke($name = null, $params = array(), $options = array(), $reuseMatchedParams = true) {
        
        $name = ($this->routeMatch && $this->routeMatch->getParam('area'))
            ? $this->routeMatch->getParam('area') . (
                $name ? '/' . $name : ''
            ) : $name;
        
        $link = parent::__invoke($name, $params, $options, $reuseMatchedParams);

        return $link;
    }

}