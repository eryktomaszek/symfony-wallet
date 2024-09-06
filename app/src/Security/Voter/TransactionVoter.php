<?php
/**
 * This file is part of the Budgetly project.
 *
 * (c) Eryk Tomaszek 2024 <eryk.tomaszek@student.uj.edu.pl>
 */

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

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute The attribute being voted on
     * @param mixed  $subject   The subject being voted on
     *
     * @return bool True if the voter supports the attribute and subject, false otherwise
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof Transaction;
    }

    /**
     * Perform a vote to decide if the user can perform the given action.
     *
     * @param string         $attribute The action being voted on (EDIT, VIEW, DELETE)
     * @param mixed          $subject   The subject (Transaction)
     * @param TokenInterface $token     The security token representing the user
     *
     * @return bool True if the user can perform the action, false otherwise
     */
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
     * Determines if the user can edit the transaction.
     *
     * @param Transaction   $transaction The transaction entity
     * @param UserInterface $user        The user performing the action
     *
     * @return bool True if the user can edit the transaction, false otherwise
     */
    private function canEdit(Transaction $transaction, UserInterface $user): bool
    {
        return $transaction->getAuthor() === $user;
    }

    /**
     * Determines if the user can view the transaction.
     *
     * @param Transaction   $transaction The transaction entity
     * @param UserInterface $user        The user performing the action
     *
     * @return bool True if the user can view the transaction, false otherwise
     */
    private function canView(Transaction $transaction, UserInterface $user): bool
    {
        return $transaction->getAuthor() === $user;
    }

    /**
     * Determines if the user can delete the transaction.
     *
     * @param Transaction   $transaction The transaction entity
     * @param UserInterface $user        The user performing the action
     *
     * @return bool True if the user can delete the transaction, false otherwise
     */
    private function canDelete(Transaction $transaction, UserInterface $user): bool
    {
        return $transaction->getAuthor() === $user;
    }
}
