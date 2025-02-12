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
            't.type',
    
        ])->from(Hebergement::class, 'h')
        ->leftJoin('h.typeId', 't')
        ->where('h.id = :id')
        ->setParameter('id', $id)
        ->getQuery();

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
            'e.equipement'
        ])->from(Hebergement::class, 'h')
        ->leftJoin('h.equipementID', 'e')
        ->where('h.id = :id')
        ->setParameter('id', $id)
        ->getQuery();

        return $query->getResult();
    }

    /**
     * méthode qui récupère les images lié à l'hebergement
     * @param int $id
     * @return array
    */
    public function imageByHeberg(int $id): array
    {
        $entityManager = $this->getEntityManager();

        $qb = $entityManager->createQueryBuilder();

        $query = $qb->select([
            'i.id',
            'i.imagePath'
        ])->from(Hebergement::class, 'h')
        ->leftJoin('h.imageId', 'i')
        ->where('h.id = :id')
        ->setParameter('id', $id)
        ->getQuery()->getResult();

        return $query;
    }

        
}
