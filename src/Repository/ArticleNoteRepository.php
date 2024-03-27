<?php

namespace App\Repository;

use App\Entity\ArticleNote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ArticleNote>
 *
 * @method ArticleNote|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticleNote|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticleNote[]    findAll()
 * @method ArticleNote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleNoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticleNote::class);
    }

    //    /**
    //     * @return ArticleNote[] Returns an array of ArticleNote objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ArticleNote
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
