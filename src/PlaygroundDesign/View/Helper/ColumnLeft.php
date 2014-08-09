<?php
namespace PlaygroundDesign\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;

class ColumnLeft extends AbstractHelper
{
    /**
     * $var string template used for view
     */
    protected $viewTemplate='application/common/column_left.phtml';

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

        if (array_key_exists('channel', $options) && $options['channel'] != '') {
            $channel = $options['channel'];
        } else {
            $channel = '';
        }

       /*if (array_key_exists('adserving', $options) && is_array($options['adserving'])) {
            $cat1 = $options['adserving']['cat1'];
            $cat2 = $options['adserving']['cat2'];
            $cat3 = $options['adserving']['cat3'];
        } else {*/
            $cat1 = 'playground';
            $cat2 = '';
            $cat3 = '';
        //}

        $vm = new ViewModel(array());
        $vm->setTemplate($template);
        $vm->setVariables(array(
            'cat1' => $cat1,
            'cat2' => $cat2,
            'cat3' => $cat3,
            'channel' => $channel,
        ));

        return $this->getView()->render($vm);

        //return $this->getView()->render('application/common/column_right.phtml');
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
