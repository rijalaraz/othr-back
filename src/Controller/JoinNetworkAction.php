<?php

namespace App\Controller;

use App\Entity\Network;
use App\Entity\NetworkMember;
use App\Entity\Notification;
use App\Entity\NotificationType;
use App\Service\GrantedService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class JoinNetworkAction 
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

        $members = $data->getNetworkMembers();
        foreach($members as $member) {
            if($member->getType() == NetworkMember::TYPE_MAIN_ADMIN || $member->getType() == NetworkMember::TYPE_ADMIN) {
                $receiver = $member->getUser();
                break;
            }
        }

        $notif = new Notification();
        $notif->setType(NotificationType::JOIN_NETWORK);
        $notif->setMessage('Un utilisateur a demandé de rejoindre le réseau'.$data->getName());
        $notif->setSender($currentUser);
        $notif->setReceiver($receiver);
        $notif->setStatus(Notification::SENT);

        $this->em->persist($notif);

        return $data;
    }
}