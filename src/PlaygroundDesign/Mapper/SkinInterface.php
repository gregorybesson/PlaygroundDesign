<?php

namespace PlaygroundDesign\Entity;

interface SkinInterface
{
    public function findById($id);

    public function findByIdentifier($identifier);

    public function insert($block);

    public function update($block);
}