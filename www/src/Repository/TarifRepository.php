<?php

namespace App\Repository;

use App\Entity\Hebergement;
use App\Entity\Tarif;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tarif>
 */
class TarifRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tarif::class);
    }

    public function getHebergementTarifByDate(int $id, \DateTime $dateStart, \DateTime $dateEnd): ?array
    {
        // Call the entity manager
        $entityManager = $this->getEntityManager();

        // Create the DQL query
        $qb = $entityManager->createQueryBuilder();
        $query = $qb->select([
            't.prix',  // Price
            's.label'  // Season label
        ])
            ->from(Hebergement::class, 'h')
            ->leftJoin('h.tarif', 't')
            ->leftJoin('t.saison', 's')
            ->where('h.id = :id')  // Ensure we're getting the correct Hebergement
            ->andWhere(':dateStart BETWEEN s.dateStart AND s.dateEnd')  // Check if dateStart is within season range
            ->andWhere(':dateEnd BETWEEN s.dateStart AND s.dateEnd')  // Check if dateEnd is within season range
            ->setParameter('id', $id)
            ->setParameter('dateStart', $dateStart)
            ->setParameter('dateEnd', $dateEnd)
            ->getQuery();

        // Execute the query and fetch the result
        return $query->getResult(); // Returns an array of results or an empty array if no match is found
    }
}
