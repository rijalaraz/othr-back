<?php

namespace App\Service;

use sngrl\PhpFirebaseCloudMessaging\Client;
use sngrl\PhpFirebaseCloudMessaging\Message;
use sngrl\PhpFirebaseCloudMessaging\Notification;
use sngrl\PhpFirebaseCloudMessaging\Recipient\Device;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FirebaseNotificationService
{

    public function sendNotificationMessage (
        $deviceToken = '', 
        $title = '', 
        $body = '',
        $data = []
    ) {
        $serverKey = $_ENV['FIREBASE_SERVER_KEY'];

        $client = new Client();
        $client->setApiKey($serverKey);
        $client->injectGuzzleHttpClient(new \GuzzleHttp\Client());

        $message = new Message();
        $message->addRecipient(new Device($deviceToken));
        $message
            ->setNotification(new Notification($title, $body))
            ->setData($data)
        ;

        $response = $client->send($message);

        $responseContent = $response->getBody()->getContents();

        $result = json_decode($responseContent, true);
        
        if($result['failure']) {
            throw new BadRequestHttpException('Notification: '.$result['results'][0]['error']);
        }
    }
    
}



