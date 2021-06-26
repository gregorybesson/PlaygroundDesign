<?php
namespace PlaygroundDesign\View\Helper;
use Laminas\View\Helper\Url as ZendUrl;
/**
 * This Class will reuse the params from the current page url (area)
 * It will then prepend the area with the name ('treasurehunt/play' will become 'frontend/treasurehunt/play')
 * This will make the creation of an url much easier for the front dev !
 *
 * @author greg
 *
 */
class AdminUrl extends ZendUrl
{
    public function __invoke($name = null, $params = array(), $options = array(), $reuseMatchedParams = true)
    {
        $link = null;

        try {
            if ($this->routeMatch && $this->routeMatch->getParam('area')) {
                if ($name) {
                    $name = $this->routeMatch->getParam('area') . '/' . $name;
                } else {
                    $name = $this->routeMatch->getParam('area');
                }
            } elseif ($name) {
                if (strtolower(substr($name, 0, 8)) != 'admin') {
                    $name = 'admin/' . $name;
                }
            } else {
                $name = 'admin';
            }

            $link = parent::__invoke($name, $params, $options, $reuseMatchedParams);
        } catch (\Exception $e) {
            //throw $e;
        }

        return $link;
    }
}
