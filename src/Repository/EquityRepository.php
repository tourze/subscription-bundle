<?php

namespace SubscriptionBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use SubscriptionBundle\Entity\Equity;

/**
 * @method Equity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Equity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Equity[] findAll()
 * @method Equity[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EquityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Equity::class);
    }
}
