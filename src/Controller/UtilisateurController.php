<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\ModifierMotDePasseFormType;
use App\Form\ModifierProfilFormType;
use App\Repository\SortieRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UtilisateurController extends AbstractController
{

    /**
     * Afficher un compte
     * @Route("/profil/", name="default_profil")
     * @IsGranted("ROLE_USER")
     */
    public function afficher(Request $request): Response
    {
        $utilisateur = $this->getUser();
        if($utilisateur == null) {
            throw $this->createNotFoundException("Utilisateur inconnu");
        }

        return $this->render("profil.html.twig");
    }

	/**
	 * @Route("modifierMdp", name="default_modifierMdp")
	 */
	public function modifMdp(EntityManagerInterface $em,
							 Request $request,
							 UserPasswordHasherInterface $userPasswordHasherInterface) {
		$utilisateur = $this->getUser();
		$formUtilisateur = $this->createForm(ModifierMotDePasseFormType::class, $utilisateur);
		$formUtilisateur->handleRequest($request);

		if($formUtilisateur->isSubmitted() && $formUtilisateur->isValid()) {
				$utilisateur->setPassword(
					$userPasswordHasherInterface->hashPassword(
						$utilisateur,
						$formUtilisateur->get('plainPassword')->getData()
					)
				);

			// sauvegarder les données dans la base
			$em->persist($utilisateur);
			$em->flush();
			//ajouter un msg à l'utilisateur
			$this->addFlash('success', 'Le mot de passe a bien été modifié !');
			//redirection
			return $this->redirectToRoute("default_accueil");
		}

		return $this->render("modifiermotdepasse.html.twig", [
			'formUtilisateur' => $formUtilisateur->createView()
		]);

	}
    /**
     * Modifier un compte
     * @Route("/modifier/", name="default_modifier")
     * @IsGranted("ROLE_USER")
     */
    public function modifier(EntityManagerInterface $em,
                             Request $request,
                             UserPasswordHasherInterface $userPasswordHasherInterface
    ): Response
    {
        $utilisateur = $this->getUser();
        if($utilisateur == null) {
            throw $this->createNotFoundException("Utilisateur inconnu");
        }

        $formUtilisateur = $this->createForm(ModifierProfilFormType::class, $utilisateur);

        $formUtilisateur->handleRequest($request);

        if($formUtilisateur->isSubmitted() && $formUtilisateur->isValid()) {

            // sauvegarder les données dans la base
            $em->persist($utilisateur);
            $em->flush();
            //ajouter un msg à l'utilisateur
            $this->addFlash('success', 'Le profil a bien été modifié !');
            //redirection
            return $this->redirectToRoute("default_accueil");
        }

        return $this->render("modifierprofil.html.twig", [
            'formUtilisateur' => $formUtilisateur->createView()
        ]);
    }


    /**
     * Afficher le profil d'un participant
     * @Route("/participant/{id}", name="default_participant", requirements={"id"="\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function participant(UtilisateurRepository $utilisateurRepository, SortieRepository $sortieRepository, Request $request): Response
    {
        $sortieId = $request->get('sortieId');
        if($sortieId != null){
            $sortie = $sortieRepository->find($sortieId);
        } else{
            $sortie = "nepasafficher";
        }


        $participantId = $request->get('participantId');
        $participant = $utilisateurRepository->find($participantId);


        if($participant == null) {
            throw $this->createNotFoundException("Utilisateur inconnu");
        }


        return $this->render("participant.html.twig", [
            'participant' => $participant,
            'sortie' => $sortie
        ]);
    }

}