<?php
namespace PlaygroundDesign\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;

class Footer extends AbstractHelper
{
    /**
     * $var string template used for view
     */
    protected $viewTemplate='playground-design/common/footer.phtml';

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

        $vm = new ViewModel();
        $vm->setTemplate($template);
        $vm->setVariables($options);
        return $this->getView()->render($vm, array());
    }

    /**
     * @param  string $viewTemplate
     * @return Footer
     */
    public function setViewTemplate($viewTemplate)
    {
        $this->viewTemplate = $viewTemplate;

        return $this;
    }
}
