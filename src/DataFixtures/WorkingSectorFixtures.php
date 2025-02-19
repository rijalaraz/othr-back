<?php

namespace App\DataFixtures;

use App\Entity\WorkingSector;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DomCrawler\Crawler;

class WorkingSectorFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $crawler = new Crawler();
        $crawler->addXmlContent(file_get_contents(__DIR__.'/working_sector.xml'));

        $crawler->filter('table')->each(function (Crawler $table) use($manager) {

            $ws = new WorkingSector();

            $table->filter('column')->each(function(Crawler $col) use($ws) {

                switch($col->attr('name')) {
                    case 'name':
                        $ws->setName($col->text());
                        break;
                }

            });

            $manager->persist($ws);

        });
        $manager->flush();
    }
}