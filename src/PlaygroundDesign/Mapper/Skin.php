<?php

namespace PlaygroundDesign\Mapper;

use Doctrine\ORM\EntityManager;
use ZfcBase\Mapper\AbstractDbMapper;

use PlaygroundDesign\Options\ModuleOptions;

class Skin implements SkinInterface
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
    * @param int $id id du skin
    *
    * @return PlaygroundDesign\Entity\Skin $skin
    */
    public function findById($id)
    {
        return $this->getEntityRepository()->find($id);
    }

    /**
    * findBy : recupere des entites en fonction de filtre
    * @param array $array tableau de filtre
    *
    * @return collection $skins collection de PlaygroundDesign\Entity\Skin
    */
    public function findBy($array)
    {
        return $this->getEntityRepository()->findBy($array);
    }

    /**
    * insert : insert en base une entité skin
    * @param PlaygroundDesign\Entity\Skin $entity skin
    *
    * @return PlaygroundDesign\Entity\Skin $skin
    */
    public function insert($entity)
    {
        return $this->persist($entity);
    }

    /**
    * insert : met a jour en base une entité skin
    * @param PlaygroundDesign\Entity\Skin $entity skin
    *
    * @return PlaygroundDesign\Entity\Skin $skin
    */
    public function update($entity)
    {
        return $this->persist($entity);
    }

    /**
    * insert : met a jour en base une entité skin et persiste en base
    * @param PlaygroundDesign\Entity\Skin $entity skin
    *
    * @return PlaygroundDesign\Entity\Skin $skin
    */
    protected function persist($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();

        return $entity;
    }

    /**
    * findAll : recupere toutes les entites 
    *
    * @return collection $skins collection de PlaygroundDesign\Entity\Skin
    */
    public function findAll()
    {
        return $this->getEntityRepository()->findAll();
    }

     /**
    * remove : supprimer une entite skin
    * @param PlaygroundDesign\Entity\Skin $entity skin
    *
    */
    public function remove($entity)
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    /**
    * getEntityRepository : recupere l'entite skin
    *
    * @return PlaygroundDesign\Entity\Skin $skin
    */
    public function getEntityRepository()
    {
        if (null === $this->er) {
            $this->er = $this->em->getRepository('PlaygroundDesign\Entity\Skin');
        }

        return $this->er;
    }
}
