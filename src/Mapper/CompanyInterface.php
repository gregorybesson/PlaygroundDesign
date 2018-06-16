<?php

namespace PlaygroundDesign\Mapper;

interface CompanyInterface
{
    public function findById($id);

    public function insert($entity);

    public function update($entity);
}
