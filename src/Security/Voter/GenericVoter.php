<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class GenericVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const VIEW = 'VIEW';
    public const CREATE = 'CREATE';
    public const LIST = 'LIST';
     public const DELETE = 'DELETE';
    public const LIST_ALL = 'LIST_ALL';

    protected function supports(string $attribute, mixed $subject): bool
    {
       //Déclaration de toutes les permissions supportées
       $supportedAttributes = [self::CREATE, self::LIST, self::EDIT, self::LIST_ALL, self::VIEW, self::DELETE];

       if(!in_array($attribute, $supportedAttributes)) {
        return false;
       }

       // cas: CREATE, LIST, LIST_ALL => pas besoin de sujet
       if(in_array($attribute, [self::CREATE, self::VIEW, self::LIST, self::LIST_ALL])) {
        return true;
       }

       // cas: EDIT, DELETE et VIEW => on doit avoir un sujet qui implemente la methode getUser()
       return is_object($subject) && method_exists($subject, 'getUser');

    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
            $owner = $subject->getUser();
            if($owner === null) {
                return false;
            }
            return $owner->getId() === $user->getId();

            case self::VIEW:
                return true;
            case self::CREATE:
                return true;
            case self::LIST:
                return true;
            case self::DELETE:
                return true;

            case self::LIST_ALL:
                $allowedRoles = ['ROLE_ADMIN', 'ROLE_MANAGER', 'ROLE_DG'];
                return count(array_intersect($allowedRoles, $user->getRoles())) > 0;      
        }

        return false;
    }
}
