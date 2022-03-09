<?php

namespace App\Controller;

use App\Entity\TriUtils;
use App\Form\TriUtilsType;
use App\Repository\ArchivageRepository;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
	/**
	 * Affiche la page d'accueil
	 * @Route("/", name="default_accueil")
	 */
	public function home(SortieRepository $sortieRepository, UtilisateurRepository $utilisateurRepository,
						 ArchivageRepository $archivageRepository, EtatRepository $etatRepository,
						 Request $request, EntityManagerInterface $em) : Response {

		// Archivage
		$archivageRepository->archivage($em, $sortieRepository, $etatRepository);

		$triUtils = new TriUtils();
		$triForm = $this->createForm(TriUtilsType::class, $triUtils);
		$user = $this->getUser();

		$triForm->handleRequest($request);
		if ($triForm->isSubmitted() && $triForm->isValid()) {
			dump($triUtils);
			$sorties = $sortieRepository->findByTri($triUtils, $user);
			return $this->render("accueil.html.twig",[
				'sorties' => $sorties,
				'triForm' => $triForm->createView(),
				'utilisateur' => $user
			]);
		}

        $sorties = $sortieRepository->afficherSorties();
        $etatPasse = $etatRepository->findOneBy(["libelle"=>"Passée"]);
        $etatCloturee = $etatRepository->findOneBy(["libelle"=>"Cloturée"]);
        $etatOuverte = $etatRepository->findOneBy(["libelle" => "Ouverte"]);

        foreach ($sorties as $sortie){
            $libelleEtat = $sortie->getEtat()->getLibelle();
            if($libelleEtat == "Ouverte" || $libelleEtat == "Cloturée"){
                if($sortie->getDateHeureDebut() <= new \DateTime()){
                    $sortie->setEtat($etatPasse);

                } elseif (($sortie->getParticipants()->count() >= $sortie->getNbInsciptionsMax() ||
                    $sortie->getDateLimiteInscription() < new \DateTime()) && $sortie->getEtat()->getLibelle() != "Cloturée"){
                    $sortie->setEtat($etatCloturee);


                } elseif ($sortie->getParticipants()->count() < $sortie->getNbInsciptionsMax() &&
                        $sortie->getDateLimiteInscription() > new \DateTime() && $sortie->getEtat()->getLibelle() != "Cloturée") {
                    $sortie->setEtat($etatOuverte);

                }
                $em->persist($sortie);
                $em->flush();
            }
        }

		return $this->render("accueil.html.twig",[
            'sorties' => $sorties,
			'triForm' => $triForm->createView(),
			'utilisateur' => $user
        ]);
	}

}