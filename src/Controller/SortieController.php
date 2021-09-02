<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Sortie;
use App\Form\AnnulSortieType;
use App\Form\SearchType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Service\ClotureSortieService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SortieController extends AbstractController
{
    /**
     * @Route("/sortie/create", name="sortie_create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager, ParticipantRepository $participantRepository, EtatRepository $etatRepository): Response
    {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            switch ($sortieForm->getClickedButton()->getName()) {
                case 'save':
                    $libelle = 'Créée';
                    $message = 'sortie créé ! Merci beaucoup.';
                    $nextAction = 'sortie_list';
                    break;
                case 'saveAndPublish':
                    $libelle = 'Ouverte';
                    $message = 'sortie créé et Publiée ! Merci beaucoup.';
                    $nextAction = 'sortie_list';
                    break;
            }

            $email = $this->getUser()->getUsername();
            $participant = $participantRepository->findOneByEmail($email);

            $etat = $etatRepository->findOneBy(['libelle' => $libelle]);
            $sortie->setOrganisateur($participant);
            $sortie->setEtat($etat);
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash(
                'success',
                $message
            );

            return $this->redirectToRoute($nextAction);
        }

        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $sortieForm->createView(),
        ]);
    }

    /**
     * @Route("/sorties", name="sortie_list")
     */
    public function list(SortieRepository $sortieRepository,
                         ClotureSortieService $clotureSortieService,
                         Request $request
    ): Response
    {
        // $series = $sortieRepository->findAll();
        // $series = $sortieRepository->findBy([], ['nom' => 'DESC', 'dateLimiteInscription' => 'DESC'], 30);
        // $sorties = $sortieRepository->findSorties();
        $sorties = $sortieRepository->findAll();
        $sorties = $clotureSortieService->updateEtatByDateSortie((array)$sorties);
      //  $sorties = $clotureSortieService->sortiesToDisplay($sorties);

        $data = new SearchData();
        $searchForm = $this->createForm(SearchType::class, $data);
        $searchForm->handleRequest($request);

        $sorties = $sortieRepository->findSearch($data);


        // Appel une service de changement d'etat des sorties

        return $this->render('sortie/list.html.twig', [
            'sorties' => $sorties,
            'searchForm' => $searchForm->createView()
        ]);
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

    /**
     * @Route("/sortie/inscription/{id}", name="sortie_inscription")
     */
    public function inscription(EntityManagerInterface $entityManager, SortieRepository $sortieRepository, ParticipantRepository $participantRepository, int $id): Response
    {
        $sortie = $sortieRepository->find($id);

        $email = $this->getUser()->getUsername();
        $participant = $participantRepository->findOneByEmail($email);

        if (!$sortie) {
            throw $this->createNotFoundException('Sortie non trouvée !');
        }
        $sortie->addParticipant($participant);

        $entityManager->persist($sortie);
        $entityManager->flush();

        $this->addFlash(
                'success',
                'Vous été bien enregistre dans la sortie ! Merci beaucoup.'
            );

        return $this->render('sortie/details.html.twig', ['sortie' => $sortie]);
    }

    /**
     * @Route("/sortie/desister/{id}", name="sortie_desister")
     */
    public function desister(EntityManagerInterface $entityManager, SortieRepository $sortieRepository, ParticipantRepository $participantRepository, int $id): Response
    {
        $sortie = $sortieRepository->find($id);
        $email = $this->getUser()->getUsername();

        $participant = $participantRepository->findOneByEmail($email);

        if (!$sortie) {
            throw $this->createNotFoundException('Sortie non trouvée !');
        }

        $sortie->removeParticipant($participant);

        $entityManager->persist($sortie);
        $entityManager->flush();

        $this->addFlash(
                'success',
                'Vous été bien desisncrit de la sortie ! Merci beaucoup.'
            );

        return $this->render('sortie/details.html.twig', ['sortie' => $sortie]);
    }

    /**
     * @Route("/sortie/publier/{id}", name="sortie_publier")
     */
    public function publier(EntityManagerInterface $entityManager, SortieRepository $sortieRepository, EtatRepository $etatRepository, ParticipantRepository $participantRepository, int $id, AuthorizationCheckerInterface $authorizationChecker): Response
    {
        $sortie = $sortieRepository->find($id);

        $email = $this->getUser()->getUsername();

        $organisateur = $participantRepository->findOneByEmail($email);

        $this->denyAccessUnlessGranted('sortie_publier', $sortie);

        if (!$sortie) {
            throw $this->createNotFoundException('Sortie non trouvée !');
        } elseif ($sortie->getOrganisateur() !== $organisateur) {
            throw $this->createNotFoundException('Vous n\'aves pas les droit de publier cette sortie !');
        }

        $etat = $etatRepository->findOneBy(['libelle' => 'Ouverte']);

        $sortie->setEtat($etat);

        $entityManager->persist($sortie);
        $entityManager->flush();

        $this->addFlash(
                        'success',
                        'Vous aves bien publie la sortie ! Merci beaucoup.'
                    );

        return $this->render('sortie/details.html.twig', ['sortie' => $sortie]);
    }

    /**
     * @Route("/annule/{id}", name="sortie_annuler")
     *
     * @param $entityManager
     *
     * @return Response
     */
    public function annule(int $id,
                           SortieRepository $sortieRepository,
                           Request $request,
                           EntityManagerInterface $entityManager,
                           EtatRepository $etatRepository
    ): Response {
        $sortie = $sortieRepository->findOneBySomeField($id);

        $annulForm = $this->createForm(AnnulSortieType::class, $sortie);

        //traitement du formulaire
        $annulForm->handleRequest($request);

        if ($annulForm->isSubmitted()) {
            $etat = $etatRepository->findOneBy(['libelle' => 'Annulée']);
            $sortie->setEtat($etat);
            //message flash pour annoncer la bonne exécution de l'annulation
            $this->addFlash('success', 'la sortie a été annulée');

            //envoi des données à la BD pour modifier le champs infosSortie
            $entityManager->persist($sortie);
            $entityManager->flush();

            //redirection vers la page d'accueil
            return $this->redirectToRoute('main_home');
        }

        return $this->render('sortie/annule.html.twig', [
            'sortie' => $sortie,
            'annulForm' => $annulForm->createView(),
        ]);
    }
}