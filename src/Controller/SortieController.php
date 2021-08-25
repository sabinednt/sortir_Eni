<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\SortieType;
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
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $sortie->setDateHeureDebut(new \DateTime());
            // $user = $this->getUser();

            $participant = $this->getDoctrine()
                ->getRepository(Participant::class)
                ->findOneBy(['id' => 1]);

            dump($participant);

            $sortie->setOrganisateur($participant);
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash(
               'success',
               'sortie added! Good job.'
            );

            return $this->redirectToRoute('sortie_list', []);
        }

        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $sortieForm->createView(),
        ]);
    }

    /**
     * @Route("/sorties", name="sortie_list")
     */
    public function list(SortieRepository $serieRepository): Response
    {
        // $series = $serieRepository->findAll();
        // $series = $serieRepository->findBy([], ['popularity' => 'DESC', 'vote' => 'DESC'], 30);
        // $sorties = $serieRepository->findSorties();
        $sorties = $serieRepository->findAll();

        dump($sorties);

        return $this->render('sortie/list.html.twig', ['sorties' => $sorties]);
    }
}