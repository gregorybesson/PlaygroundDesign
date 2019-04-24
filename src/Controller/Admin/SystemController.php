<?php

namespace PlaygroundDesign\Controller\Admin;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorInterface;

class SystemController extends AbstractActionController
{
    protected $settingsForm;

    /**
     * @var SettingsService
     */
    protected $settingsService;

    /**
     *
     * @var ServiceManager
     */
    protected $serviceLocator;

    public function __construct(ServiceLocatorInterface $locator)
    {
        $this->serviceLocator = $locator;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
    
    public function indexAction()
    {
        return new ViewModel();
    }

    public function settingsAction()
    {
        $service = $this->getSettingsService();
        $mapper = $service->getSettingsMapper();

        $settings = $mapper->findById('1');

        $viewModel = new ViewModel();

        $form = $this->getSettingsForm();
        $form->setAttribute('method', 'post');

        if ($this->getRequest()->isPost()) {
            $data = array_merge(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );
            
            if ($settings) {
                $settings = $service->edit($data, $settings);
            } else {
                $settings = $service->create($data);
            }
        }

        if ($settings) {
            $form->bind($settings);
        }

        return $viewModel->setVariables(array('form' => $form, 'title' => 'Settings'));
    }

    public function modulesAction()
    {
        $manager = $this->getServiceLocator()->get('ModuleManager');
        $modules = $manager->getLoadedModules();

        return array('modules' => $modules);
    }

    public function getSettingsForm()
    {
        if (!$this->settingsForm) {
            $this->settingsForm = $this->getServiceLocator()->get('playgrounddesign_settings_form');
        }

        return $this->settingsForm;
    }

    public function setSettingsForm(\PlaygoundDesign\Form\Admin\Settings $form)
    {
        $this->settingsForm = $form;

        return $this;
    }

    public function getSettingsService()
    {
        if (!$this->settingsService) {
            $this->settingsService = $this->getServiceLocator()->get('playgrounddesign_settings_service');
        }

        return $this->settingsService;
    }

    public function setSettingsService(SettingsService $service)
    {
        $this->settingsService = $service;

        return $this;
    }
}
