<?php

namespace App\Repository;

use App\Entity\BookReview;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BookReview>
 */
class BookReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookReview::class);
    }

    //    /**
    //     * @return BookReview[] Returns an array of BookReview objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?BookReview
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
