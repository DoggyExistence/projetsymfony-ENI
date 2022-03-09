<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SortieFixture extends Fixture
{


    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        for ($i=1; $i<=10; $i++) {
            $sortie = new Sortie();
            $sortie->setNom("Sortie_$i");
            $sortie->setDateHeureDebut($faker->dateTimeBetween("now", "+6 months"));
            $sortie->setDuree($faker->numberBetween(30, 2880));
            $sortie->setDateLimiteInscription($sortie->getDateHeureDebut());
            $sortie->setNbInsciptionsMax($faker->numberBetween(2, 20));
            $sortie->setInfosSortie($faker->realText());
            $sortie->setLieu($this->getReference(Lieu::class . "_" . mt_rand(1, 10)));
            $sortie->setOrganisateur($this->getReference(Utilisateur::class."_".mt_rand(1, 4)));
			$sortie->setSite($sortie->getOrganisateur()->getSite());

            for ($j=1; $j<$sortie->getNbInsciptionsMax(); $j++){
                $sortie->addParticipant($this->getReference(Utilisateur::class."_".mt_rand(1, 4)));
            }
            if(new \DateTime() < $sortie->getDateLimiteInscription()){
                if($sortie->getNbInsciptionsMax()===$sortie->getParticipants()->count()){
                    $sortie->setEtat($this->getReference(Etat::class.'3'));
                }else{
                    $sortie->setEtat($this->getReference(Etat::class.mt_rand(1,2)));
                }

            }elseif (new \DateTime() < $sortie->getDateHeureDebut()){
                $sortie->setEtat($this->getReference(Etat::class.'3'));
            }elseif (new \DateTime('now + 1 month') >= $sortie->getDateHeureDebut()){
                $sortie->setEtat($this->getReference(Etat::class.'5'));
            }else{
                $sortie->setEtat($this->getReference(Etat::class.'4'));
            }

            $manager->persist($sortie);
        }
        $manager->flush();
    }


}
