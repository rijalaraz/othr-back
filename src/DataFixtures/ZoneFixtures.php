<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DomCrawler\Crawler;
use App\Entity\Zone;

class ZoneFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $crawler = new Crawler();
        $crawler->addXmlContent(file_get_contents(__DIR__.'/departement.xml'));

        $crawler->filter('table')->each(function (Crawler $table) use($manager) {

            $zone = new Zone();

            $table->filter('column')->each(function(Crawler $col) use($zone) {

                switch($col->attr('name')) {
                    case 'departement_code':
                        $zone->setCode($col->text());
                        break;

                    case 'departement_nom':
                        $zone->setName($col->text());
                        break;
                }

            });

            $zone->setCountry('FR');

            $manager->persist($zone);

        });
        $manager->flush();
    }
}