<?php

namespace App\DataFixtures;

use App\Entity\Network;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;
use App\Entity\Post;
use Doctrine\Persistence\ObjectManager as PersistenceObjectManager;

class PostFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(PersistenceObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');


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

    }


    public function getDependencies()
    {
        return array(
            UserFixtures::class,
        );
    }
}
