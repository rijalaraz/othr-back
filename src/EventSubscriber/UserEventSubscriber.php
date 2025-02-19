<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\User;
use App\Repository\UserRepository;
use Slot\MandrillBundle\Dispatcher;
use Slot\MandrillBundle\Message;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class UserEventSubscriber implements EventSubscriberInterface
{

    private $router;
    private $mailer;
    private $tokenGenerator;
    private $encoder;
    private $serializer;
    private $dispatcher;
    private $userRepository;

    public function __construct(

        UserRepository $userRepository,
        UrlGeneratorInterface $router,
        TokenGeneratorInterface $tokenGenerator,
        UserPasswordEncoderInterface $encoder,
        MailerInterface $mailer,
        SerializerInterface $serializer,
        Dispatcher $dispatcher
    ) {

        $this->userRepository     = $userRepository;
        $this->serializer     = $serializer;
        $this->tokenGenerator = $tokenGenerator;
        $this->mailer         = $mailer;
        $this->router         = $router;
        $this->encoder        = $encoder;
        $this->dispatcher     = $dispatcher;

    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['changePassword', EventPriorities::PRE_VALIDATE],
        ];
    }

    public function changePassword(GetResponseForControllerResultEvent $event)
    {
        $data   = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();
        
        if ($data instanceof User && $method === "PATCH") {
            $route = $event->getRequest()->attributes->get('_route');
            switch ($route) {
                case "api_users_forgotten_password_item":
                    $this->forgottenPassword($event, $data->getEmail());
                    break;
                case "api_users_reset_password_item":
                   $this->resetPassword($event, $event->getRequest()->attributes->get('token'), $data->getPassword()); 
                    break;
            }

        }

    }

    public function forgottenPassword($event, $email)
    {
        $user = $this->userRepository->findOneBy(["email" => $email]);
        $user->setToken($this->tokenGenerator->generateToken());
        $this->userRepository->upgradePassword($user, $user->getPassword());
        $event->setControllerResult($user);
        $link = $this->createLink($user->getToken());
        $this->sendEmail($link, $email);
    }

    public function resetPassword($event, $token, $password)
    {
        $user = $this->userRepository->findOneBy(["token" => $token]);
        $user->setToken(null);
        $hash = $this->encoder->encodePassword($user, $password);
        $this->userRepository->upgradePassword($user, $hash);
        $event->setControllerResult($user);

    }

    public function createLink($token)
    {

        $url = $this->router->generate(
            'api_users_reset_password_item',
            array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL
        );
        return $url;
    }

    public function sendEmail($url, $emailTo)
    {
        $message = new Message();
        $message
            ->setFromName('Othr')
            ->addTo($emailTo)
            ->setSubject('Othr Reset password')
            ->addGlobalMergeVar('RESET_PASSWORD_LINK', $url);

        $templateName = 'othr-reset-password';

        $result = $this->dispatcher->send(
            $message,
            $templateName
        );

        if ($result[0]['status'] == 'rejected') {
            throw new BadRequestHttpException('Email: ' . $result[0]['reject_reason']);
        }
    }

}
