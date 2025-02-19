<?php

namespace App\Security;

use App\Entity\Notification;
use App\Entity\NotificationType;
use App\Entity\User;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class NotificationVoter extends Voter
{
    const CREATE_NOTIFICATION = 'create_notification';
    
    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false   
        if(!in_array($attribute, [self::CREATE_NOTIFICATION])) {
            return false;
        }

        // only vote on `Notification` objects
        if(!$subject instanceof Notification) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        // the user must be logged in; if not, deny access
        if(!$user instanceof User) {
            return false;
        }
        
        // you know $subject is a Notification object, thanks to `supports()`
        /** @var Notification $post */
        $post = $subject;

        switch ($attribute) {
            case self::CREATE_NOTIFICATION:
                return $this->canCreate($post, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canCreate(Notification $post, User $user)
    {
        switch ($post->getType()) {
            case NotificationType::RECOMMAND_USER:
                // The sender must be the user connected
                if($post->getSender() !== $user) {
                    throw new BadRequestHttpException("Le recommandeur doit être l'utilisateur connecté");
                }

                $thisUser = $post->getReceiver();
                // I can recommand a user only if he is my Swaapr
                if(!$user->isThisUserMySwaapr($thisUser)) {
                    throw new BadRequestHttpException($thisUser->getName()." n'est pas votre Swaapr");
                }

                $post->setMessage($user->getName()." vous a recommandé");

                break;
            
            case NotificationType::SWAAPE_REQUEST:
                // The sender must be the user connected
                if($post->getSender() !== $user) {
                    throw new BadRequestHttpException("Le demandeur doit être l'utilisateur connecté");
                }

                $thisUser = $post->getReceiver();
                // I cannot swaape a user if he is already my Swaapr
                if($user->isThisUserMySwaapr($thisUser)) {
                    throw new BadRequestHttpException($thisUser->getName()." est déjà votre Swaapr");
                }

                $post->setMessage($user->getName()." vous a fait une demande de contact");

                break;
        }
        return true;
    }

}