<?php

namespace App\Service;

use App\Entity\Sortie;
use App\Repository\EtatRepository;

class ClotureSortieService
{
    private $etatRepository;

    public function __construct(EtatRepository $etatRepository)
    {
        $this->etatRepository = $etatRepository;
    }

    public function updateEtatByDateSortie(array $sorties): array
    {
        foreach ($sorties as $sortie) {
            if ($this->dateSortieExpired($sortie)) {
                $etat = $this->etatRepository->findOneByLibelle('Clôturée');
                $sortie->setEtat($etat);
            }
        }

        return $sorties;
    }

    public function dateSortieExpired(Sortie $sortie): bool
    {
        if ($sortie->getDateLimiteInscription() < new \DateTime('now')) {
            return true;
        }

        return false;
    }
}