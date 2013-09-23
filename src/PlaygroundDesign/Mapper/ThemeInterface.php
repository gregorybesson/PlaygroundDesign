<?php

namespace PlaygroundDesign\Mapper;

interface ThemeInterface
{
    public function findById($id);

    public function insert($theme);

    public function update($theme);
}