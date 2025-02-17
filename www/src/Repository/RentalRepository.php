<?php

namespace App\Repository;

use App\Entity\Rental;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Rental>
 */
class RentalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rental::class);
    }

    public function getUserReservationHistory(User $user): array
    {
        $entityManager = $this->getEntityManager();
        $qb = $entityManager->createQueryBuilder();
    
        $query = $qb->select([
            'r.id AS reservationId',
            'r.dateStart',
            'r.dateEnd',
            'r.prixTotal',
            'r.statu',
            'h.id AS hebergementId',
            'h.capacity',
            'h.surface',
            'h.description',
            'h.imagePath',
            't.label AS hebergementType'
        ])
            ->from(Rental::class, 'r')
            ->leftJoin('r.hebergement', 'h')
            ->leftJoin('h.type', 't')
            ->where('r.user = :user')
            ->setParameter('user', $user)
            ->orderBy('r.dateStart', 'DESC')
            ->getQuery();
    
        return $query->getResult();
    }
}
