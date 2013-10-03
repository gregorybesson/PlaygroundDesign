<?php

namespace PlaygroundDesign\Entity;

interface CompanyInterface
{
     /**
     * @param $title
     * @return mixed
     */
    public function setTitle($title);

    /**
     * @return mixed
     */
    public function getTitle();

    /**
     * @param $address
     * @return mixed
     */
    public function setAddress($address);

    /**
     * @return mixed
    */
    public function getAddress();
    /**
     * @param $phoneNumber
     * @return mixed
     */
    public function setPhoneNumber($phoneNumber);

    /**
     * @return mixed
    */
    public function getPhoneNumber();
    /**
     * @param $mainImage
     * @return mixed
     */
    public function setMainImage($mainImage);

    /**
     * @return mixed
    */
    public function getMainImage();
}
