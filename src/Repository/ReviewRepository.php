<?php

namespace App\Repository;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Review>
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    public function findByPage($offset, $recordsPerPage) {
        return $this->createQueryBuilder('r')
            ->setFirstResult($offset)
            ->setMaxResults($recordsPerPage)
            ->getQuery()
            ->getResult();
    }  
    
    public function findByPageByBook($book, $offset, $recordsPerPage) {
        return $this->createQueryBuilder('r')
            ->andWhere('r.book = :book')
            ->setParameter('book', $book)
            ->setFirstResult($offset)
            ->setMaxResults($recordsPerPage)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Review[] Returns an array of Review objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Review
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}