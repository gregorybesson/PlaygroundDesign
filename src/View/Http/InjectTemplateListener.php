<?php

namespace PlaygroundDesign\View\Http;

use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface as Events;
use Laminas\Mvc\MvcEvent;
use Laminas\Stdlib\StringUtils;
use Laminas\View\Model\ModelInterface as ViewModel;
use Laminas\Mvc\View\Http\InjectTemplateListener as ZendInjectTemplateListener;

class InjectTemplateListener extends ZendInjectTemplateListener
{

    /**
     * Maps controller to template if controller namespace is whitelisted or mapped
     * ***********   PLAYGROUND  **************************************************
     * I don't want the Frontend nor Admin directories to be part of the template path
     * hence the overload of the original listener
     *
     * @param string $controller controller FQCN
     * @return string|false template name or false if controller was not matched
     */
    public function mapController($controller)
    {
        $mapped = '';
        foreach ($this->controllerMap as $namespace => $replacement) {
            if (// Allow disabling rule by setting value to false since config
                // merging have no feature to remove entries
                false == $replacement
                // Match full class or full namespace
                || ! ($controller === $namespace || strpos($controller, $namespace . '\\') === 0)
            ) {
                continue;
            }

            // Map namespace to $replacement if its value is string
            if (is_string($replacement)) {
                $mapped = rtrim($replacement, '/') . '/';
                $controller = substr($controller, strlen($namespace) + 1) ?: '';
                break;
            }
        }

        //strip Controller namespace(s) (but not classname)
        $parts = explode('\\', $controller);
        array_pop($parts);
        $parts = array_diff($parts, ['Controller']);
        //print_r($parts);
        if (isset($parts[2]) && ($parts[2] === 'Admin' || $parts[2] === 'Frontend')) {
            unset($parts[2]);
        }
        //print_r($parts);
        //$parts = array_diff($parts, ['Frontend']);
        //$parts = array_diff($parts, ['Admin']);
        //strip trailing Controller in class name
        $parts[] = $this->deriveControllerClass($controller);
        $controller = implode('/', $parts);

        $template = trim($mapped . $controller, '/');
        // inflect CamelCase to dash
        return $this->inflectName($template);
    }
}
