<?php

namespace App\Repository;

use App\Entity\Archivage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Archivage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Archivage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Archivage[]    findAll()
 * @method Archivage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArchivageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Archivage::class);
    }

	public function archivage(EntityManagerInterface $em, SortieRepository $sortieRepository, EtatRepository $etatRepository) {

		//récupérer la dernière date d'archivage
		$qb = $this->createQueryBuilder("a")
			->addOrderBy("a.dateDernierArchivage", "DESC")
			->setMaxResults(1)
			->setFirstResult(0);
		$dernierArchivage = $qb->getQuery()->getResult();
		$maintenant = new \DateTime();

		// si supérieure à 24h ou null faire un archivage
		if (!$dernierArchivage || ($dernierArchivage[0]->getDateDernierArchivage()->add(new \DateInterval("P1D")) < $maintenant)) {

			// requete update sortie set etat='archivée' where date_sortie > (now + 1 mois) and etat !='annulé'
			$sortieRepository->archiverSorties($etatRepository);

			// stocher la nouvelle date d'archivage
			// si pas de date stockée en bdd on ajoute celle du jour
			if (!$dernierArchivage) {
				$nouvelArchivage = new Archivage();;
			} else {
				// sinon on remplace la date existante
				$nouvelArchivage = $dernierArchivage[0];
			}
			$nouvelArchivage->setDateDernierArchivage($maintenant);
			$em->persist($nouvelArchivage);
			$em->flush();
		}

		// sinon rien

	}

    // /**
    //  * @return Archivage[] Returns an array of Archivage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Archivage
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
