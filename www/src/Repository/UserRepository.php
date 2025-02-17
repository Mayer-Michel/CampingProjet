<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * méthode qui retourne tous les utilisateurs avec ROLE_ADMIN
     * @return User[]
     */
    public function findAllAdmin():array 
    {
        $entityManager = $this->getEntityManager();

        $qb = $entityManager->createQueryBuilder();

        $query = $qb->select([
            'u.id',
            'u.username',
            'u.email',
            'u.roles',
        ])
        ->from(User::class, 'u')
        ->where('u.roles LIKE :roles')
        ->setParameter('roles', '%ROLE_ADMIN%')
        ->getQuery();

        return $query->getResult();
    }

    /**
     * méthode qui retourne tous les utilisateurs avec ROLE_USER
     * @return User[]
     */
    public function findAllUser():array 
    {
        $entityManager = $this->getEntityManager();

        $qb = $entityManager->createQueryBuilder();

        $query = $qb->select([
            'u.id',
            'u.username',
            'u.email',
            'u.roles',
        ])
        ->from(User::class, 'u')
        ->where('u.roles LIKE :roles')
        ->setParameter('roles', '%ROLE_USER%')
        ->getQuery();

        return $query->getResult();
    }
}
