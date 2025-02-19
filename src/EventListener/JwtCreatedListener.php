<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class JwtCreatedListener {

    private $normalizer;

    public function __construct(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function updateJwtData(JWTCreatedEvent $event) {
        $data = $event->getData();
        $user = $event->getUser();

        $jsonld = $this->normalizer->normalize($user, 'jsonld', ['groups' => ['user_jwt']]);

        $event->setData($jsonld);
    }
}