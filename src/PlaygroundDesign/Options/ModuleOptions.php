<?php

namespace PlaygroundDesign\Options;

class ModuleOptions
{
    /**
     * @var string
     */
    protected $actionEntityClass = 'PlaygroundDesign\Entity\Theme';

    /**
     * @var bool
     */
    protected $enableDefaultEntities = true;

    /**
     * Turn off strict options mode
     */
    protected $__strictMode__ = false;
    
    /**
     * @var string
     */
    protected $partnerMapper = 'PlaygroundDesign\Mapper\Theme';

}
