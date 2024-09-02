<?php

namespace App\Security\Voter;

use App\Entity\Transaction;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class TransactionVoter.
 */
class TransactionVoter extends Voter
{
    private const EDIT = 'EDIT';
    private const VIEW = 'VIEW';
    private const DELETE = 'DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof Transaction;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }
        if (!$subject instanceof Transaction) {
            return false;
        }

        return match ($attribute) {
            self::EDIT => $this->canEdit($subject, $user),
            self::VIEW => $this->canView($subject, $user),
            self::DELETE => $this->canDelete($subject, $user),
            default => false,
        };
    }

    /**
     * @param Transaction $transaction
     * @param UserInterface $user
     * @return bool
     */
    private function canEdit(Transaction $transaction, UserInterface $user): bool
    {
        return $transaction->getAuthor() === $user;
    }

    /**
     * @param   Transaction   $transaction
     * @param   UserInterface   $user
     * @return bool
     */
    private function canView(Transaction $transaction, UserInterface $user): bool
    {
        return $transaction->getAuthor() === $user;
    }

    /**
     * @param Transaction $transaction
     * @param UserInterface $user
     * @return bool
     */
    private function canDelete(Transaction $transaction, UserInterface $user): bool
    {
        return $transaction->getAuthor() === $user;
    }
}
