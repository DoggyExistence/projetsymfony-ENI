<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraints\Email;

class EtatFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();


        $etat = new Etat();
        $etat->setLibelle("Créée");
        $manager->persist($etat);
        $this->addReference(Etat::class."1", $etat);
        $etat = new Etat();
        $etat->setLibelle("Ouverte");
        $manager->persist($etat);
        $this->addReference(Etat::class."2", $etat);
        $etat = new Etat();
        $etat->setLibelle("Cloturée");
        $manager->persist($etat);
        $this->addReference(Etat::class."3", $etat);
        $etat = new Etat();
        $etat->setLibelle("Passée");
        $manager->persist($etat);
        $this->addReference(Etat::class."4", $etat);
        $etat = new Etat();
        $etat->setLibelle("Archivée");
        $manager->persist($etat);
        $this->addReference(Etat::class."5", $etat);
        $etat = new Etat();
        $etat->setLibelle("Annulée");
        $manager->persist($etat);
        $this->addReference(Etat::class."6", $etat);
        $etat = new Etat();
        $etat->setLibelle("Activité en cours");
        $manager->persist($etat);
        $this->addReference(Etat::class."7", $etat);

        $manager->flush();
    }
}
