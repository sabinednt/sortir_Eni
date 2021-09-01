<?php

namespace App\Security\Voter;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Repository\ParticipantRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class SortieVoter extends Voter
{
    const AFFICHER = 'sortie_afficher';
    const PUBLIER = 'sortie_publier';
    const ANNULER = 'sortie_annuler';
    const MODIFIER = 'sortie_modifier';
    const CREATE = 'sortie_create';
    const DESISTER = 'sortie_desister';
    const INCRIPTION = 'sortie_inscription';

    /**
     * security.
     *
     * @var Security
     */
    private $security;

    /**
     * @var ParticipantRepository
     */
    private $participantRepository;

    public function __construct(Security $security, ParticipantRepository $participantRepository)
    {
        $this->security = $security;
        $this->participantRepository = $participantRepository;
    }

    protected function supports($attribute, $subject)
    {
        // la méthode supports() permet de déterminer dans quel contexte le Voter doit s’appliquer
        return in_array($attribute, [self::PUBLIER, self::ANNULER, self::CREATE, self::MODIFIER, self::DESISTER, self::INCRIPTION, self::AFFICHER]) && $subject instanceof Sortie;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        // on récupère l'utilisateur connecté grâce au Token
        $user = $token->getUser();
        // si l'utilisateur est "anonyme", on n'autorise aucune action
        if (!$user instanceof UserInterface) {
            return false;
        }

        $sortie = $subject;
        switch ($attribute) {
            case self::AFFICHER:
                return $this->canAfficher($sortie);
            case self::PUBLIER:
                return $this->canPublier($sortie, $user);
            case self::ANNULER:
                return $this->canAnnuler($sortie, $user);
            case self::CREATE:
                return $this->canCreate($sortie, $user);
            case self::DESISTER:
                return $this->canDesister($sortie, $user);
            case self::MODIFIER:
                return $this->canModifier($sortie, $user);
            case self::INCRIPTION:
                return $this->canInscrire($sortie, $user);
            }

        return false;
    }

    /** * On vérifie si le participant peut afficher la sortie. */
    private function canAfficher(Sortie $sortie): bool
    {
        return in_array($sortie->getEtat()->getLibelle(), ['Ouverte', 'Fermé', 'Activité en cours']) || ($this->security->isGranted('ROLE_ADMIN'));
    }

    /** * On vérifie si le Organisateur peut publier la sortie. */
    private function canPublier(Sortie $sortie, UserInterface $user): bool
    {
        $organisateur = $this->getParticipantByUser($user);

        return ($sortie->getOrganisateur() === $organisateur) && 'Créée' === $sortie->getEtat()->getLibelle() || ($this->security->isGranted('ROLE_ADMIN') && 'Créée' === $sortie->getEtat()->getLibelle());
    }

    /** *  On vérifie si le Organisateur peut annule la sortie. */
    private function canAnnuler(Sortie $sortie, UserInterface $user): bool
    {
        $organisateur = $this->getParticipantByUser($user);

        return ($sortie->getOrganisateur() === $organisateur && 'Ouverte' === $sortie->getEtat()->getLibelle()) || ($this->security->isGranted('ROLE_ADMIN') && 'Ouverte' === $sortie->getEtat()->getLibelle());
    }

    /** * On vérifie si le Organisateur peut créé une sortie. */
    private function canCreate(Sortie $sortie, UserInterface $user): bool
    {
        return (null !== $user) || $this->security->isGranted('ROLE_ADMIN');
    }

    /** * On vérifie si le $participant peux il se desister une sortie. */
    private function canDesister(Sortie $sortie, UserInterface $user): bool
    {
        $participant = $this->getParticipantByUser($user);

        return ($sortie->getParticipant($user->getUsername()) === $participant) && in_array($sortie->getEtat()->getLibelle(), ['Ouverte', 'Fermé']) || ($this->security->isGranted('ROLE_ADMIN') && $sortie->getParticipant($user->getUsername()) === $participant);
    }

    /** * On vérifie si le participant peux il modifier une sortie. */
    private function canModifier(Sortie $sortie, UserInterface $user): bool
    {
        $organisateur = $this->getParticipantByUser($user);

        return ($sortie->getOrganisateur() === $organisateur) && 'Créée' === $sortie->getEtat()->getLibelle() || ($this->security->isGranted('ROLE_ADMIN') && 'Créée' === $sortie->getEtat()->getLibelle());
    }

    /** * On vérifie si le participant peux il s'inscrire a une sortie. */
    private function canInscrire(Sortie $sortie, UserInterface $user): bool
    {
        $participant = $this->getParticipantByUser($user);

        return $sortie->getParticipant($user->getUsername()) !== $participant && 'Ouverte' === $sortie->getEtat()->getLibelle() && count($sortie->getParticipants()) < $sortie->getNbInscriptionsMax();
    }

    /**
     * getParticipantByUser.
     *
     * @param mixed $user
     *
     * @return Participant
     */
    private function getParticipantByUser(UserInterface $user): ?Participant
    {
        $email = $user->getUsername();
        $participant = $this->participantRepository->findOneByEmail($email);

        return $participant;
    }
}