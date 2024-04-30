<?php

namespace App\Article\Repository;

use App\Article\Constant\ArticleStatus;
use App\Entity\Article;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private readonly Security $security)
    {
        parent::__construct($registry, Article::class);
    }

    public function findByUser(User $user): array
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            $resutl =  $this->findAll();
        }

        return $this->createQueryBuilder('a')
            ->where('a.status = :published')
            ->orWhere('a.user = :user')
            ->setParameter('published', ArticleStatus::PUBLISHED)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}
