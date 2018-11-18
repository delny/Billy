<?php

namespace App\Manager;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Person;

/**
 * Class PersonManager
 * @package App\Manager
 */
class PersonManager 
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * PersonManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @return Person
     */
    public function create()
    {
        return new Person();
    }

    /**
     * @param Person $person
     */
    public function save(Person $person)
    {
        if(null === $person->getId())
        {
            $this->em->persist($person);
        }

        $this->em->flush();
    }

    /**
     * @param $id
     * @return null|object
     */
    public function getById($id)
    {
        return $this->em->getRepository(Person::class)->find($id);
    }
}