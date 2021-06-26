<?php

namespace PlaygroundDesignTest\Form;

use PlaygroundDesignTest\Bootstrap;
use PlaygroundDesign\Entity\Company as CompanyEntity;

class CompanyTest extends \PHPUnit\Framework\TestCase
{
    protected $traceError = true;
    protected $form;

    /**
     * Company sample
     * @var Array
     */
    protected $companyData;

    protected function setUp(): void
    {
        $this->getForm();
        $this->companyData = array(
            'title' => 'Company',
            'address' => 'CompanyAddress',
            'phoneNumber'  => '0123456789',
            'mainImage' => 'media/design/1-180.jpg',
            'facebookPage' => 'testest',
            'twitterAccount' => 'testest',
        );
        parent::setUp();
    }

    public function testValid()
    {
        $company = new CompanyEntity();
        $company->populate($this->companyData);

        $this->form->bind($company);
        $this->assertTrue($this->form->isValid());
    }

    public function getForm()
    {
        if (null === $this->form) {
            $sm = Bootstrap::getServiceManager();
            $this->form = $sm->get('playgrounddesign_company_form');
        }

        return $this->form;
    }
}
