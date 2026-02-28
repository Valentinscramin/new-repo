<?php

namespace App\Repository;

use App\Entity\Comparison;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comparison>
 *
 * @method Comparison|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comparison|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comparison[]    findAll()
 * @method Comparison[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComparisonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comparison::class);
    }

    public function save(Comparison $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Comparison $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByUser($user): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :user')
            ->setParameter('user', $user)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
