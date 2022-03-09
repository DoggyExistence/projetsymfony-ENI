<?php

namespace App\Repository;

use App\Entity\Sortie;
use App\Entity\TriUtils;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

	/**
	 * Récupérer les sorties selon les critères de tri saisis
	 */
	public function findByTri(TriUtils $triUtils, Utilisateur $user) {
		// si aucun critère n'est renseigné, ou seulement "inscrit" ET "non inscrit" alors on effectue pas de tri
		if ((!$triUtils->getSite() && !$triUtils->getMotRecherche() && !$triUtils->getDateDebut() && !$triUtils->getDateFin()
			&& !$triUtils->getIsOrga() && !$triUtils->getIsInscrit() && !$triUtils->getIsNonInscrit() && !$triUtils->getIsPassee()) OR
			(!$triUtils->getSite() && !$triUtils->getMotRecherche() && !$triUtils->getDateDebut() && !$triUtils->getDateFin()
			&& !$triUtils->getIsOrga() && $triUtils->getIsInscrit() && $triUtils->getIsNonInscrit() && !$triUtils->getIsPassee())) {
			return $this->findAll();
		}

		// solution crade pour récupérer les sorties auxquelles on est pas inscrit quand cela est le seul critère de recherche
		if (!$triUtils->getSite() && !$triUtils->getMotRecherche() && !$triUtils->getDateDebut() && !$triUtils->getDateFin()
			&& !$triUtils->getIsOrga() && !$triUtils->getIsInscrit() && $triUtils->getIsNonInscrit() && !$triUtils->getIsPassee()) {

			// Récupérer toutes les sorties
			$allSorties = $this->findAll();
			// Trier et retourner la liste
			return $this->triNonParticipant($allSorties, $user);
		}

		$qb = $this->createQueryBuilder("s")
			->join("s.etat", "e") //Permet d'optimiser la requête
			->addSelect("e")
			->join("s.organisateur", "o")
			->addSelect("o")
			->leftJoin("s.participants", "p")
			->addSelect("p");

		if ($triUtils->getSite()) {
			$qb->andWhere("s.site = :site")
			->setParameter('site', $triUtils->getSite()->getId())
			;
		}

		// si plusieurs mots sont saisis ils sont tous ajoutés à la requête
		if ($triUtils->getMotRecherche()) {
			$mots = explode(" ", $triUtils->getMotRecherche());

			$qb->andWhere("s.nom LIKE :mot")
				->setParameter('mot', "%".$mots[0]."%")
			;
			for ($i = 1; $i < sizeof($mots); $i++) {
				$qb->orWhere("s.nom LIKE :mot".$i)
					->setParameter('mot'.$i, "%".$mots[$i]."%")
				;
			}

		}
		if ($triUtils->getDateDebut()) {
			$qb->andWhere("s.dateHeureDebut > :dateDebut")
				->setParameter('dateDebut', $triUtils->getDateDebut())
			;
		}
		if ($triUtils->getDateFin()) {
			$qb->andWhere("s.dateHeureDebut < :dateFin")
				->setParameter('dateFin', $triUtils->getDateFin())
			;
		}
		if ($triUtils->getIsOrga()) {
			$qb->andWhere("s.organisateur = :orgaId")
				->setParameter('orgaId', $user->getId())
			;
		}

		if ($triUtils->getIsPassee()) {
			$now = new \DateTimeImmutable();// pour test : ("2022-1-1T00:00:00P");
			$qb->andWhere("s.dateHeureDebut < :now")
				->setParameter('now', $now)
			;
		}

		// XOR car si aucun ou les deux sont cochés cela revient à ne pas appliquer de filtre
		if ($triUtils->getIsInscrit() XOR $triUtils->getIsNonInscrit()) {
			if ($triUtils->getIsInscrit()) {
				$qb->leftJoin("s.participants", "u")
				->addSelect("u")
					->andWhere("u.id = :utilId")
					->setParameter('utilId', $user->getId())
				;
			} else {
				$allSorties = $qb->getQuery()->getResult();
				return $this->triNonParticipant($allSorties, $user);
			}
		}


		$dql = $qb->getDql();
		dump($dql);
		return $qb->getQuery()->getResult();
	}

	private function triNonParticipant($allSorties, $user): array
	{

		// Récupérer les sorties auxquelles l'utilisateur participe
		$qb = $this->createQueryBuilder("s")
			->join("s.participants", "u")
			->addSelect("u")
			->andWhere("u.id = :utilId")
			->setParameter('utilId', $user->getId())
		;
		$sortiesParticipant = $qb->getQuery()->getResult();
		// s'il ne participe à aucune sortie on renvoie la liste complète des sorties
		if (!$sortiesParticipant) {
			return $allSorties;
		}

		// retirer les sorties auxquelles l'utilisateur participe de l'ensembles des sorties
		$index = 0;
		$sorties = [];
		foreach ($allSorties as $sortie) {
			$participant = false;
			foreach ($sortiesParticipant as $sortieParticipant) {
				if ($sortie == $sortieParticipant) {
					$participant = true;
				}
			}
			if (!$participant) {
				$sorties[$index] = $sortie;
				$index++;
			}

		}
		return $sorties;
	}


    public function afficherSorties() {

        $qb = $this->createQueryBuilder("s")
            ->join("s.etat", "e") //Permet d'optimiser la requête
            ->addSelect("e")
            ->join("s.organisateur", "o")
            ->addSelect("o")
            ->leftJoin("s.participants", "p")
            ->addSelect("p");

        $query = $qb->getQuery();
        return $query->getResult();

    }

	public function archiverSorties(EtatRepository $etatRepository) {
		// aujourdhui + 1 mois
		$dateAArchiver = new \DateTime();
		$dateAArchiver->sub(new \DateInterval("P1M")); // moins 1 mois

		// récupérer état archivé
		$etatArchivee = $etatRepository->findOneBy(['libelle'=>'Archivée']);
		$etatAnnulee = $etatRepository->findOneBy(['libelle'=>'Annulée']);
		// récupérer état annulé
		$qb = $this->createQueryBuilder("s")
			->update(Sortie::class, "s")
			->set("s.etat", ":archivee")
			->andWhere("s.dateHeureDebut < :aArchiver")
			->andWhere("s.etat != :annulee")
			->setParameter("archivee", $etatArchivee->getId())
			->setParameter("annulee", $etatAnnulee->getId())
			->setParameter("aArchiver", $dateAArchiver)
			;
		$qb->getQuery()->execute();
	}

    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sortie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
