<?php

namespace PlaygroundDesign\Mapper;

use Doctrine\ORM\EntityManager;
use ZfcBase\Mapper\AbstractDbMapper;
use Zend\Stdlib\Hydrator\HydratorInterface;

class Block extends AbstractDbMapper implements SkinInterface
{
    protected $tableName  = 'skin';

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \PlaygroundDesign\Options\ModuleOptions
     */
    protected $options = 'PlaygroundDesign\Entity\Skin';

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function findAll()
    {
        $er = $this->em->getRepository($this->options->getSkinEntityClass());

        return $er->findAll();
    }

    /**
     * @param $id
     * @return object
     */
    public function findById($id)
    {
        $er = $this->em->getRepository($this->options->getSkinEntityClass());
        $entity = $er->find($id);

        $this->getEventManager()->trigger('find', $this, array('entity' => $entity));

        return $entity;
    }

    /**
     * @param $identifier
     * @return object
     */
    public function findByIdentifier($identifier)
    {
        $er = $this->em->getRepository($this->options->getSkinEntityClass());
        $entity = $er->findOneBy(array('identifier' => $identifier));

        $this->getEventManager()->trigger('find', $this, array('entity' => $entity));

        return $entity;
    }

    /**
     * Find entity by integer id or string identifier
     *
     * @param $identifier
     * @return object
     * @throws \Exception
     */
    public function find($identifier)
    {
        if (is_int($identifier)) {
            $entity = $this->findById($identifier);
        } elseif (is_string($identifier)) {
            $entity = $this->findByIdentifier($identifier);
        } else {
            throw new \Exception('Wrong skin identifier provided.');
        }

        return $entity;
    }

    public function findBy($array)
    {
        $er = $this->em->getRepository($this->options->getSkinEntityClass());

        return $er->findBy($array);
    }
  
  public function findAllBy($sortArray = array())
    {
        $er = $this->em->getRepository($this->options->getSkinEntityClass());

        return $er->findBy(array(), $sortArray);
    }


    public function insert($entity, $tableName = null, HydratorInterface $hydrator = null)
    {
        return $this->persist($entity);
    }

    public function update($entity, $where = null, $tableName = null, HydratorInterface $hydrator = null)
    {
        return $this->persist($entity);
    }
  
  public function remove($entity)
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    protected function persist($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();

        return $entity;
    }
}
