<?php

namespace PlaygroundDesign\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Laminas\View\Model\ViewModel;

class DebugView extends AbstractHelper
{
    protected $companyMapper;

    public function __construct()
    {
    }

    /**
     * @param  int|string $identifier
     * @return string
     */
    public function __invoke($that)
    {
        $pgDebug = [];
        foreach($that->viewModel()->getCurrent()->getVariables() as $name => $attributeOrClass) {
            if (!is_object($attributeOrClass)) {
                $pgDebug['$this->' . $name] = $attributeOrClass;
            } else if (strpos(get_class($attributeOrClass), '\Entity') !== false) {
                $class_methods = get_class_methods($attributeOrClass);
                $reflector = new \ReflectionClass(get_class($attributeOrClass));
                //echo $reflector->getDocComment();
                $methods = [];
                foreach ($class_methods as $method_name) {
                    if (substr($method_name, 0, 3) == 'get' || substr($method_name, 0, 2) == 'is') {
                        //echo get_class($attributeOrClass) . " " . $name . " " . $method_name . "<br/>";
                        // I only want the get methods from the playground entities
                        if (substr($method_name, 0, 14) != 'getInputFilter' && substr($method_name, 0, 3) != 'getArrayCopy') {
                            $result = call_user_func( array( $attributeOrClass, $method_name ) );
                            if (!is_string($result) && !is_numeric($result) && !is_int($result) && !is_null($result) && !is_bool($result)) {
                                $result = 'returns an array';
                            } else if (is_null($result)) {
                                $result = 'null';
                            } else if (is_bool($result)) {
                                $result = ($result) ? 'true' : 'false';
                            }
                            $methods['->'.$method_name.'()'] = [
                                'doc' => $reflector->getMethod($method_name)->getDocComment(),
                                'result' => $result
                            ];
                        }
                    }
                }
                
                $pgDebug[$name] = $methods;
            }
        }
        $str = '<table><thead><td>var or method</td><td>Result</td></thead>';
        foreach($pgDebug as $key => $value) {
            //$result .= $key . " : ";
            if (!is_array($value)) {
                $str .= '<tr><td>' . $key . '</td><td>' . $value . "</td></tr>";
            } else {
                foreach($value as $k => $v) {
                    $str .= '<tr><td>$this->' . $key . $k . '</td><td>' . $v['result'] . "</td></tr>";
                    //$result .= '<br/>  $this->' . $key . $k . " : " . $v['result'];
                    //$result .= '<br> DOC : ' . $v['doc'];
                }
                //$result .= "<br/>";
            }
        }
        $str .= '</table>';

        return $str;
    }
}
