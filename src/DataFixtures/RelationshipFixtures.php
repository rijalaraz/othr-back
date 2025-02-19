<?php

namespace App\DataFixtures;

use App\Entity\Event;
use App\Entity\Network;
use App\Entity\NetworkMember;
use App\Entity\Notification;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;
use Xvladqt\Faker\LoremFlickrProvider;
use App\Entity\NotificationType;
use App\Entity\Relationship;
use App\Entity\Post;
use Doctrine\Persistence\ObjectManager as PersistenceObjectManager;

class RelationshipFixtures extends Fixture implements DependentFixtureInterface
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


        for($a = 0 ; $a < 7 ; $a++) {
            $post = new Post();
            $indexN = array_rand($networks);
            $indexU = array_rand($users);
            $post->setTitle("Titre ".$a);
            $post->setNetwork($networks[$indexN]);
            $post->setUser($users[$indexU]);
            $aTYPE = POST::TYPES;
            $indexTypePost = array_rand($aTYPE);
            $post->setType($aTYPE[$indexTypePost]);
            $post->setContent("Text ".$a);
            $manager->persist($post);
        }

        $manager->flush();

        foreach($users as $indexUser => $user) {
            $aNetwork = $this->getMemberNetwork($networks);

            if($indexUser == 0) {
                $rel = new Relationship();
                $rel->setSourceUser($user);
                $rel->setTargetUser($users[1]);
                $rel->setTeam(true);
                $manager->persist($rel);
                $creationDate = $faker->dateTimeBetween('-1 month', 'now');
                $rel->setCreatedAt($creationDate);
                $manager->persist($rel);

                $rel = new Relationship();
                $rel->setSourceUser($user);
                $rel->setTargetUser($users[4]);
                $manager->persist($rel);
                $creationDate = $faker->dateTimeBetween('-1 month', 'now');
                $rel->setCreatedAt($creationDate);
                $manager->persist($rel);

                $rel = new Relationship();
                $rel->setSourceUser($user);
                $rel->setTargetUser($users[6]);
                $manager->persist($rel);
                $creationDate = $faker->dateTimeBetween('-1 month', 'now');
                $rel->setCreatedAt($creationDate);
                $manager->persist($rel);
            }

            if($indexUser == 1) {
                $rel = new Relationship();
                $rel->setSourceUser($user);
                $rel->setTargetUser($users[2]);
                $rel->setTeam(true);
                $manager->persist($rel);
                $creationDate = $faker->dateTimeBetween('-1 month', 'now');
                $rel->setCreatedAt($creationDate);
                $manager->persist($rel);

                $rel = new Relationship();
                $rel->setSourceUser($user);
                $rel->setTargetUser($users[5]);
                $rel->setTeam(true);
                $manager->persist($rel);
                $creationDate = $faker->dateTimeBetween('-1 month', 'now');
                $rel->setCreatedAt($creationDate);
                $manager->persist($rel);

                $rel = new Relationship();
                $rel->setSourceUser($user);
                $rel->setTargetUser($users[6]);
                $manager->persist($rel);
                $creationDate = $faker->dateTimeBetween('-1 month', 'now');
                $rel->setCreatedAt($creationDate);
                $manager->persist($rel);
            }

            if($indexUser == 2) {
                $rel = new Relationship();
                $rel->setSourceUser($user);
                $rel->setTargetUser($users[3]);
                $manager->persist($rel);
                $creationDate = $faker->dateTimeBetween('-1 month', 'now');
                $rel->setCreatedAt($creationDate);
                $manager->persist($rel);

                $rel = new Relationship();
                $rel->setSourceUser($user);
                $rel->setTargetUser($users[4]);
                $rel->setTeam(true);
                $manager->persist($rel);
                $creationDate = $faker->dateTimeBetween('-1 month', 'now');
                $rel->setCreatedAt($creationDate);
                $manager->persist($rel);

                $rel = new Relationship();
                $rel->setSourceUser($user);
                $rel->setTargetUser($users[7]);
                $rel->setTeam(true);
                $manager->persist($rel);
                $creationDate = $faker->dateTimeBetween('-1 month', 'now');
                $rel->setCreatedAt($creationDate);
                $manager->persist($rel);
            }

            if($indexUser == 3) {
                $rel = new Relationship();
                $rel->setSourceUser($user);
                $rel->setTargetUser($users[1]);
                $rel->setTeam(true);
                $manager->persist($rel);
                $creationDate = $faker->dateTimeBetween('-1 month', 'now');
                $rel->setCreatedAt($creationDate);
                $manager->persist($rel);

                $rel = new Relationship();
                $rel->setSourceUser($user);
                $rel->setTargetUser($users[5]);
                $manager->persist($rel);
                $creationDate = $faker->dateTimeBetween('-1 month', 'now');
                $rel->setCreatedAt($creationDate);
                $manager->persist($rel);

                $rel = new Relationship();
                $rel->setSourceUser($user);
                $rel->setTargetUser($users[8]);
                $rel->setTeam(true);
                $manager->persist($rel);
                $creationDate = $faker->dateTimeBetween('-1 month', 'now');
                $rel->setCreatedAt($creationDate);
                $manager->persist($rel);
            }

            if($indexUser == 4) {
                $rel = new Relationship();
                $rel->setSourceUser($user);
                $rel->setTargetUser($users[1]);
                $manager->persist($rel);
                $creationDate = $faker->dateTimeBetween('-1 month', 'now');
                $rel->setCreatedAt($creationDate);
                $manager->persist($rel);

                $rel = new Relationship();
                $rel->setSourceUser($user);
                $rel->setTargetUser($users[3]);
                $rel->setTeam(true);
                $manager->persist($rel);
                $creationDate = $faker->dateTimeBetween('-1 month', 'now');
                $rel->setCreatedAt($creationDate);
                $manager->persist($rel);

                $rel = new Relationship();
                $rel->setSourceUser($user);
                $rel->setTargetUser($users[8]);
                $manager->persist($rel);
                $creationDate = $faker->dateTimeBetween('-1 month', 'now');
                $rel->setCreatedAt($creationDate);
                $manager->persist($rel);
            }

            if($indexUser == 5) {
                $rel = new Relationship();
                $rel->setSourceUser($user);
                $rel->setTargetUser($users[0]);
                $manager->persist($rel);
                $creationDate = $faker->dateTimeBetween('-1 month', 'now');
                $rel->setCreatedAt($creationDate);
                $manager->persist($rel);

                $rel = new Relationship();
                $rel->setSourceUser($user);
                $rel->setTargetUser($users[6]);
                $rel->setTeam(true);
                $manager->persist($rel);
                $creationDate = $faker->dateTimeBetween('-1 month', 'now');
                $rel->setCreatedAt($creationDate);
                $manager->persist($rel);

                $rel = new Relationship();
                $rel->setSourceUser($user);
                $rel->setTargetUser($users[7]);
                $rel->setTeam(true);
                $manager->persist($rel);
                $creationDate = $faker->dateTimeBetween('-1 month', 'now');
                $rel->setCreatedAt($creationDate);
                $manager->persist($rel);
            }

            if($indexUser == 6) {
                $rel = new Relationship();
                $rel->setSourceUser($user);
                $rel->setTargetUser($users[2]);
                $manager->persist($rel);
                $creationDate = $faker->dateTimeBetween('-1 month', 'now');
                $rel->setCreatedAt($creationDate);
                $manager->persist($rel);

                $rel = new Relationship();
                $rel->setSourceUser($user);
                $rel->setTargetUser($users[3]);
                $manager->persist($rel);
                $creationDate = $faker->dateTimeBetween('-1 month', 'now');
                $rel->setCreatedAt($creationDate);
                $manager->persist($rel);

                $rel = new Relationship();
                $rel->setSourceUser($user);
                $rel->setTargetUser($users[7]);
                $rel->setTeam(true);
                $manager->persist($rel);
                $creationDate = $faker->dateTimeBetween('-1 month', 'now');
                $rel->setCreatedAt($creationDate);
                $manager->persist($rel);
            }

            if($indexUser == 7) {
                $rel = new Relationship();
                $rel->setSourceUser($user);
                $rel->setTargetUser($users[0]);
                $rel->setTeam(true);
                $manager->persist($rel);
                $creationDate = $faker->dateTimeBetween('-1 month', 'now');
                $rel->setCreatedAt($creationDate);
                $manager->persist($rel);

                $rel = new Relationship();
                $rel->setSourceUser($user);
                $rel->setTargetUser($users[1]);
                $rel->setTeam(true);
                $manager->persist($rel);
                $creationDate = $faker->dateTimeBetween('-1 month', 'now');
                $rel->setCreatedAt($creationDate);
                $manager->persist($rel);

                $rel = new Relationship();
                $rel->setSourceUser($user);
                $rel->setTargetUser($users[4]);
                $rel->setTeam(true);
                $manager->persist($rel);
                $creationDate = $faker->dateTimeBetween('-1 month', 'now');
                $rel->setCreatedAt($creationDate);
                $manager->persist($rel);
            }

            if($indexUser == 8) {
                $rel = new Relationship();
                $rel->setSourceUser($user);
                $rel->setTargetUser($users[0]);
                $rel->setTeam(true);
                $manager->persist($rel);
                $creationDate = $faker->dateTimeBetween('-1 month', 'now');
                $rel->setCreatedAt($creationDate);
                $manager->persist($rel);

                $rel = new Relationship();
                $rel->setSourceUser($user);
                $rel->setTargetUser($users[1]);
                $manager->persist($rel);
                $creationDate = $faker->dateTimeBetween('-1 month', 'now');
                $rel->setCreatedAt($creationDate);
                $manager->persist($rel);

                $rel = new Relationship();
                $rel->setSourceUser($user);
                $rel->setTargetUser($users[6]);
                $manager->persist($rel);
                $creationDate = $faker->dateTimeBetween('-1 month', 'now');
                $rel->setCreatedAt($creationDate);
                $manager->persist($rel);
            }

            for($m = 0 ; $m < 2 ; $m++) {
                $member = new NetworkMember();
                $member->setUser($user);

                $network = $aNetwork[$m];
                $member->setNetwork($network);

                $indexType = array_rand(NetworkMember::TYPES);
                $type = NetworkMember::TYPES[$indexType];
                $member->setType($type);

                $manager->persist($member);

                $creationDate = $faker->dateTimeBetween('-1 month', 'now');
                $member->setCreatedAt($creationDate);

                $manager->persist($member);

                $user->addNetworkMember($member);
                $manager->persist($user);

                $network->addNetworkMember($member);
                $manager->persist($network);
            }


            $userZones = $user->getZones();
            $aUserZones = $userZones->toArray();
            $indexUserZone = array_rand($aUserZones);
            $userZone = $userZones[$indexUserZone];

            $indexNetwork = array_rand($aNetwork);
            $network = $aNetwork[$indexNetwork];
            $network->setZone($userZone);
            $manager->persist($network);

            $indexNetwork = array_rand($aNetwork);
            $network = $aNetwork[$indexNetwork];
            $network->setZone($userZone);
            $manager->persist($network);

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

    private function getMemberNetwork($networks) {
        $aNetwork = [];
        $indexNetwork = array_rand($networks);
        $aNetwork[] = $networks[$indexNetwork];

        $indexNetwork2 = array_rand($networks);

        if($indexNetwork2 == $indexNetwork) {
            return $this->getMemberNetwork($networks);
        } else {
            $aNetwork[] = $networks[$indexNetwork2];
        }
        return $aNetwork;
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
            UserFixtures::class,
        );
    }

}
