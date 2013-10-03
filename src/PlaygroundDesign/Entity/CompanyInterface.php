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
}
