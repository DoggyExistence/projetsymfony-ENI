<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EtatController extends AbstractController
{
//    /**
//     * @Route("/etat", name="etat")
//     */
//    public function index(): Response
//    {
//        return $this->render('etat/index.html.twig', [
//            'controller_name' => 'EtatController',
//        ]);
//    }

    /**
     * @Route("/etat/publie", name="etat_publie")
     * @return Response
     */
    public function publier(Request $request, SortieRepository $sortieRepository, EntityManagerInterface $entityManager, EtatRepository $etatRepository) : Response
    {
        $sortieId = $request->get('id');
        //$heidi = $this->get('id');
        $sortie = $sortieRepository->find($sortieId);
        //$etat = new Etat();
        $etat = $etatRepository->findOneBy(['libelle' => 'Ouverte']);
        $sortie->setEtat($etat);
        $entityManager->persist($sortie);
        $entityManager->flush();
        $this->addFlash('success', 'La sortie a bien été publiée !');
        return $this->redirectToRoute("sortie_detail",['id' => $sortieId]);
    }
}
