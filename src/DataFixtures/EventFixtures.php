<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Event;
use App\Entity\Media;
use App\Entity\Network;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;
use Xvladqt\Faker\LoremFlickrProvider;
use App\Entity\Ticket;
use App\Entity\Zone;
use Doctrine\Persistence\ObjectManager as PersistenceObjectManager;

class EventFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(PersistenceObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $imageFaker = Factory::create();
        $imageFaker->addProvider(new LoremFlickrProvider($imageFaker));

        $youtubeFaker = Factory::create();
        $youtubeFaker->addProvider(new YoutubeGenerator($youtubeFaker));

        $users = $manager->getRepository(User::class)->findAll();
        $networks = $manager->getRepository(Network::class)->findAll();
        $zones = $manager->getRepository(Zone::class)->findAll();

        $aEvent = [];
        for($e = 0 ; $e < 15 ; $e++) {
            $event = new Event();
            $event->setTitle($faker->catchPhrase()) ;
            $event->setDescription($faker->realText());

            $address = new Address();
            $address->setStreet($faker->streetAddress);
            $address->setCity($faker->city);
            $address->setPlace($faker->streetName);
            $address->setZipCode($faker->postcode);
            $address->setInfo($faker->address);
            $address->setCountry('France');
            $event->setAddress($address);

            $startDate = $faker->dateTimeBetween('-1 month', '+1 month');
            $endDate = clone $startDate;
            $endDate->add(new \DateInterval('PT3H')); // +3 hours
            $event->setStartDate($startDate);
            $event->setEndDate($endDate);
            $event->setNbTickets($faker->randomNumber(2,true));

            $media = new Media();
            $media->setUrl($imageFaker->imageUrl(640, 480, ['group']));
            $event->setImage($media);

            for($m = 0 ; $m < 2 ; $m++) {
                $media = new Media();
                $media->setUrl($imageFaker->imageUrl(640, 480, ['group']));
                $manager->persist($media);
                $event->addImage($media);
            }

            $indexUser = array_rand($users);
            $user = $users[$indexUser];
            $event->setUser($user);

            if($e % 5 == 0) {
                $event->setNetwork(null);
            } else {
                $indexNetwork = array_rand($networks);
                $event->setNetwork($networks[$indexNetwork]);
            }

            $price = $faker->numberBetween(20,40);

            $aTickets = [
                [
                    'name' => 'Event',
                    'price' => $price,
                    'description' => "Rencontrer différents dirigeants durant une session de 3h organisée autour de différentes thématiques"
                ],[
                    'name' => 'Event + Dîner',
                    'price' => $price + 20,
                    'description' => "Continuez vos échanges de 20h à 22h avec un dîner entre dirigeants. Le dîner aura lieu au PLAZA REAL"
                ],[
                    'name' => 'Event + Dîner + Soirée',
                    'price' => $price + 50,
                    'description' => "Prolongez la soirée au BAR VINTAGE place de la bourse (Open Bar jusqu'à 02h)"
                ]
            ];

            foreach ($aTickets as $v) {
                $ticket = new Ticket();
                $ticket->setName($v['name']);
                $ticket->setPrice($v['price']);
                $ticket->setDescription($v['description']);
                $event->addTicket($ticket);
            }

            $indexZone = array_rand($zones);
            $zone = $zones[$indexZone];
            $event->setZone($zone);

            $manager->persist($event);
            $aEvent[] = $event;
        }

        $manager->flush();

    }


    public function getDependencies()
    {
        return array(
            UserFixtures::class,
        );
    }
}
