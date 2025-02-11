<?php

namespace App\Repository;

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

    //    /**
    //     * @return Tarif[] Returns an array of Tarif objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Tarif
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    // public function getTarif(Hebergement $hebergement, DateTimeInterface $date): ?float
    // {
    //     // Vérifier la fermeture hivernale
    //     $mois = (int) $date->format('m');
    //     if ($mois >= 10 || $mois <= 3) {
    //         return null; // Indique que l'hébergement est fermé
    //     }

    //     // Récupérer le tarif correspondant à la période
    //     $tarif = $this->tarifRepository->findOneByDateAndHebergement($date, $hebergement);

    //     return $tarif ? $tarif->getPrix() : null;
    // }
}
