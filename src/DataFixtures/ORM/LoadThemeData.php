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
        $theme->setImage('/theme/images/screenshots/1-Penguins.jpg');
        $theme->setArea('admin');
        $theme->setPackage('default');
        $theme->setTheme('base');
        $manager->persist($theme);

        $theme = new Theme();
        $theme->setTitle('Theme 2');
        $theme->setImage('/theme/images/screenshots/2-Desert.jpg');
        $theme->setArea('admin');
        $theme->setPackage('default');
        $theme->setTheme('demo');
        $manager->persist($theme);
        $theme->setIsActive(1);
        $manager->persist($theme);

        $theme = new Theme();
        $theme->setTitle('Theme 3');
        $theme->setImage('/theme/images/screenshots/3-Tulips.jpg');
        $theme->setArea('frontend');
        $theme->setPackage('default');
        $theme->setTheme('base');
        $manager->persist($theme);

        $theme = new Theme();
        $theme->setTitle('Theme 4');
        $theme->setImage('/theme/images/screenshots/4-Koala.jpg');
        $theme->setArea('frontend');
        $theme->setPackage('default');
        $theme->setTheme('demo');
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
