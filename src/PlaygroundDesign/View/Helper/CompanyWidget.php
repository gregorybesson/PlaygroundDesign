<?php

namespace PlaygroundDesign\View\Helper;

use Zend\View\Helper\AbstractHelper;
use PlaygroundDesign\Mapper\Company as CompanyMapper;
use Zend\View\Model\ViewModel;

class CompanyWidget extends AbstractHelper
{
    protected $companyMapper;

    public function __construct(\PlaygroundDesign\Mapper\Company $companyMapper)
    {
        $this->companyMapper = $companyMapper;
    }

    /**
     * @param  int|string $identifier
     * @return string
     */
    public function __invoke()
    {
        $result = '';
        $companies = $this->companyMapper->findAll();
        $company = NULL;
        if (count($companies) > 0) {
            $company= $companies[0];
        }
        
        return $company;
    }
}
