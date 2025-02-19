<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Notification;
use App\Entity\NotificationType;
use App\Service\FirebaseNotificationService;
use Slot\MandrillBundle\Dispatcher;
use Slot\MandrillBundle\Message;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class NotificationDataPersister implements ContextAwareDataPersisterInterface
{
    private $decorated;
    private $notificationService;
    private $dispatcher;

    public function __construct(DataPersisterInterface $decorated,  FirebaseNotificationService $notificationService, Dispatcher $dispatcher)
    {
        $this->decorated = $decorated;
        $this->notificationService = $notificationService;
        $this->dispatcher = $dispatcher;
    }

    public function supports($data, array $context = []): bool
    {
        return $this->decorated->supports($data, $context);
    }

    public function persist($data, array $context = [])
    {
        $result = $this->decorated->persist($data, $context);

        if($data instanceof Notification && ($context['collection_operation_name'] ?? null) === 'post')
        {
            $this->sendEmail($data);
            $this->sendNotification($data);
        }

        return $result;
    }

    public function remove($data, array $context = [])
    {
        return $this->decorated->remove($data, $context);
    }

    private function sendEmail(Notification $notif)
    {
        $message = new Message();
        $message
            ->setFromName('Othr')
            ->addTo($notif->getReceiver()->getEmail())
            ->setSubject('Othr Notification')
            ->addGlobalMergeVar('SENDER_NAME', $notif->getSender()->getName())
            ->addGlobalMergeVar('RECEIVER_NAME', $notif->getReceiver()->getName());

        switch ($notif->getType()) {
            case NotificationType::SWAAPE_REQUEST:
                $templateName = 'othr-swaape-request';
            break;
           
            case NotificationType::RECOMMAND_USER:
                $templateName = 'othr-recommandation';
            break;
        }

        $result = $this->dispatcher->send(
            $message,
            $templateName
        );
        if($result[0]['status'] == 'rejected') {
            throw new BadRequestHttpException('Email: '.$result[0]['reject_reason']);
        }
    }

    private function sendNotification(Notification $notif)
    {
        $reg = $notif->getReceiver()->getUserTokenDevices()->last();
        
        $this->notificationService->sendNotificationMessage (
            $reg->getToken(),
            'Othr Notification',
            $notif->getMessage()
        );
    }
}