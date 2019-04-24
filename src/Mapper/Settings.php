<?php

namespace PlaygroundDesign\Mapper;

use Doctrine\ORM\EntityManager;

use PlaygroundDesign\Options\ModuleOptions;

class Settings
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
    * @param int $id id du settings
    *
    * @return PlaygroundDesign\Entity\Settings $settings
    */
    public function findById($id)
    {
        return $this->getEntityRepository()->find($id);
    }

    /**
    * findBy : recupere des entites en fonction de filtre
    * @param array $array tableau de filtre
    *
    * @return collection $settingss collection de PlaygroundDesign\Entity\Settings
    */
    public function findBy($filter, $order = null, $limit = null, $offset = null)
    {
        return $this->getEntityRepository()->findBy($filter, $order, $limit, $offset);
    }

    /**
    * insert : insert en base une entité
    * @param PlaygroundDesign\Entity\Settings $entity settings
    *
    * @return PlaygroundDesign\Entity\Settings $settings
    */
    public function insert($entity)
    {
        return $this->persist($entity);
    }

    /**
    * insert : met a jour en base une entité
    * @param PlaygroundDesign\Entity\Settings $entity settings
    *
    * @return PlaygroundDesign\Entity\Settings $settings
    */
    public function update($entity)
    {
        return $this->persist($entity);
    }

    /**
    * insert : met a jour en base une entité settings et persiste en base
    * @param PlaygroundDesign\Entity\Settings $entity settings
    *
    * @return PlaygroundDesign\Entity\Settings $settings
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
    * @return collection $settingss collection de PlaygroundDesign\Entity\Settings
    */
    public function findAll()
    {
        return $this->getEntityRepository()->findAll();
    }

     /**
    * remove : supprimer une entite settings
    * @param PlaygroundDesign\Entity\Settings $entity settings
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
    * getEntityRepository : recupere l'entite settings
    *
    * @return PlaygroundDesign\Entity\Settings $settings
    */
    public function getEntityRepository()
    {
        if (null === $this->er) {
            $this->er = $this->em->getRepository('PlaygroundDesign\Entity\Settings');
        }

        return $this->er;
    }
}
