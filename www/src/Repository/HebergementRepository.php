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
     * méthode qui récupère tous les hebergements disponible par rapport les input du client avec les données
     * @param int $dateStart, $dateEnd, ?int $type, int $adults, int $kids
     * @return array
     */
    public function findAvailableHebergements($dateStart, $dateEnd, ?int $type, int $adults, int $kids): array
    {
        $entityManager = $this->getEntityManager();
        $qb = $entityManager->createQueryBuilder();

        $qb->select([
            'h.id',
            'h.capacity',
            'h.surface',
            'h.description',
            'h.imagePath',
            't.label'
        ])
            ->from(Hebergement::class, 'h')
            ->leftJoin('h.type', 't')
            ->leftJoin('h.rentals', 'r') // Join rentals
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->isNull('r.id'), // No reservations at all
                    $qb->expr()->orX(
                        $qb->expr()->lt('r.dateEnd', ':dateStart'),  // Last reservation ended before the new one starts
                        $qb->expr()->gt('r.dateStart', ':dateEnd')   // Next reservation starts after the new one ends
                    )
                )
            )
            ->setParameter('dateStart', $dateStart)
            ->setParameter('dateEnd', $dateEnd);

        // Filter by type if provided
        if ($type !== null) {
            $qb->andWhere('h.type = :type')
                ->setParameter('type', $type);
        }

        // Ensure the Hebergement can accommodate total guests (adults + kids)
        $totalGuests = $adults + $kids;
        $qb->andWhere('h.capacity >= :totalGuests')
            ->setParameter('totalGuests', $totalGuests);

        // Debugging: Dump the SQL to check what’s happening
        dump($qb->getQuery()->getSQL(), $qb->getParameters()); // Debug
        return $qb->getQuery()->getResult();
    }


    /**
     * méthode qui récupère un hebergement par son id avec les données
     * @param int $id
     * @return array
     */
    public function hebergementDetail(int $id): array
    {
        //on appel l'entity manager
        $entityManager = $this->getEntityManager();
        //METHODE AVEC DQL
        $qb = $entityManager->createQueryBuilder();
        //on crée la query
        $query = $qb->select([
            'h.id',
            'h.surface',
            'h.description',
            'h.capacity',
            'h.imagePath',
            't.label',

        ])->from(Hebergement::class, 'h')
            ->leftJoin('h.type', 't')
            ->where('h.id = :id')
            ->setParameter('id', $id)
            ->getQuery();
        // dd($query);

        return $query->getOneOrNullResult();
    }

    /**
     * méthode qui récupère les équipements lié à l'hebergement
     * @param int $id
     * @return array
     */
    public function equipementByHeberg(int $id): array
    {
        $entityManager = $this->getEntityManager();

        $qb = $entityManager->createQueryBuilder();

        $query = $qb->select([
            'e.id',
            'e.label'
        ])->from(Hebergement::class, 'h')
            ->leftJoin('h.equipement', 'e')
            ->where('h.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

        return $query->getResult();
    }

}
