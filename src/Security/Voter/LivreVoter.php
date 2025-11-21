<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Livre;

final class LivreVoter extends Voter
{
    public const EDIT = 'LVRE_EDIT';
    public const VIEW = 'LIVRE_VIEW';
    public const CREATE = 'LIVRE_CREATE';
    public const LIST = 'LIVRE_LIST';
    public const LIST_ALL = 'LIVRE_ALL';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::CREATE, self::LIST, self::LIST_ALL]) ||
        (
            in_array($attribute, [self::EDIT, self::VIEW]) && $subject instanceof Livre 
        );
           
    }

    // Methode qui determine si l'utilisateur a la permission pour l'action demandée
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
       
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return $subject->getUser()->getId() === $user->getId();
            
            // Les autres Cas: .....
            case self::LIST:
            case self::CREATE:
            case self::VIEW:
           // case self::LIST_ALL:

            return true;
        }

        return false;
    }
}
