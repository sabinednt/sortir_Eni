<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/sortie", name="sortie")
     */
    public function index(): Response
    {
        return $this->render('sortie/index.html.twig', [
            'controller_name' => 'SortieController',
        ]);
    }

    /**
     * @Route("/sortie/create", name="sortie_create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager, EtatRepository $etatRepository): Response
    {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $sortieForm->handleRequest($request);
        // todo gerer les etat
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            switch ($sortieForm->getClickedButton()->getName()) {
                case 'save':
                    $participant = $this->getUser();
                    $etat = $etatRepository->findOneBy(['libelle' => 'Créée']);
                    $sortie->setOrganisateur($participant);
                    $sortie->setEtat($etat);
                    $entityManager->persist($sortie);
                    $entityManager->flush();

                    $this->addFlash(
                        'success',
                        'sortie créé ! Merci beaucoup.'
                    );
                    $nextAction = 'sortie_list';
                    break;
                case 'saveAndPublish':
                    $participant = $this->getUser();
                    $etat = $etatRepository->findOneBy(['libelle' => 'Ouverte']);
                    $sortie->setOrganisateur($participant);
                    $sortie->setEtat($etat);
                    $entityManager->persist($sortie);
                    $entityManager->flush();

                    $this->addFlash(
                        'success',
                        'sortie créé et Publiée ! Merci beaucoup.'
                    );
                    $nextAction = 'sortie_list';
                    break;
            }

            return $this->redirectToRoute($nextAction);
        }

        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $sortieForm->createView(),
        ]);
    }

    /**
     * @Route("/sorties", name="sortie_list")
     */
    public function list(SortieRepository $sortieRepository): Response
    {
        // $series = $sortieRepository->findAll();
        // $series = $sortieRepository->findBy([], ['nom' => 'DESC', 'dateLimiteInscription' => 'DESC'], 30);
        $sorties = $sortieRepository->findSorties();
        // $sorties = $sortieRepository->findAll();

        return $this->render('sortie/list.html.twig', ['sorties' => $sorties]);
    }

    /**
     * @Route("/sortie/details/{id}", name="sortie_details")
     */
    public function details(SortieRepository $sortieRepository, int $id): Response
    {
        $sortie = $sortieRepository->find($id);

        if (!$sortie) {
            throw $this->createNotFoundException('Sortie non trouvée !');
        }

        return $this->render('sortie/details.html.twig', ['sortie' => $sortie]);
    }
}