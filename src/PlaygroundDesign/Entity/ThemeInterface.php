<?php

namespace PlaygroundDesign\Entity;

interface ThemeInterface
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