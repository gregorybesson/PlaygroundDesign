<?php

namespace PlaygroundDesginTest\Service;

use PlaygroundDesignTest\Bootstrap;
use \PlaygroundDesign\Entity\Company as CompanyEntity;

class CompanyTest extends \PHPUnit_Framework_TestCase
{
    protected $traceError = true;

    /**
     * Company sample
     * @var Array
     */
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

    public function testCreateTrue()
    {
        $service = new \PlaygroundDesign\Service\Company(Bootstrap::getServiceManager());

        $companyPostUpdate = new CompanyEntity;
        $companyPostUpdate->setTitle($this->companyData['title']);

        $mapper = $this->getMockBuilder('PlaygroundDesign\Mapper\Company')
            ->disableOriginalConstructor()
            ->getMock();
        $mapper->expects($this->any())
            ->method('insert')
            ->will($this->returnValue($companyPostUpdate));
        $mapper->expects($this->any())
            ->method('update')
            ->will($this->returnValue($companyPostUpdate));

        $service->setCompanyMapper($mapper);

        $company = $service->create($this->companyData);

        $this->assertEquals($this->companyData['title'], $company->getTitle());
    }
}
