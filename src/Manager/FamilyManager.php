<?php

namespace App\Manager;

use App\Entity\Family;
use App\Entity\Person;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class FamilyManager
 */
class FamilyManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * FamilyManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @return Family
     */
    public function create()
    {
        return new Family();
    }

    /**
     * @param Family $family
     */
    public function save(Family $family)
    {
        if(null === $family->getId())
        {
            $this->em->persist($family);
        }

        $this->em->flush();
    }

    /**
     * @param string $fid
     * @return Family|mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getOrCreateByFid(string $fid)
    {
        $family = $this->getByFid($fid);
        if(empty($family)) {
            $family = $this->create();
        }

        return $family;
    }

    /**
     * @param string $fid
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getByFid(string $fid)
    {
        return $this->em->getRepository(Family::class)->findByFid($fid);
    }

    /**
     * @param Person $person
     * @return mixed
     */
    public function getByParent(Person $person)
    {
        return $this->em->getRepository(Family::class)->findByParent($person);
    }


}
