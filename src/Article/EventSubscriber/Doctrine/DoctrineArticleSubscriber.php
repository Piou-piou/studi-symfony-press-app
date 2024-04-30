<?php

namespace App\Article\EventSubscriber\Doctrine;

use App\Article\Constant\ArticleStatus;
use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Article::class)]
class DoctrineArticleSubscriber
{
    public function __construct(private readonly Security $security)
    {
    }

    public function prePersist(Article $article, PrePersistEventArgs $args): void
    {
        $article->setStatus(ArticleStatus::DRAFT);
        $article->setUser($this->security->getUser());
    }
}
