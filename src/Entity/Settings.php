<?php

namespace PlaygroundDesign\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity @HasLifecycleCallbacks
 * @ORM\Table(name="platform_settings")
 */
class Settings implements InputFilterAwareInterface
{

    protected $inputFilter;
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="home_pagination", type="integer", nullable=true)
     */
    protected $homePagination = 0;

    /**
     * @ORM\Column(name="home_keep_closed_game_position", type="integer", nullable=true)
     */
    protected $homeKeepClosedGamePosition = 0;

    /**
     * @ORM\Column(name="google_recaptcha_url", type="string", nullable=true)
     */
    protected $gReCaptchaUrl = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * @ORM\Column(name="google_recaptcha_key", type="string", nullable=true)
     */
    protected $gReCaptchaKey = '';

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;


    /**
     * @PrePersist
     */
    public function createChrono()
    {
        $this->createdAt = new \DateTime("now");
        $this->updatedAt = new \DateTime("now");
    }

    /**
     * @PreUpdate
     */
    public function updateChrono()
    {
        $this->updatedAt = new \DateTime("now");
    }

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
     *
     * @return the $homePagination
     */
    public function getHomePagination()
    {
        return $this->homePagination;
    }

    /**
     *
     * @param field_type $homePagination
     */
    public function setHomePagination($homePagination)
    {
        $this->homePagination = $homePagination;

        return $this;
    }

    /**
     *
     * @return the $homeKeepClosedGamePosition
     */
    public function getHomeKeepClosedGamePosition()
    {
        return $this->homeKeepClosedGamePosition;
    }

    /**
     *
     * @param field_type $homeKeepClosedGamePosition
     */
    public function setHomeKeepClosedGamePosition($homeKeepClosedGamePosition)
    {
        $this->homeKeepClosedGamePosition = $homeKeepClosedGamePosition;

        return $this;
    }

    /**
     *
     * @return the $gReCaptchaUrl
     */
    public function getGReCaptchaUrl()
    {
        return $this->gReCaptchaUrl;
    }

    /**
     *
     * @param field_type $gReCaptchaUrl
     */
    public function setGReCaptchaUrl($gReCaptchaUrl)
    {
        $this->gReCaptchaUrl = $gReCaptchaUrl;

        return $this;
    }

    /**
     *
     * @return the $gReCaptchaKey
     */
    public function getGReCaptchaKey()
    {
        return $this->gReCaptchaKey;
    }

    /**
     *
     * @param field_type $gReCaptchaKey
     */
    public function setGReCaptchaKey($gReCaptchaKey)
    {
        $this->gReCaptchaKey = $gReCaptchaKey;

        return $this;
    }

    /**
     * @param mixed $createdAt
     * @return Theme
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return date
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $updatedAt
     * @return Theme
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return date
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * Populate from an array. Used when hydrating from a form
     *
     * @param array $data
     */
    public function populate($data = array())
    {
        if (isset($data['homePagination']) && $data['homePagination'] != null) {
            $this->homePagination = $data['homePagination'];
        }
        if (isset($data['homeKeepClosedGamePosition']) && $data['homeKeepClosedGamePosition'] != null) {
            $this->homeKeepClosedGamePosition = $data['homeKeepClosedGamePosition'];
        }
        if (isset($data['gReCaptchaUrl']) && $data['gReCaptchaUrl'] != null) {
            $this->gReCaptchaUrl = $data['gReCaptchaUrl'];
        }
        if (isset($data['gReCaptchaKey']) && $data['gReCaptchaKey'] != null) {
            $this->gReCaptchaKey = $data['gReCaptchaKey'];
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
