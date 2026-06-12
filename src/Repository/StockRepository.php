<?php

namespace App\Repository;

use App\Entity\Stock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Stock>
 */
class StockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stock::class);
    }

    /**
     * Returns articles whose quantity is at or below the minimum threshold.
     *
     * @return Stock[]
     */
    public function findCritiques(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.quantite <= s.seuil_min')
            ->orderBy('s.quantite', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
