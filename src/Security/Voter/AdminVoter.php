<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class AdminVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
       
        if (!$user instanceof UserInterface) {
            return false;
        }

        $allowedRoles = ['ROLE_ADMIN', 'ROLE_MANAGER', 'ROLE_DG'];
        return count(array_intersect($allowedRoles, $user->getRoles())) > 0;   
        
    }
}
