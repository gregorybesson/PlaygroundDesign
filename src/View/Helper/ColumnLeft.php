<?php
namespace PlaygroundDesign\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Laminas\View\Model\ViewModel;

class ColumnLeft extends AbstractHelper
{
    /**
     * $var string template used for view
     */
    protected $viewTemplate='playground-design/common/column_left.phtml';

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
        $vm->setVariables($options);

        return $this->getView()->render($vm);
    }

    /**
     * @param  string     $viewTemplate
     * @return ColumnLeft
     */
    public function setViewTemplate($viewTemplate)
    {
        $this->viewTemplate = $viewTemplate;

        return $this;
    }
}
