<?php

namespace PlaygroundDesign\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity @HasLifecycleCallbacks
 * @ORM\Table(name="skin")
 */
class Skin implements SkinInterface, InputFilterAwareInterface
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
     * image
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     */
    protected $image;

    /**
     * type
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     */
    protected $type;

    /**
     * package
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     */
    protected $package;

    /**
     * theme
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     */
    protected $theme;

     /**
     * Author of the skin
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     */
    protected $author;

    /**
     * Is this Skin is activated on the site
     * @ORM\Column(name="is_active",type="boolean")
     */
    protected $isActive;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated_at;


    /** @PrePersist */
    public function createChrono()
    {
        $this->created_at = new \DateTime("now");
        $this->updated_at = new \DateTime("now");
    }

    /** @PreUpdate */
    public function updateChrono()
    {
        $this->updated_at = new \DateTime("now");
    }

    /**
     * @param int $id
     * @return Skin
     */
    public function setId($id)
    {
        $this->id = (int) $id;

        return $this;
    }

    /**
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $title
     * @return Skin
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
     * @param string $image
     * @return Skin
     */
    public function setImage($image)
    {
        $this->image = (string) $image;

        return $this;
    }

    /**
     * @return string $image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $type
     * @return Skin
     */
    public function setType($type)
    {
        $this->type = (string) $type;

        return $this;
    }

    /**
     * @return string $type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Skin
     */
    public function setPackage($package)
    {
        $this->package = (string) $package;

        return $this;
    }

    /**
     * @return string $package
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * @param string $theme
     * @return Skin
     */
    public function setTheme($theme)
    {
        $this->theme = (string) $theme;

        return $this;
    }

    /**
     * @return string $theme
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @param string $author
     * @return Skin
     */
    public function setAuthor($author)
    {
        $this->author = (string) $author;

        return $this;
    }

    /**
     * @return string $author
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param bool $isActive
     * @return Skin
     */
    public function setIsActive($isActive)
    {
        $this->isActive = (bool) $isActive;

        return $this;
    }

    /**
     * @return bool $isActive
     */
    public function getIsActive()
    {
        return $this->isActive;
    }


    /**
     * @param mixed $createdAt
     * @return Skin
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return date
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $updatedAt
     * @return Skin
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return date
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        return $this->inputFilter;
    }

}
