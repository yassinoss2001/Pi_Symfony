<?php

namespace App\Repository;

use App\Entity\Evennemnt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Evennemnt>
 *
 * @method Evennemnt|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evennemnt|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evennemnt[]    findAll()
 * @method Evennemnt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvennemntRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evennemnt::class);
    }

//    /**
//     * @return Evennemnt[] Returns an array of Evennemnt objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Evennemnt
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
