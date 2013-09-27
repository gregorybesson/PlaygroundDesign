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
 * @ORM\Table(name="design_theme") 
 */
class Theme implements ThemeInterface, InputFilterAwareInterface
{
    
    const BASE = 'design/';
    const AUTHOR = 'system';
    const SCREENSHOT_PATH = 'assets/images/screenshots';

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $image;

    /**
     * area
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $area;

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
     * Author of the theme
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $author;

    /**
     * Is this Theme is activated on the site
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
    }

    /**
    * Si le theme n'a pas d'auteur, on met System 
    * @PrePersist 
    */
    public function createAuthor()
    {
        if (empty($this->author)) {
            $this->author = self::AUTHOR;
        }
    }

    /**
    * Un theme crée est automatiquement désactivé
    * @PrePersist 
    */
    public function createActive()
    {
        $this->is_active = false;
    }

    /** @PreUpdate */
    public function updateChrono()
    {
        $this->updated_at = new \DateTime("now");
    }

    /**
     * @param int $id
     * @return Theme
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
     * @return Theme
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
     * @return Theme
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
    * getImages permet de recuperer les images depuis le filer
    *
    * @return array $images 
    */
    public function getImages()
    {
        $images = array();
        $image = $this->getImage();
        if (!empty($image)) {
            $images[$image] = $image;
        }
        
        $screenshotsPath = $this->getFilePath().self::SCREENSHOT_PATH;
        $files = scandir($screenshotsPath);

        foreach ($files as $file) {
            if (is_file($file)) {
                $images['/theme/images/screenshots/'.$file] = '/theme/images/screenshots/'.$file;
            }
        }

       
        return $images;
    }

    /**
     * @param string $area
     * @return Theme
     */
    public function setArea($area)
    {
        $this->area = (string) $area;

        return $this;
    }

    /**
     * @return string $area
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @param string $package
     * @return Theme
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
     * @return Theme
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
     * @return Theme
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
     * @return Theme
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
     * @return Theme
     */
    public function setCreatedAt($created_at)
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
     * @return Theme
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

        if (isset($data['area']) && $data['area'] != null) {
            $this->area = $data['area'];
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
        return __DIR__.'/../../../../../../'.self::BASE;
    }

    /**
    * getUrlBase : recuperation du chemin complet du theme
    *  
    * @return string $base  
    */
    public function getUrlBase()
    {
        return self::BASE.$this->getArea().'/'.$this->getPackage().'/'.$this->getTheme().'/';
    }

    /**
    * getBasePath : recuperation du dossier de base de l'application
    *  
    * @return string $base  
    */
    public function getFilePath()
    {
        return $this->getBasePath().$this->getArea().'/'.$this->getPackage().'/'.$this->getTheme().'/';
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