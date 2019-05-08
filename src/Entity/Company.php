<?php

namespace PlaygroundDesign\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity @HasLifecycleCallbacks
 * @ORM\Table(name="company")
 */
class Company implements CompanyInterface, InputFilterAwareInterface
{

    protected $inputFilter;
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * title
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $title;

    /**
     * address
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $address;

    /**
     * email address
     * @ORM\Column(name="email_address", type="string", length=255, nullable=true)
     */
    protected $emailAddress;

    /**
     * email name
     * @ORM\Column(name="email_name", type="string", length=255, nullable=true)
     */
    protected $emailName;

    /**
     * phoneNumber
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    protected $phoneNumber;

    /**
     * facebookPage
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $facebookPage;

    /**
     * twitterAccount
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $twitterAccount;

    /**
     * googleAnalytics
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $googleAnalytics;

    /**
     * googleAnalytics view id (for the stats module)
     * @ORM\Column(name="ga_view_id", type="string", length=255, nullable=true)
     */
    protected $gaViewId;

    /**
     * @ORM\Column(name="main_image", type="string", length=255, nullable=true)
     */
    protected $mainImage;

    /**
     * @param string $title
     * @return Company
     */
    public function setId($id)
    {
        $this->id = (string) $id;

        return $this;
    }

    /**
     * @return string $title
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $title
     * @return Company
     */
    public function setTitle($title)
    {
        $this->title = (string) $title;

        return $this;
    }

    /**
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $emailAddress
     * @return Company
     */
    public function setEmailAddress($emailEmail)
    {
        $this->emailAddress = (string) $emailAddress;

        return $this;
    }

    /**
     * @return string $emailAddress
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * @param string $emailName
     * @return Company
     */
    public function setEmailName($emailName)
    {
        $this->emailName = (string) $emailName;

        return $this;
    }

    /**
     * @return string $emailName
     */
    public function getEmailName()
    {
        return $this->emailName;
    }


    /**
     * @param string $address
     * @return Company
     */
    public function setAddress($address)
    {
        $this->address = (string) $address;

        return $this;
    }

    /**
     * @return string $address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $googleAnalytics
     * @return Company
     */
    public function setGoogleAnalytics($googleAnalytics)
    {
        $this->googleAnalytics = (string) $googleAnalytics;

        return $this;
    }

    /**
     * @return string $googleAnalytics
     */
    public function getGoogleAnalytics()
    {
        return $this->googleAnalytics;
    }

    /**
     * @param string $gaViewId
     * @return Company
     */
    public function setGaViewId($gaViewId)
    {
        $this->gaViewId = (string) $gaViewId;

        return $this;
    }

    /**
     * @return string $gaViewId
     */
    public function getGaViewId()
    {
        return $this->gaViewId;
    }

    /**
     * @param string $phoneNumber
     * @return Company
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = (string) $phoneNumber;

        return $this;
    }

    /**
     * @return string $phoneNumber
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param string $facebookPage
     * @return Company
     */
    public function setFacebookPage($facebookPage)
    {
        $this->facebookPage = (string) $facebookPage;

        return $this;
    }

    /**
     * @return string $facebookPage
     */
    public function getFacebookPage()
    {
        return $this->facebookPage;
    }

    /**
     * @param string $twitterAccount
     * @return Company
     */
    public function setTwitterAccount($twitterAccount)
    {
        $this->twitterAccount = (string) $twitterAccount;

        return $this;
    }

    /**
     * @return string $twitterAccount
     */
    public function getTwitterAccount()
    {
        return $this->twitterAccount;
    }


    /**
     *
     * @return the $mainImage
     */
    public function getMainImage()
    {
        return $this->mainImage;
    }

    /**
     *
     * @param field_type $mainImage
     */
    public function setMainImage($mainImage)
    {
        $this->mainImage = $mainImage;

        return $this;
    }


    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * Populate from an array.
     *
     * @param array $data
     */
    public function populate($data = array())
    {
        if (isset($data['title']) && $data['title'] != null) {
            $this->title = $data['title'];
        }
        if (isset($data['address']) && $data['address'] != null) {
            $this->address = $data['address'];
        }
        if (isset($data['emailAddress']) && $data['emailAddress'] != null) {
            $this->emailAddress = $data['emailAddress'];
        }
        if (isset($data['emailName']) && $data['emailName'] != null) {
            $this->emailName = $data['emailName'];
        }
        if (isset($data['phoneNumber']) && $data['phoneNumber'] != null) {
            $this->phoneNumber = $data['phoneNumber'];
        }
        if (isset($data['facebookPage']) && $data['facebookPage'] != null) {
            $this->facebookPage = $data['facebookPage'];
        }
        if (isset($data['twitterAccount']) && $data['twitterAccount'] != null) {
            $this->twitterAccount = $data['twitterAccount'];
        }
        if (isset($data['googleAnalytics']) && $data['googleAnalytics'] != null) {
            $this->googleAnalytics = $data['googleAnalytics'];
        }
        if (isset($data['gaViewId']) && $data['gaViewId'] != null) {
            $this->gaViewId = $data['gaViewId'];
        }
        if (isset($data['mainImage']) && $data['mainImage'] != null) {
            $this->mainImage = $data['mainImage'];
        }
    }



    /**
    * setInputFilter
    * @param InputFilterInterface $inputFilter
    */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    /**
    * getInputFilter
    *
    * @return  InputFilter $inputFilter
    */
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}
