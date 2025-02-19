<?php

namespace App\DataFixtures;

use App\Entity\Activity;
use App\Entity\Address;
use App\Entity\Argument;
use App\Entity\Color;
use App\Entity\Event;
use App\Entity\Media;
use App\Entity\Network;
use App\Entity\NetworkType;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;
use Xvladqt\Faker\LoremFlickrProvider;
use App\Entity\Service;
use App\Entity\WorkingSector;
use App\Entity\Zone;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager as PersistenceObjectManager;


class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(PersistenceObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $imageFaker = Factory::create();
        $imageFaker->addProvider(new LoremFlickrProvider($imageFaker));

        $youtubeFaker = Factory::create();
        $youtubeFaker->addProvider(new YoutubeGenerator($youtubeFaker));

        $colors = [
            ['code' => '#008000'],
            ['code' => '#800080'],
            ['code' => '#FFD700'],
            ['code' => '#FF00FF'],
            ['code' => '#990000'],
            ['code' => '#00CED1'],
            ['code' => '#8A2BE2'],
            ['code' => '#00FF00'],
            ['code' => '#0000FF'],
            ['code' => '#660066']
        ];

        $aColors = [];
        foreach ($colors as $v) {
            $color = new Color();
            $color->setCode($v['code']);
            $manager->persist($color);
            $aColors[] = $color;
        }
        $manager->flush();



        $aService = [];
        for($s = 0 ; $s < 10 ; $s++) {
            $service = new Service();
            $service->setName($faker->vat);
            $service->setDescription($faker->realText());
            $aService[] = $service;
            $manager->persist($service);
        }
        $manager->flush();


        $aWorkingSectors = $manager->getRepository(WorkingSector::class)->findAll();


        $aNetworkTypes = [
            [
                "name" => "Réseaux d'affaires",
                "description" => "Les réseaux d'affaires sont des groupes d'entrepreneurs qui..."
            ],
            [
                "name" => "Clubs",
                "description" => "Les clubs sont des groupes de travailleurs qui..."
            ],
            [
                "name" => "Syndicats",
                "description" => "Les syndicats sont des groupes d'associations qui défendent les intérêts des travailleurs"
            ],
            [
                "name" => "Clubs service",
                "description" => "Les clubs service sont des groupes de prestataires de service qui..."
            ],
            [
                "name" => "Groupements de métiers",
                "description" => "Les groupements de métiers sont des groupes qui..."
            ]
        ];

        $netType = [];
        foreach ($aNetworkTypes as $type) {
            $networkType = new NetworkType();
            $networkType->setName($type['name']);
            $networkType->setDescription($type['description']);
            $networkType->setColor($faker->hexColor);
            $netType[] = $networkType;
            $manager->persist($networkType);
        }
        $manager->flush();


        $aNetwork = [];
        for($n = 0 ; $n < 15 ; $n++) {

            $network = new Network();

            $network->setName($faker->userName);

            $address = new Address();
            $address->setStreet($faker->streetAddress);
            $address->setCity($faker->city);
            $address->setPlace($faker->streetName);
            $address->setZipCode($faker->postcode);
            $address->setInfo($faker->address);
            $address->setCountry('France');
            $network->setAddress($address);

            $image = new Media();
            $image->setUrl($imageFaker->imageUrl(640, 480, ['group']));
            $network->setImage($image);

            $indexType = array_rand($netType);
            $network->setType($netType[$indexType]);

            $logo = new Media();
            $logo->setUrl($imageFaker->imageUrl(640, 480, ['logo']));
            $network->setLogo($logo);

            $imageRepresentation = new Media();
            $imageRepresentation->setUrl($imageFaker->imageUrl(640, 480, ['group']));
            $network->setImageRepresentation($imageRepresentation);

            $network->setDescriptionWho($faker->realText());

            $video = new Media();
            $video->setUrl($youtubeFaker->youtubeUri());
            $network->setVideo($video);

            $network->setDescriptionWhy($faker->realText());

            $imageDescription = new Media();
            $imageDescription->setUrl($imageFaker->imageUrl(640, 480, ['group']));
            $network->setImageDescription($imageDescription);

            $network->setDescriptionHow($faker->realText());

            $network->setEmail($faker->companyEmail);

            $network->setWebsite($faker->domainName);

            $network->setNbMembersOffline($faker->numberBetween(100,500));

            $aNetwork[] = $network;

            $manager->persist($network);
        }
        $manager->flush();


        $aZones = $manager->getRepository(Zone::class)->findAll();


        $user = new User();
        $user->setFirstName('Montest');
        $user->setLastName('TEST');
        $user->setEmail('test@test.com');
        $user->setPassword('$2y$12$ORnjH4CGJwueY5.yOlikCuK0Ti3E4r/269NrbYFkVhn5l6paRJVPO');
        $user->setPhoneNumber('+33-655-5615-98');

        $indexColor = array_rand($aColors);
        $color = $aColors[$indexColor];
        $user->setColor($color);

        $user->setJob($faker->jobTitle);

        $image = new Media();
        $image->setUrl($imageFaker->imageUrl(640, 480, ['girl']));
        $user->setImage($image);

        $address = new Address();
        $address->setStreet($faker->streetAddress);
        $address->setCity($faker->city);
        $address->setPlace($faker->streetName);
        $address->setZipCode($faker->postcode);
        $address->setInfo($faker->address);
        $address->setCountry('France');

        $user->setAddress($address);

        $indexNetwork = array_rand($aNetwork);
        $user->addSubscription($aNetwork[$indexNetwork]);
        $indexNetwork = array_rand($aNetwork);
        $user->addSubscription($aNetwork[$indexNetwork]);

        for($m = 0 ; $m < 2 ; $m++) {
            $media = new Media();
            $media->setUrl($imageFaker->imageUrl(640, 480, ['group']));
            $manager->persist($media);
            $user->addAchievement($media);
        }

        $user->setDescription($faker->realText());

        $logo = new Media();
        $logo->setUrl($imageFaker->imageUrl(640, 480, ['logo']));
        $user->setLogo($logo);
        $user->setWebsite($faker->domainName);

        for($a = 0 ; $a < 2 ; $a++) {
            $activity = new Activity();
            $activity->setName($faker->catchPhrase());
            $activity->setDescription($faker->realText());
            $manager->persist($activity);
            $user->addActivity($activity);
        }

        for($m = 0 ; $m < 1 ; $m++) {
            $media = new Media();
            $media->setUrl($imageFaker->imageUrl(640, 480, ['work','job']));
            $manager->persist($media);
            $user->addActivityImage($media);
        }

        for($a = 0 ; $a < 2 ; $a++) {
            $argument = new Argument();
            $argument->setTitle($faker->catchPhrase());
            $argument->setDescription($faker->realText());
            $manager->persist($argument);
            $user->addArgument($argument);
        }

        for($c = 0 ; $c < 1 ; $c++) {
            $media = new Media();
            $media->setUrl($imageFaker->imageUrl(640, 480, ['logo']));
            $manager->persist($media);
            $user->addCustomer($media);
        }

        $video = new Media();
        $video->setUrl($youtubeFaker->youtubeUri());
        $user->setVideo($video);

        $services = $this->getUserServices($aService);
        for($s = 0 ; $s < 2 ; $s++) {
            $service = $services[$s];
            $user->addService($service);
        }

        $indexWS = array_rand($aWorkingSectors);
        $ws = $aWorkingSectors[$indexWS];
        $user->setWorkingSector($ws);

        $user->setRoles([User::ROLE_SUPER_ADMIN]);

        $aUser[] = $user;
        $manager->persist($user);
        $creationDate = $faker->dateTimeBetween('-1 month', 'now');
        $user->setCreatedAt($creationDate);

        $nbZone = rand(7, 10);
        for ($z = 0 ; $z < $nbZone ; $z++) {
            $indexZone = array_rand($aZones);
            $zone = $aZones[$indexZone];
            $user->addZone($zone);
        }

        $manager->persist($user);

        $manager->flush();



        $aUser = [];
        $aRoles = User::ROLES;

        for($u = 0 ; $u < 8 ; $u++) {
            $user = new User();
            $user->setFirstName($faker->firstName);
            $user->setLastName($faker->lastName);
            $user->setEmail($faker->email);
            $user->setPassword('$2y$12$ORnjH4CGJwueY5.yOlikCuK0Ti3E4r/269NrbYFkVhn5l6paRJVPO');
            $user->setPhoneNumber($faker->phoneNumber);

            $indexColor = array_rand($aColors);
            $color = $aColors[$indexColor];
            $user->setColor($color);

            $user->setJob($faker->jobTitle);

            $image = new Media();
            $image->setUrl($imageFaker->imageUrl(640, 480, ['girl']));
            $user->setImage($image);

            $address = new Address();
            $address->setStreet($faker->streetAddress);
            $address->setCity($faker->city);
            $address->setPlace($faker->streetName);
            $address->setZipCode($faker->postcode);
            $address->setInfo($faker->address);
            $address->setCountry('France');

            $user->setAddress($address);

            $indexNetwork = array_rand($aNetwork);
            $user->addSubscription($aNetwork[$indexNetwork]);
            $indexNetwork = array_rand($aNetwork);
            $user->addSubscription($aNetwork[$indexNetwork]);

            for($m = 0 ; $m < 2 ; $m++) {
                $media = new Media();
                $media->setUrl($imageFaker->imageUrl(640, 480, ['group']));
                $manager->persist($media);
                $user->addAchievement($media);
            }

            $user->setDescription($faker->realText());

            $logo = new Media();
            $logo->setUrl($imageFaker->imageUrl(640, 480, ['logo']));
            $user->setLogo($logo);
            $user->setWebsite($faker->domainName);

            for($a = 0 ; $a < 2 ; $a++) {
                $activity = new Activity();
                $activity->setName($faker->catchPhrase());
                $activity->setDescription($faker->realText());
                $manager->persist($activity);
                $user->addActivity($activity);
            }

            for($m = 0 ; $m < 1 ; $m++) {
                $media = new Media();
                $media->setUrl($imageFaker->imageUrl(640, 480, ['work','job']));
                $manager->persist($media);
                $user->addActivityImage($media);
            }

            for($a = 0 ; $a < 2 ; $a++) {
                $argument = new Argument();
                $argument->setTitle($faker->catchPhrase());
                $argument->setDescription($faker->realText());
                $manager->persist($argument);
                $user->addArgument($argument);
            }

            for($c = 0 ; $c < 1 ; $c++) {
                $media = new Media();
                $media->setUrl($imageFaker->imageUrl(640, 480, ['logo']));
                $manager->persist($media);
                $user->addCustomer($media);
            }

            $video = new Media();
            $video->setUrl($youtubeFaker->youtubeUri());
            $user->setVideo($video);

            $services = $this->getUserServices($aService);
            for($s = 0 ; $s < 2 ; $s++) {
                $service = $services[$s];
                $user->addService($service);
            }

            $indexWS = array_rand($aWorkingSectors);
            $ws = $aWorkingSectors[$indexWS];
            $user->setWorkingSector($ws);

            $indexRole = array_rand($aRoles);
            $user->setRoles([$aRoles[$indexRole]]);

            $aUser[] = $user;
            $manager->persist($user);
            $creationDate = $faker->dateTimeBetween('-1 month', 'now');
            $user->setCreatedAt($creationDate);

            $nbZone = rand(7, 10);
            for ($z = 0 ; $z < $nbZone ; $z++) {
                $indexZone = array_rand($aZones);
                $zone = $aZones[$indexZone];
                $user->addZone($zone);
            }

            $manager->persist($user);
        }
        $manager->flush();

    }

    private function getUserServices($aService) {
        $services = [];
        $indexService = array_rand($aService);
        $services[] = $aService[$indexService];

        $indexService2 = array_rand($aService);
        if($indexService2 == $indexService) {
            return $this->getUserServices($aService);
        } else {
            $services[] = $aService[$indexService2];
        }
        return $services;
    }

    private function getUserZoneEvent($aEvent, $user)
    {
        $indexEvent = array_rand($aEvent);
        $event = $aEvent[$indexEvent];
        if( $this->isThisEventInUserZone($event, $user) ) {
            return $event;
        } else {
            return $this->getUserZoneEvent($aEvent, $user);
        }
    }

    private function isThisEventInUserZone(Event $event, User $user) : bool
    {
        return $user->getZones()->exists(function ($key, $zone) use ($event) {
            return $zone === $event->getZone();
        });
    }

    public function getDependencies()
    {
        return array(
            ZoneFixtures::class,
            WorkingSectorFixtures::class
        );
    }

}
