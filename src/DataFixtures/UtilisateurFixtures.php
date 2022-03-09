<?php

namespace App\DataFixtures;

use App\Entity\Site;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UtilisateurFixtures extends Fixture
{
	/**
	 * @var UserPasswordHasherInterface
	 */
	//Pour récupérer le hasher, pour n epas le stocker en clair dans la bdd
	private $hasher;

	public function __construct(UserPasswordHasherInterface $hasher) {

		$this->hasher = $hasher;
	}

    public function load(ObjectManager $manager): void
    {
		$faker = \Faker\Factory::create('fr_FR');

		$user = new Utilisateur();
		$user->setEmail("admin@test.fr");
		$user->setNom($faker->lastName());
		$user->setprenom($faker->firstName());
		$user->setPseudo("admin");
		$user->setRoles(['ROLE_ADMIN']);
		$password = $this->hasher->hashPassword($user, "admin");
		$user->setPassword($password);
		$user->setSite($this->getReference(Site::class . "_1"));
		$manager->persist($user);

		for ($i = 1; $i <= 5 ; $i++) {
			$user = new Utilisateur();
			$user->setEmail("user".$i."@test.fr");
			$user->setNom($faker->lastName());
			$user->setprenom($faker->firstName());
			$user->setPseudo("user".$i);
			$user->setRoles(['ROLE_USER']);
			$password = $this->hasher->hashPassword($user, "user".$i);
			$user->setPassword($password);
			$user->setSite($this->getReference(Site::class . "_".mt_rand(1,4)));
			$manager->persist($user);
            $this->addReference(Utilisateur::class."_$i", $user);
		}

        $manager->flush();
    }

}
