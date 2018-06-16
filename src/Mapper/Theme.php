<?php

namespace PlaygroundDesign\Mapper;

use Doctrine\ORM\EntityManager;
use ZfcBase\Mapper\AbstractDbMapper;
use PlaygroundDesign\Options\ModuleOptions;

class Theme implements ThemeInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    protected $er;

    /**
     * @var \PlaygroundDesign\Options\ModuleOptions
     */
    protected $options;


    /**
    * __construct
    * @param Doctrine\ORM\EntityManager $em
    * @param PlaygroundDesign\Options\ModuleOptions $options
    *
    */
    public function __construct(EntityManager $em, ModuleOptions $options)
    {
        $this->em      = $em;
        $this->options = $options;
    }

    /**
    * findById : recupere l'entite en fonction de son id
    * @param int $id id du theme
    *
    * @return PlaygroundDesign\Entity\Theme $theme
    */
    public function findById($id)
    {
        return $this->getEntityRepository()->find($id);
    }

    /**
    * findBy : recupere des entites en fonction de filtre
    * @param array $array tableau de filtre
    *
    * @return collection $themes collection de PlaygroundDesign\Entity\Theme
    */
    public function findBy($filter, $order = null, $limit = null, $offset = null)
    {
        return $this->getEntityRepository()->findBy($filter, $order, $limit, $offset);
    }

    /**
    * findActiveTheme : recupere des entites en fonction du filtre active
    * @param boolean $active valeur du champ active
    *
    * @return collection $themes collection de PlaygroundDesign\Entity\Theme
    */
    public function findActiveTheme($active = true)
    {
        return $this->findBy(array('is_active' => $active));
    }

    /**
    * findActiveThemeByArea : recupere des entites active en fonction du filtre Area
    * @param string $area area du theme
    * @param boolean $active valeur du champ active
    *
    * @return collection $themes collection de PlaygroundDesign\Entity\Theme
    */
    public function findActiveThemeByArea($area, $active = true)
    {
        return $this->findBy(array('is_active' => $active,
                                   'area'      => $area));
    }

    /**
    * findThemeByAreaPackageAndBase : recupere des entites en fonction des filtre Area, Package et Theme
    * @param string $area area du theme
    * @param string $package package du theme
    * @param string $base base du theme
    *
    * @return collection $themes collection de PlaygroundDesign\Entity\Theme
    */
    public function findThemeByAreaPackageAndBase($area, $package, $base)
    {
        return $this->findBy(array('area'    => $area,
                                   'package' => $package,
                                   'theme'   => $base));
    }

    /**
    * insert : insert en base une entité theme
    * @param PlaygroundDesign\Entity\Theme $entity theme
    *
    * @return PlaygroundDesign\Entity\Theme $theme
    */
    public function insert($entity)
    {
        return $this->persist($entity);
    }

    /**
    * insert : met a jour en base une entité theme
    * @param PlaygroundDesign\Entity\Theme $entity theme
    *
    * @return PlaygroundDesign\Entity\Theme $theme
    */
    public function update($entity)
    {
        return $this->persist($entity);
    }

    /**
    * insert : met a jour en base une entité theme et persiste en base
    * @param PlaygroundDesign\Entity\Theme $entity theme
    *
    * @return PlaygroundDesign\Entity\Theme $theme
    */
    public function persist($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();

        return $entity;
    }

    /**
    * findAll : recupere toutes les entites
    *
    * @return collection $themes collection de PlaygroundDesign\Entity\Theme
    */
    public function findAll()
    {
        return $this->getEntityRepository()->findAll();
    }

     /**
    * remove : supprimer une entite theme
    * @param PlaygroundDesign\Entity\Theme $entity theme
    *
    */
    public function remove($entity)
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    public function refresh($entity)
    {
        $this->em->refresh($entity);
    }

    /**
    * getEntityRepository : recupere l'entite theme
    *
    * @return PlaygroundDesign\Entity\Theme $theme
    */
    public function getEntityRepository()
    {
        if (null === $this->er) {
            $this->er = $this->em->getRepository('PlaygroundDesign\Entity\Theme');
        }

        return $this->er;
    }
}
