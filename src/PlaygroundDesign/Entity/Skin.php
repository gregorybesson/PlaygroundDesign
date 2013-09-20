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
 * @ORM\Table(name="skin")
 */
class Skin implements SkinInterface, InputFilterAwareInterface
{
    
    const BASE = 'design/';

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
     * @ORM\Column(type="string", length=255)
     */
    protected $image;

    /**
     * type
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $type;

    /**
     * package
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $package;

    /**
     * theme
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $theme;

     /**
     * Author of the skin
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $author;

    /**
     * Is this Skin is activated on the site
     * @ORM\Column(name="is_active",type="boolean")
     */
    protected $is_active;

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
        // Le skin est par defaut désactivé
        $this->is_active = false;
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
        $this->is_active = (bool) $isActive;

        return $this;
    }

    /**
     * @return bool $isActive
     */
    public function getIsActive()
    {
        return $this->is_active;
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
     * Convert the object to an array.
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * @return date
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
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

        if (isset($data['image']) && $data['image'] != null) {
            $this->active = $data['image'];
        }

        if (isset($data['type']) && $data['type'] != null) {
            $this->type = $data['type'];
        }

        if (isset($data['package']) && $data['package'] != null) {
            $this->package = $data['package'];
        }

        if (isset($data['theme']) && $data['theme'] != null) {
            $this->theme = $data['theme'];
        }

        if (isset($data['author']) && $data['author'] != null) {
            $this->author = $data['author'];
        }

        if (isset($data['is_active']) && $data['is_active'] != null) {
            $this->is_active = $data['is_active'];
        }
    }

    /**
    * getBasePath : recuperation du dossier de base de l'application
    *  
    * @return string $base  
    */
    public function getBasePath()
    {
        $base = exec(escapeshellcmd('pwd'));
        return trim($base);
    }

    /**
    * getUrlBase : recuperation du chemin complet du skin
    *  
    * @return string $base  
    */
    public function getUrlBase()
    {
        return $this->getBasePath().'/'.$this->getType().'/'.$this->getPackage().'/'.$this->getTheme().'/';
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