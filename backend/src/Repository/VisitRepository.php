<?php

namespace App\Repository;

use App\Entity\Link;
use App\Entity\Visit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Visit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Visit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Visit[]    findAll()
 * @method Visit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VisitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Visit::class);
    }

    /**
     * @param Link $link
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCountryLinkStatistic(Link $link)
    {
        return $this->createQueryBuilder('v')
            ->select('count(v.id)')
            ->join('v.country', 'c')
            ->andWhere('v.link = :link')
            ->setParameter('link', $link)
            ->groupBy('v.country')
            ->getQuery()
            ->getOneOrNullResult();

    }

    /**
     * @param Link $link
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPlatformLinkStatistic(Link $link)
    {
        return $this->createQueryBuilder('v')
            ->select('count(v.id)')
            ->join('v.platform', 'p')
            ->andWhere('v.link = :link')
            ->setParameter('link', $link)
            ->groupBy('v.platform')
            ->getQuery()
            ->getOneOrNullResult();

    }

    /**
     * @param Link $link
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTotalVisits(Link $link)
    {
        return $this->createQueryBuilder('v')
            ->select('count(v.id)')
            ->andWhere('v.link = :link')
            ->setParameter('link', $link)
            ->getQuery()
            ->getSingleScalarResult();

    }
    // /**
    //  * @return Visit[] Returns an array of Visit objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Visit
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
