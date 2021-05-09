<?php

namespace App\Repository;

use App\Entity\Link;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Link|null find($id, $lockMode = null, $lockVersion = null)
 * @method Link|null findOneBy(array $criteria, array $orderBy = null)
 * @method Link[]    findAll()
 * @method Link[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Link::class);
    }

    /**
     * @param User $user
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function userLinksQueryBulder(User $user, $status): QueryBuilder
    {
        $status = json_encode($status, true);

        return $this->createQueryBuilder('l')
            ->andWhere('l.owner = :owner')
            ->setParameter('owner', $user)
            ->orderBy('l.id', 'ASC');
    }

}
