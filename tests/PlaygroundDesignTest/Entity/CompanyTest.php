<?php

namespace PlaygroundDesginTest\Entity;

use PlaygroundDesignTest\Bootstrap;
use \PlaygroundDesign\Entity\Company as CompanyEntity;

class CompanyTest extends \PHPUnit_Framework_TestCase
{

    protected $companyData;

    public function setUp()
    {
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

    public function testPopulate()
    {
        $company = new CompanyEntity;
        $company->populate($this->companyData);

        $this->assertEquals($this->companyData['title'], $company->getTitle());
        $this->assertEquals($this->companyData['address'], $company->getAddress());
        $this->assertEquals($this->companyData['phoneNumber'], $company->getPhoneNumber());
        $this->assertEquals($this->companyData['mainImage'], $company->getMainImage());
        $this->assertEquals($this->companyData['facebookPage'], $company->getFacebookPage());
        $this->assertEquals($this->companyData['twitterAccount'], $company->getTwitterAccount());
    }

    public function testGetAddress()
    {
        $company = new CompanyEntity;
        $company->populate($this->companyData);

        $this->assertEquals($this->companyData['address'], $company->getAddress());
    }
}
