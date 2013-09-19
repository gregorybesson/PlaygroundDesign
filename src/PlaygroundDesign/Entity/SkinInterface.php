<?php

namespace PlaygroundDesign\Entity;

interface SkinInterface
{
   /**
     * @return int $id
     */
    public function getId();

    /**
     * @param int $id
     * @return mixed
     */
    public function setId($id);
}