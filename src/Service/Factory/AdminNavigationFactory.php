<?php
namespace PlaygroundDesign\Service\Factory;

use Zend\Navigation\Service\DefaultNavigationFactory;

/**
 * Factory for the ZfcAdmin admin navigation
 *
 * @package    ZfcAdmin
 * @subpackage Navigation\Service
 */
class AdminNavigationFactory extends DefaultNavigationFactory
{
    /**
     * @{inheritdoc}
     */
    protected function getName()
    {
        return 'admin';
    }
}
