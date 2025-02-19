<?php

namespace App\DataFixtures;

use App\Entity\Event;
use App\Entity\Payment;
use App\Entity\User;
use App\Entity\UserEvent;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager as PersistenceObjectManager;
use Faker\Factory;
use Xvladqt\Faker\LoremFlickrProvider;

class AppFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(PersistenceObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $imageFaker = Factory::create();
        $imageFaker->addProvider(new LoremFlickrProvider($imageFaker));

        $youtubeFaker = Factory::create();
        $youtubeFaker->addProvider(new YoutubeGenerator($youtubeFaker));

        $users = $manager->getRepository(User::class)->findAll();
        $events = $manager->getRepository(Event::class)->findAll();


        $couple = [];
        for ($p = 0; $p < 100; $p++) {
            $indexUser = array_rand($users);
            $user = $users[$indexUser];

            $event = $this->getUserZoneEvent($events, $user);

            if ($event) {
                $couple[] = [$user, $event];
            }
        }

        $statutes = Payment::STATUTES;

        $couples = array_unique($couple, SORT_REGULAR);
        foreach ($couples as $c) {
            [$user, $event] = $c;

            $userEvent = new UserEvent();

            $userEvent->setUser($user);

            $userEvent->setEvent($event);

            //$startDate = new \DateTime($event->getStartDate());
            $startDate = new \DateTime();
            $registrationDate = clone $startDate;
            $registrationDate->sub(new \DateInterval('P' . rand(1, 15) . 'D'));
            $userEvent->setRegistrationDate($registrationDate);
            $userEvent->setNbPlaces($faker->numberBetween(1, 5));

            $ticket = $event->getTickets();

            if ($ticket) {
                $userEvent->setTicketType($ticket[rand(0, 2)]);
            } else {
                $userEvent->setTicketType(null);
            }

            $pay = new Payment();
            $pay->setUser($user);
            $pay->setAmount(rand(25, 100));
            $pay->setCurrency('EUR');

            $indexStatus = array_rand($statutes);
            $status = $statutes[$indexStatus];
            $pay->setPaymentStatus($status);

            $userEvent->setPayment($pay);

            $manager->persist($pay);
            $manager->persist($userEvent);
            $manager->flush();
        }

        $manager->flush();
    }

    private function getUserZoneEvent($events, $user)
    {
        shuffle($events);
        foreach ($events as $event) {
            if ($this->isThisEventInUserZone($event, $user)) {
                return $event;
            }
        }
    }

    private function isThisEventInUserZone(Event $event, User $user): bool
    {
        return $user->getZones()->exists(
            function ($key, $zone) use ($event) {
                return $zone === $event->getZone();
            }
        );
    }


    public function getDependencies()
    {
        return [
            UserFixtures::class,
            EventFixtures::class,
        ];
    }

}
