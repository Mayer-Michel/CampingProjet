<?php

namespace App\Repository;

use App\Entity\Saison;
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

    public function getTarifBySaison(int $id): array
    {
     //on appel l'entity manager
     $entityManager = $this->getEntityManager();
 
     //METHODE AVEC DQL
     $qb = $entityManager->createQueryBuilder();
     //on crée la query
     $query = $qb->select([
         's.id',
         's.label',
         't.prix'
     ])->from(tarif::class, 't')
     ->leftJoin('t.saison', 's')
     ->where('t.id = :id')
     ->setParameter('id', $id)
     ->getQuery()->getResult();
     
   
     return $query;

    }

}
