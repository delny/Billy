<?php

namespace App\Repository;

use App\Entity\Family;
use App\Entity\Person;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Family|null find($id, $lockMode = null, $lockVersion = null)
 * @method Family|null findOneBy(array $criteria, array $orderBy = null)
 * @method Family[]    findAll()
 * @method Family[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FamilyRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Family::class);
    }

    /**
     * @param string $fid
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByFid(string $fid)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.fid = :fid')
            ->setParameter('fid', $fid)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Person $parent
     * @return mixed
     */
    public function findByParent(Person $parent)
    {
        return $this->createQueryBuilder('f')
            ->leftJoin('f.father', 'father')
            ->leftJoin('f.mother', 'mother')
            ->orWhere('father.id = :parentId')
            ->orWhere('mother.id = :parentId')
            ->setParameter('parentId', $parent->getId())
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Family[] Returns an array of Family objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Family
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
