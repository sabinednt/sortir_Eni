<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\AnnulSortieType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sortie", name="sortie")
 */

class SortieController extends AbstractController
{
    /**
     * @Route("/annule/{id}", name="annule")
     * @param int $id
     * @param SortieRepository $sortieRepository
     * @param Request $request
     * @param $entityManager
     * @return Response
     *
     */
    public function annule(int              $id,
                           SortieRepository $sortieRepository,
                           Request          $request,
                           EntityManagerInterface $entityManager
    ): Response
    {
        $id=1;
        $sortie = $sortieRepository ->findOneBySomeField($id);


        $annulForm=$this->createForm(AnnulSortieType::class, $sortie);

        //traitement du formulaire
        $annulForm->handleRequest($request);

        if($annulForm ->isSubmitted() ){

            //message flash pour annoncer la bonne exécution de l'annulation
            $this->addFlash('success','la sortie a été annulée');

            //envoi des données à la BD pour modifier le champs infosSortie
            $entityManager -> persist($sortie);
            $entityManager -> flush();

            //redirection vers la page d'accueil
            return $this->redirectToRoute('main_home');
        }

        return $this->render('sortie/annule.html.twig', [
            "sortie" => $sortie,
            "annulForm" => $annulForm->createView()
        ]);
    }
}
