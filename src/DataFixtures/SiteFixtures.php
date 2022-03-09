<?php

namespace App\DataFixtures;

use App\Entity\Site;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SiteFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
		$site = new Site();
		$site->setNom("Rennes");
		$manager->persist($site);
		$this->addReference(Site::class."_1", $site);

		$site = new Site();
		$site->setNom("Nantes");
		$manager->persist($site);
		$this->addReference(Site::class."_2", $site);

		$site = new Site();
		$site->setNom("Niort");
		$manager->persist($site);
		$this->addReference(Site::class."_3", $site);

		$site = new Site();
		$site->setNom("Quimper");
		$manager->persist($site);
		$this->addReference(Site::class."_4", $site);

		$manager->flush();
    }
}
