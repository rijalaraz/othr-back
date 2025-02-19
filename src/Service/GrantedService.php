<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

/**
 * GrantedService permet de vérifier si l’utilisateur a bien les droits tout en vérifiant la hiérarchie des roles
 */
class GrantedService 
{
    private $accessDecisionManager;

    public function __construct(AccessDecisionManagerInterface $accessDecisionManager)
    {
        $this->accessDecisionManager = $accessDecisionManager;
    }

    public function isGranted(User $user, $attributes, $object = null) {
        if(!is_array($attributes))
            $attributes = [$attributes];

        $token = new UsernamePasswordToken($user, 'none', 'none', $user->getRoles());

        return ($this->accessDecisionManager->decide($token, $attributes, $object));
    }
}