<?php
namespace PlaygroundDesign\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;

class Header extends AbstractHelper
{
    /**
     * $var string template used for view
     */
    protected $viewTemplate='application/common/header.phtml';

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

        if (array_key_exists('currentPage', $options) && is_array($options['currentPage'])) {
            $pageGames = $options['currentPage']['pageGames'];
            $pageWinners = $options['currentPage']['pageWinners'];
        } else {
            $pageGames = '';
            $pageWinners = '';
        }

        $vm = new ViewModel(array());
        $vm->setTemplate($template);
        $vm->setVariables(array(
            'cat1' => $cat1,
            'cat2' => $cat2,
            'cat3' => $cat3,
            'pageGames' => $pageGames,
            'pageWinners' => $pageWinners,
            'channel' => $channel
        ));

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
