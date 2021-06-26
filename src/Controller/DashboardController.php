<?php

namespace PlaygroundDesign\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class DashboardController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }
}
