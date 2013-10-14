<?php

namespace PlaygroundDesign\Service;

use PlaygroundDesign\Entity\Company as CompanyEntity;

use Zend\Form\Form;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Validator\NotEmpty;
use ZfcBase\EventManager\EventProvider;
use PlaygroundDesign\Options\ModuleOptions;
use DoctrineModule\Validator\NoObjectExists as NoObjectExistsValidator;
use Zend\Stdlib\ErrorHandler;

class Company extends EventProvider implements ServiceManagerAwareInterface
{

    /**
     * @var companyMapperInterface
     */
    protected $companyMapper;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var UserServiceOptionsInterface
     */
    protected $options;

    /**
     *
     * This service is ready for create a company
     *
     * @param  array  $data
     * @param  string $formClass
     *
     * @return \PlaygroundDesign\Entity\Company
     */
    public function create(array $data)
    {
        $company = new CompanyEntity;
        $entityManager = $this->getServiceManager()->get('playgrounddesign_doctrine_em');

        $form = $this->getServiceManager()->get('playgrounddesign_company_form');

        $form->bind($company);
        $form->setData($data);

        if (!$form->isValid()) {
            return false;
        }

        $companyMapper = $this->getCompanyMapper();
        $company = $companyMapper->insert($company);

        $this->uploadImage($company, $data);

        $company = $companyMapper->update($company);

        return $company;
    }

    /**
     *
     * This service is ready for edit a company
     *
     * @param  array  $data
     * @param  string $company
     * @param  string $formClass
     *
     * @return \PlaygroundDesign\Entity\Company
     */
    public function edit(array $data, $company)
    {
        $entityManager = $this->getServiceManager()->get('playgrounddesign_doctrine_em');

        $form  = $this->getServiceManager()->get('playgrounddesign_company_form');

        $form->bind($company);

        $form->setData($data);

        if (!$form->isValid()) {
            return false;
        }

        $company = $this->uploadImage($company, $data);
        $company = $this->getCompanyMapper()->update($company);

        return $company;
    }

    public function uploadImage($company, $data)
    {
         if (!empty($data['uploadMainImage']['tmp_name'])) {
            $path = $this->getOptions()->getMediaPath() . DIRECTORY_SEPARATOR;
            if (!is_dir($path)) {
                mkdir($path,0777, true);
            }
            $media_url = $this->getOptions()->getMediaUrl() . '/';
            move_uploaded_file($data['uploadMainImage']['tmp_name'], $path . $company->getId() . "-" . $data['uploadMainImage']['name']);
            $company->setMainImage($media_url . $company->getId() . "-" . $data['uploadMainImage']['name']);
        }
        return $company;
    }

    /**
     * getCompanyMapper
     *
     * @return CompanyMapperInterface
     */
    public function getCompanyMapper()
    {
        if (null === $this->companyMapper) {
            $this->companyMapper = $this->getServiceManager()->get('playgrounddesign_company_mapper');
        }

        return $this->companyMapper;
    }

    /**
     * setCompanyMapper
     * @param  CompanyMapperInterface $companyMapper
     *
     * @return PlaygroundDesign\Entity\Company Company
     */
    public function setCompanyMapper($companyMapper)
    {
        $this->companyMapper = $companyMapper;

        return $this;
    }

    /**
     * setOptions
     * @param  ModuleOptions $options
     *
     * @return PlaygroundDesign\Service\Company $this
     */
    public function setOptions(ModuleOptions $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * getOptions
     *
     * @return ModuleOptions $optins
     */
    public function getOptions()
    {
        if (!$this->options instanceof ModuleOptions) {
            $this->setOptions($this->getServiceManager()->get('playgrounddesignmodule_options'));
        }

        return $this->options;
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @param  ServiceManager $serviceManager
     * @return User
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;

        return $this;
    }
}