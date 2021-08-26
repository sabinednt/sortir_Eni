<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Participant;
use App\Form\ProfilType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;

Class ProfilController extends AbstractController{
    /**
     * @Route("/profil", name="profil")
     */
    public function profil(Request $request, EntityManagerInterface $entityManager, ParticipantRepository $participantRepository): Response
    {

        // Récupération des informations de l'utilisateur

        
        $id = '3';

        $participant = $participantRepository->find($id);


        // Formulaire pour modifier le profil
        
        $participantForm = $this->createForm(ProfilType::class, $participant);

        $participantForm->handleRequest($request);

        if ($participantForm->isSubmitted()){

            $entityManager->persist($participant);
            $entityManager->flush();
        }


        return $this->render('profil.html.twig', [
            'participant' => $participant, 'participantForm' => $participantForm->createView()
        ]);

    }
}