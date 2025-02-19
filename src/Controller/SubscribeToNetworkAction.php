<?php

namespace App\Controller;

use App\Entity\Network;
use App\Service\GrantedService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class SubscribeToNetworkAction 
{
    private $security;
    private $grantedService;
    private $em;

    public function __construct(Security $security, GrantedService $grantedService, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->grantedService = $grantedService;
        $this->em = $em;
    }

    public function __invoke(Network $data)
    {
        $currentUser = $this->security->getUser();
        $currentUser->addSubscription($data);
        $this->em->persist($currentUser);
        return $data;
    }
}