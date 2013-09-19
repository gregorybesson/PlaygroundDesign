<?php

namespace PlaygroundDesign\Mapper;

interface SkinInterface
{
    public function findById($id);

    public function insert($skin);

    public function update($skin);
}