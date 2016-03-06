<?php

namespace PlaygroundDesignTest\Controller\Frontend;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

use PlaygroundDesignTest\Bootstrap;
use PlaygroundDesign\Entity\Company as CompanyEntity;

class CompanyAdminControllerTest extends AbstractHttpControllerTestCase
{
    protected $traceError = true;

   /**
    * @var $themeMapper mapper de l'entity theme
    */
    protected $CompanyMapper;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ . '/../../TestConfig.php'
        );

        parent::setUp();
    }

    public function testIndexAction()
    {
        //$this->dispatch('/admin/company');
        $company = new CompanyEntity();
        $company->setTitle('titre')
            ->setAddress('address')
            ->setPhoneNumber('0102030405');

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);

        $f = $this->getMockBuilder('PlaygroundDesign\Service\Company')
        ->setMethods(array('getCompanyMapper'))
        ->disableOriginalConstructor()
        ->getMock();

        $serviceManager->setService('playgrounddesign_company_service', $f);

        $pageMapperMock = $this->getMockBuilder('PlaygroundDesign\Mapper\Company')
        ->disableOriginalConstructor()
        ->getMock();

        $f->expects($this->once())
        ->method('getCompanyMapper')
        ->will($this->returnValue($pageMapperMock));

        $pageMapperMock->expects($this->once())
        ->method('findById')
        ->will($this->returnValue($company));

        $this->dispatch('/admin/company');
        $this->assertModuleName('playgrounddesign');
        $this->assertControllerName('playgrounddesign\controller\admin\Company');
        $this->assertControllerClass('CompanyController');
        $this->assertActionName('index');
        $this->assertMatchedRouteName('admin/playgrounddesign_companyadmin');
    }
}
