<?php

namespace App\Controller;

use App\Entity\Campus;
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
     * @Route("/profiles/profileModif", name="profile_modif")
     */
    public function profileModif(Request $request, EntityManagerInterface $entityManager, ParticipantRepository $participantRepository): Response
    {

        // Get user informations

        $participant = $this->getUser();

        // Form for change the profile
        
        $participantForm = $this->createForm(ProfilType::class, $participant);

        $participantForm->handleRequest($request);

        if ($participantForm->isSubmitted()){

            $entityManager->persist($participant);
            $entityManager->flush();
        }

        return $this->render('profiles/profileModif.html.twig', [
            'participant' => $participant, 'participantForm' => $participantForm->createView()
        ]);

    }

    /**
     * @Route("/profiles/profileDisplay/{pseudo}", name="profile_display")
     */
    public function profileDisplay(ParticipantRepository $participantRepository, String $pseudo){
       
    
        $utilisateur = $participantRepository->findOneByPseudo($pseudo);

        return $this->render('profiles/profileDisplay.html.twig', [
            'utilisateur' => $utilisateur ]);
    }
}