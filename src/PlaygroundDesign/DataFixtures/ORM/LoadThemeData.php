<?php

namespace PlaygroundDesign\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use PlaygroundDesign\Entity\Theme;

/**
 *
 * @author troger
 * Use the command : php doctrine-module.php data-fixture:import --append
 * to install these data into database
 */
class LoadThemeData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load address types
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {

        $theme = new Theme();
        $theme->setTitle('Theme 1');
        $theme->setImage('/design/admin/default/base/assets/images/1-theme-1.jpg');
        $theme->setType('admin');
        $theme->setPackage('default');
        $theme->setTheme('base');
        $theme->setAuthor('system');
        $theme->setIsActive(0);
        $theme->setCreatedAt(time());
        $theme->setUpdatedAt(time());

        $manager->persist($theme);

        $theme = new Theme();
        $theme->setTitle('Theme 2');
        $theme->setImage('/design/admin/default/demo/assets/images/2-theme-2.jpg');
        $theme->setType('admin');
        $theme->setPackage('default');
        $theme->setTheme('demo');
        $theme->setAuthor('system');
        $theme->setIsActive(0);
        $theme->setCreatedAt(time());
        $theme->setUpdatedAt(time());

        $manager->persist($theme);
        $theme->setIsActive(1);
        $manager->persist($theme);

        $theme = new Theme();
        $theme->setTitle('Theme 3');
        $theme->setImage('/design/frontend/default/base/assets/images/3-theme-3.jpg');
        $theme->setType('frontend');
        $theme->setPackage('default');
        $theme->setTheme('base');
        $theme->setAuthor('system');
        $theme->setIsActive(0);
        $theme->setCreatedAt(time());
        $theme->setUpdatedAt(time());


        $manager->persist($theme);

        $theme = new Theme();
        $theme->setTitle('Theme 4');
        $theme->setImage('/design/frontend/default/demo/assets/images/4-theme-4.jpg');
        $theme->setType('frontend');
        $theme->setPackage('default');
        $theme->setTheme('demo');
        $theme->setAuthor('system');
        $theme->setIsActive(0);
        $theme->setCreatedAt(time());
        $theme->setUpdatedAt(time());

        $manager->persist($theme);
        $theme->setIsActive(1);
        $manager->persist($theme);

        $manager->flush();
    }

    public function getOrder()
    {
        return 60;
    }
}
