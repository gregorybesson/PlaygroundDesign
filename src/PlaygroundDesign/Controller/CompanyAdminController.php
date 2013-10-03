<?php

namespace PlaygroundDesign\Controller;

use PlaygroundDesign\Service\Company as CompanyService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class CompanyAdminController extends AbstractActionController
{
    protected $companyForm;

    /**
     * @var CompanyService
     */
    protected $companyService;

    public function indexAction()
    {
        $service = $this->getCompanyService();
        $mapper = $service->getCompanyMapper();

        $company = $mapper->findById('1');

        $viewModel = new ViewModel();

        $form = $this->getCompanyForm();
        $form->setAttribute('method', 'post');
        if($company) {
            $form->bind($company);
        }

        if ($this->getRequest()->isPost()) {
            $data = array_merge(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );
            if($company) {
                $company = $service->edit($data, $company);
            } else {
                $company = $service->create($data);
            }
        }
        return $viewModel->setVariables(array('form' => $form, 'title' => 'Company'));
    }


    public function getCompanyForm()
    {
        if (!$this->companyForm) {
            $this->companyForm = $this->getServiceLocator()->get('playgrounddesign_company_form');
        }

        return $this->companyForm;
    }

    public function setCompanyForm(CompanyForm $form)
    {
        $this->companyForm = $form;

        return $this;
    }

    public function getCompanyService()
    {
        if (!$this->companyService) {
            $this->companyService = $this->getServiceLocator()->get('playgrounddesign_company_service');
        }

        return $this->companyService;
    }

    public function setCompanyService(CompanyService $service)
    {
        $this->companyService = $service;

        return $this;
    }
}
