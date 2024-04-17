<?php

namespace App\Services\Voter;

use App\Entity\Article;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ArticleVoter extends Voter
{
    const SHOW = 'show';
    const EDIT = 'edit';

    public function __construct(private readonly Security $security) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::SHOW, self::EDIT])) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // you know $subject is a Post object, thanks to `supports()`
        /** @var Article $article */
        $article = $subject;

        return match($attribute) {
            self::SHOW => $this->canShow($article, $user),
            self::EDIT => $this->canEdit($article, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canShow(Article $article, User $user): bool
    {
        // the Post object could have, for example, a method `isPrivate()`
        return $article->getStatus() !== 'DRAFT' || $article->getUser() === $user;
    }

    private function canEdit(?Article $article, User $user): bool
    {
        if (!$article) {
            return true;
        }

        return $article->getUser() === $user;
    }
}
