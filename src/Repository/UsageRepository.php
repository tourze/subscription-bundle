<?php

namespace Tourze\SubscriptionBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\SubscriptionBundle\Entity\Usage;

/**
 * @method Usage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Usage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Usage[] findAll()
 * @method Usage[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Usage::class);
    }
}
