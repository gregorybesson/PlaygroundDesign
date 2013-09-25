<?php
namespace PlaygroundDesign\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;

class Head extends AbstractHelper
{
    /**
     * $var string template used for view
     */
    protected $viewTemplate='application/common/head.phtml';

    public function __construct()
    {
    }

    public function __invoke($options = array())
    {
        if (array_key_exists('template', $options) && $options['template'] != '') {
            $template = $options['template'];
        } else {
            $template = $this->viewTemplate;
        }

        $vm = new ViewModel(array());
        $vm->setTemplate($template);

        return $this->getView()->render($vm);

    }

    /**
     * @param  string $viewTemplate
     * @return Head
     */
    public function setViewTemplate($viewTemplate)
    {
        $this->viewTemplate = $viewTemplate;

        return $this;
    }
}
