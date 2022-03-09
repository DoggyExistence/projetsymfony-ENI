<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LieuFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {


        $faker = \Faker\Factory::create('fr_FR');

        for ($i=1; $i<=10; $i++){
            $lieu = new Lieu();
            $lieu->setNom($faker->address());
            $lieu->setRue($faker->streetName());
            $lieu->setLatitude($faker->latitude());
            $lieu->setLongitude($faker->longitude());
            $lieu->setCodePostal($faker->postcode());
            $lieu->setVille($faker->city());
            $manager->persist($lieu);
            $this->addReference(Lieu::class."_$i", $lieu);
        }

        $manager->flush();
    }
}
