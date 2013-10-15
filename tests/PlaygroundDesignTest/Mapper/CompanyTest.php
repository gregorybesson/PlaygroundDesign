<?php
namespace PlaygroundDesginTest\Mapper;

use PlaygroundDesignTest\Bootstrap;
use \PlaygroundDesign\Entity\Company as CompanyEntity;

class CompanyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Service Manager
     * @var Zend\ServiceManager\ServiceManager
     */
    protected $sm;

   /**
     * Doctrine Entity Manager
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * Company sample
     * @var Array
     */
    protected $companyData;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->em = $this->sm->get('doctrine.entitymanager.orm_default');
        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $classes = $this->em->getMetadataFactory()->getAllMetadata();
        $tool->dropSchema($classes);
        $tool->createSchema($classes);

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

    public function testCanInsertNewRecord()
    {
        $company = new CompanyEntity();
        $company->populate($this->companyData);

        // save data
        $this->em->persist($company);
        $this->em->flush();

        $this->assertEquals($this->companyData['title'], $company->getTitle());
        $this->assertEquals($this->companyData['address'], $company->getAddress());
        $this->assertEquals($this->companyData['phoneNumber'], $company->getPhoneNumber());
        $this->assertEquals($this->companyData['mainImage'], $company->getMainImage());
        $this->assertEquals($this->companyData['facebookPage'], $company->getFacebookPage());
        $this->assertEquals($this->companyData['twitterAccount'], $company->getTwitterAccount());

        return $company->getId();
    }

    /**
     * @depends testCanInsertNewRecord
     */
    public function testCanUpdateInsertedRecord($id)
    {
        $data = array(
            'id' => $id
        );
        $company = $this->em->getRepository('PlaygroundDesign\Entity\Company')->find($id);
        $this->assertInstanceOf('PlaygroundDesign\Entity\Company', $company);
        $this->assertEquals($this->companyData['title'], $company->getTitle());

        $company->populate($data);
        $this->em->flush();

        $this->assertEquals($this->companyData['title'], $company->getTitle());
        $this->assertEquals($this->companyData['address'], $company->getAddress());
        $this->assertEquals($this->companyData['phoneNumber'], $company->getPhoneNumber());
        $this->assertEquals($this->companyData['mainImage'], $company->getMainImage());
        $this->assertEquals($this->companyData['facebookPage'], $company->getFacebookPage());
        $this->assertEquals($this->companyData['twitterAccount'], $company->getTwitterAccount());
    }

    /**
     * @depends testCanInsertNewRecord
     */
    public function testCanRemoveInsertedRecord($id)
    {
        $company = $this->em->getRepository('PlaygroundDesign\Entity\Company')->find($id);
        $this->assertInstanceOf('PlaygroundDesign\Entity\Company', $company);

        $this->em->remove($company);
        $this->em->flush();

        $company = $this->em->getRepository('PlaygroundDesign\Entity\Company')->find($id);
        $this->assertEquals(false, $company);
    }

    public function tearDown()
    {
        $dbh = $this->em->getConnection();

        unset($this->sm);
        unset($this->em);
        parent::tearDown();
    }
}