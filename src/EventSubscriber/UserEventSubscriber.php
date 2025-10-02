<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\User;
use App\Repository\UserRepository;
use Slot\MandrillBundle\Dispatcher;
use Slot\MandrillBundle\Message;
use Symfony\Bridge\Twig\Mime\BodyRenderer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Mailer\EventListener\MessageListener;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Environment;

class UserEventSubscriber implements EventSubscriberInterface
{

    private $router;
    private $mailer;
    private $tokenGenerator;
    private $encoder;
    private $serializer;
    private $dispatcher;
    private $userRepository;
    private $twig;
    private $container;

    public function __construct(

        UserRepository $userRepository,
        UrlGeneratorInterface $router,
        TokenGeneratorInterface $tokenGenerator,
        UserPasswordEncoderInterface $encoder,
        MailerInterface $mailer,
        SerializerInterface $serializer,
        Dispatcher $dispatcher,
        Environment $twig,
        ContainerInterface $container
    ) {

        $this->userRepository     = $userRepository;
        $this->serializer     = $serializer;
        $this->tokenGenerator = $tokenGenerator;
        $this->router         = $router;
        $this->encoder        = $encoder;
        $this->dispatcher     = $dispatcher;
        $this->twig           = $twig;
        $this->container      = $container;

        $messageListener = new MessageListener(null, new BodyRenderer($this->twig));

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber($messageListener);

        $transport = Transport::fromDsn($this->container->getParameter("mailer_dsn"), $eventDispatcher);
        $this->mailer = new Mailer($transport, null, $eventDispatcher);

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
        $this->sendEmailBySymfonyMailer($link, $email);
        // $this->sendEmail($link, $email);
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

    public function sendEmailBySymfonyMailer($url, $emailTo)
    {
        // Create the email
        $email = (new TemplatedEmail())
            ->from(new Address('sender@othr.com', 'Othr'))
            ->to($emailTo)
            ->subject('Othr Reset password')
            ->htmlTemplate('emails/reset-password.html.twig') // Path to the Twig template
            ->context([
                'reset_link' => $url, // Variables passed to the template
            ]);

        // Send the email
        $this->mailer->send($email);

        return new Response('Email sent successfully!');
    }

}
