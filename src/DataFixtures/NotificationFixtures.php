<?php

namespace App\DataFixtures;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;
use App\Entity\NotificationType;
use Doctrine\Persistence\ObjectManager as PersistenceObjectManager;

class NotificationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(PersistenceObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $users = $manager->getRepository(User::class)->findAll();

        $typeNotif = NotificationType::TYPES;
        $statusNotif = Notification::STATUTES;

        for($n = 0 ; $n < 100 ; $n++) {
            $notif = new Notification();

            $indexType = array_rand($typeNotif);
            $notifType = $typeNotif[$indexType];
            $notif->setType($notifType);

            $notif->setMessage($faker->realText());

            $indexSender = array_rand($users);
            $sender = $users[$indexSender];
            $notif->setSender($sender);

            $receiver = $this->searchReceiver($sender, $users);
            $notif->setReceiver($receiver);

            $indexStatus = array_rand($statusNotif);
            $notif->setStatus($statusNotif[$indexStatus]);

            $manager->persist($notif);

            $creationDate = $faker->dateTimeBetween('-1 month', 'now');
            $notif->setCreatedAt($creationDate);

            $manager->persist($notif);
        }

        $manager->flush();

    }

    private function searchReceiver($sender, $aUser) {
        $indexReceiver = array_rand($aUser);
        $receiver = $aUser[$indexReceiver];
        if($receiver === $sender) {
            return $this->searchReceiver($sender, $aUser);
        }
        return $receiver;
    }


    public function getDependencies()
    {
        return array(
            UserFixtures::class,
        );
    }
}
