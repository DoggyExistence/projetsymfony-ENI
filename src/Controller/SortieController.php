<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\TriUtils;
use App\Form\ModifierProfilFormType;
use App\Form\SortieType;
use App\Form\TriUtilsType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use App\Repository\UtilisateurRepository;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * Affichage détail d'une sortie
     * @Route ("/sortie/detail/{id}", name="sortie_detail", requirements={"id"="\d+"})
     * @param int $id
     * @param SortieRepository $sortieRepository
     * @param UtilisateurRepository $utilisateurRepository
     * @param LieuRepository $lieuRepository
     * @return Response
     */
    public function detailSortie(int $id, SortieRepository $sortieRepository,
                                 UtilisateurRepository $utilisateurRepository,
                                 LieuRepository $lieuRepository): Response {

        $sortie = $sortieRepository->find($id);
        $idOrganisateur = $sortie->getOrganisateur()->getId();
        $organisateur = $utilisateurRepository->find($idOrganisateur);
        $idLieu = $sortie->getLieu()->getId();
        $lieu = $lieuRepository->find($idLieu);
        $participants = $sortie->getParticipants();
        $utilisateur= $this->getUser();

        if(!$sortie || !$lieu || !$organisateur){
            throw $this->createNotFoundException("Sortie inconnue");
        }
        //dump($sortie->getParticipants()->count());
        return $this->render("sortie/detail.html.twig",[
            'sortie' => $sortie,
            'organisateur' => $organisateur,
            'lieu' => $lieu,
            'participants' => $participants,
            'utilisateur' => $utilisateur
        ]);
    }

    /**
     * Créer une sortie
     * @Route("/creer/", name="default_creer")
     */
    public function creer(EntityManagerInterface $em,
                             Request $request, EtatRepository $etatRepository
    ): Response
    {
        $sortie = new Sortie();
        $sortie->setOrganisateur($this->getUser());

        $formSortie = $this->createForm(SortieType::class, $sortie);
        $formSortie->handleRequest($request);
        $token = $request->get('_token');
        if($formSortie->isSubmitted() && $formSortie->isValid() && $this->isCsrfTokenValid('_token', $token)) {

            $etat = $etatRepository->findOneBy(['libelle'=>'Créée']);
            $sortie->setEtat($etat);
			$sortie->setSite($sortie->getOrganisateur()->getSite());
            // Ajout de l'organisateur à la liste des participants
            $sortie->addParticipant($this->getUser());
            // sauvegarder les données dans la base
            $em->persist($sortie);
            $em->flush();
            //ajouter un msg à l'utilisateur
            $this->addFlash('success', 'La sortie a bien été ajoutée !');
            //redirection
            return $this->redirectToRoute("sortie_detail",['id' => $sortie->getId()]);
        }

        return $this->render("creationSortie.html.twig", [
            'formSortie' => $formSortie->createView(),
            'sortie' => $sortie
        ]);
    }



    /**
     * Se désister
     * @Route("/desister/{id}", name="sortie_desister", requirements={"id"="\d+"})
     */
    public function desister(int $id, SortieRepository $sortieRepository,
                             UtilisateurRepository $utilisateurRepository,
                             Request $request,
                             EntityManagerInterface $em
    ): Response
    {

        $sortie = $sortieRepository->find($id);
        $time = date('H:i:s \O\n d/m/Y');
        if ($sortie && $sortie->getDateLimiteInscription() > $time) {
        $utilisateurId = $request->get('utilisateurId');
        $utilisateur = $utilisateurRepository->find($utilisateurId);
        $sortie->removeParticipant($utilisateur);

        $em->persist($sortie);
        $em->flush();
        $this->addFlash("success", "Vous vous êtes correctement désisté");

        return $this->redirectToRoute("default_accueil");
        }
        $this->addFlash("danger", "Vous ne vous êtes pas désinscrit");
        return $this->redirectToRoute("default_accueil");


    }


    /**
     * S'inscrire
     * @Route("/inscrire/{id}", name="sortie_inscrire", requirements={"id"="\d+"})
     */
    public function inscrire(int $id, SortieRepository $sortieRepository,
                             UtilisateurRepository $utilisateurRepository,
                             Request $request,
                             EntityManagerInterface $em
    ): Response {

        $sortie = $sortieRepository->find($id);

        if($sortie && $sortie->getParticipants()->count() < $sortie->getNbInsciptionsMax()){
            $utilisateurId = $request->get('utilisateurId');
            $utilisateur = $utilisateurRepository->find($utilisateurId);
            $sortie->addParticipant($utilisateur);

            $em->persist($sortie);
            $em->flush();
            $this->addFlash("success", "Vous vous êtes correctement inscrit");

            return $this->redirectToRoute("sortie_detail", ['id' => $id]);
        } elseif($sortie->getParticipants()->count() >= $sortie->getNbInsciptionsMax()){
            $this->addFlash("danger", "Il n'y a plus de place pour s'inscrire à cette sortie");
            return $this->redirectToRoute("default_accueil");
        } else{
            $this->addFlash("danger", "Vous ne vous êtes pas inscrit");
            return $this->redirectToRoute("default_accueil");
        }

    }

    /**
     * Modifier une sortie
     * @Route("/modifiersortie/{id}", name="modifier_sortie", requirements={"id"="\d+"})
     */
    public function modifierSortie(int $id, UtilisateurRepository $utilisateurRepository,
                                   EtatRepository  $etatRepository,
                                   SortieRepository  $sortieRepository, EntityManagerInterface $em,
                             Request $request
    ): Response
    {
        $sortie = $sortieRepository ->find($id);

        $utilisateurId = $request ->get('utilisateurId');
//        $utilisateur = $utilisateurRepository ->find($utilisateurId);

        $formSortie = $this->createForm(SortieType::class, $sortie);
        $formSortie->handleRequest($request);
        $token = $request->get('_token');

        if($formSortie->isSubmitted() && $formSortie->isValid() && $this->isCsrfTokenValid('_token', $token)) {
            $etat = $etatRepository->findOneBy(['libelle'=>'Créée']);
            $sortie->setEtat($etat);
            $sortie->setSite($sortie->getOrganisateur()->getSite());
            // sauvegarder les données dans la base
            $em->persist($sortie);
            $em->flush();
            //ajouter un msg à l'utilisateur
            $this->addFlash('success', 'La sortie a bien été modifiée !');
            //redirection
            return $this->redirectToRoute("sortie_detail",['id' => $sortie->getId()]);
        }

//        $utilisateur = $this->getUser();
//        if($utilisateur == null) {
//            throw $this->createNotFoundException("Utilisateur inconnu");
//        }



        return $this->render("modifiersortie.html.twig", [
            'formSortie' => $formSortie->createView(),
            'sortie' => $sortie
        ]);
    }

    /**
     * annuler une sortie
     * @Route("/annuler/{idSortie}", name="sortie_annuler", requirements={"id"="\d+"})
     * @param int $idSortie
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param EtatRepository $etatRepository
     * @return Response
     */
    public function annulerSortie(int $idSortie, EntityManagerInterface $em, Request $request,
                                  SortieRepository $sortieRepository,
                                  EtatRepository $etatRepository) : Response {

        $sortie = $sortieRepository->find($idSortie);

        $formAnnulation = $this->createFormBuilder($sortie)
            ->add('motif', TextareaType::class, [
                'label' => 'Motif :',
                'attr' => ['placeholder' => "Motif..."]
            ])
            ->getForm();
        $formAnnulation->handleRequest($request);

        if($formAnnulation->isSubmitted() && $formAnnulation->isValid()) {

            $etat = $etatRepository->findOneBy(['libelle'=>'Annulée']);
            $sortie->setEtat($etat);

            // sauvegarder les données dans la base
            $em->persist($sortie);
            $em->flush();
            //ajouter un msg à l'utilisateur
            $this->addFlash('success', 'La sortie a bien été annulée !');
            //redirection
            return $this->redirectToRoute("sortie_detail",['id' => $sortie->getId()]);
        }

        return $this->render("annulationSortie.html.twig", [
            'formAnnulation' => $formAnnulation->createView(),
            'sortie' => $sortie
        ]);
    }
}
