<?php

namespace App\Controller\Admin;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/dashboard", name="admin_dashboard")
     */
    public function index(UtilisateurRepository $utilisateurRepository): Response
    {

        $utilisateurs = $utilisateurRepository->afficherUtilisateurs();

        return $this->render('admin/index.html.twig', [
            'utilisateurs' => $utilisateurs
        ]);
    }

    /**
     * @Route ("/creerutilisateur", name="admin_createuser")
     * @return Response
     */
    public function creerUtilisateur(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher) : Response {
        $utilisateur = new Utilisateur();
        $formUtilisateur = $this->createForm(UtilisateurType::class, $utilisateur);
        $formUtilisateur->handleRequest($request);
        $token = $request->get('_token');

        if($formUtilisateur->isSubmitted() && $formUtilisateur->isValid() && $this->isCsrfTokenValid('_token', $token)){
            $utilisateur->setPassword(
                $userPasswordHasher->hashPassword(
                    $utilisateur,
                    $formUtilisateur->get('password')->getData()
                )
            );
            $utilisateur->setRoles($formUtilisateur->get('roles')->getData());
            $em->persist($utilisateur);
            $em->flush();
            $this->addFlash('success', 'L\'utilisateur a bien été créé');
            return $this->redirectToRoute("admin_dashboard");
        }

        return $this->render('admin/creerutilisateur.html.twig', [
            'formUtilisateur' => $formUtilisateur->createView()
        ]);
    }
}
