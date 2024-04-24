<?php

namespace App\Comment\Voter;

use App\Entity\Comment;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentVoter extends Voter
{
    const SHOW = 'show';
    const CREATE = 'create';
    const EDIT = 'edit';
    const DELETE = 'delete';

    public function __construct(private readonly Security $security) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::SHOW, self::CREATE, self::EDIT, self::DELETE])) {
            return false;
        }
        if (null === $subject) {
            return true;
        }
        if (!$subject instanceof Comment) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // you know $subject is a Post object, thanks to `supports()`
        /** @var Comment $comment */
        $comment = $subject;

        return match($attribute) {
            self::EDIT => $this->canEdit($comment, $token->getUser()),
            self::CREATE => $this->canEdit($comment, $token->getUser()),
            self::DELETE => $this->canEdit($comment, $token->getUser()),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canEdit(?Comment $comment, UserInterface $user): bool
    {
        if (!$comment) {
            return true;
        }

        return $comment->getUser() === $user;
    }
}
