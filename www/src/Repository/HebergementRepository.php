<?php

namespace App\Repository;

use App\Entity\Hebergement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Hebergement>
 */
class HebergementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hebergement::class);
    }

    /**
    * Finds available accommodations between two dates.
    */
    public function findAvailableHebergements(\DateTimeInterface $dateStart, \DateTimeInterface $dateEnd)
    {
        $entityManager = $this->getEntityManager();

        $qb = $entityManager->createQueryBuilder();

        $query = $qb->select(
            'h.id', 
            'h.description', 
            'h.capacity', 
            'h.surface', 
            'r.dateStart', 
            'r.dateEnd'
            )
            ->from(Hebergement::class, 'h')
            ->leftJoin('h.rentals', 'r')
            ->where('r.dateEnd < :dateStart OR r.dateStart > :dateEnd')  // No overlap between rental dates and selected dates
            ->setParameter('dateStart', $dateStart)
            ->setParameter('dateEnd', $dateEnd)
            ->getQuery()->getResult();
        
        return $query;
    }
}
