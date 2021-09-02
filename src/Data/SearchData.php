<?php

namespace App\Data;

use Symfony\Component\Validator\Constraints\Date;

class SearchData
{
    /**
     * @var string
     */
    public $q = '';

    /**
     * @var null|integer
     */
    public $campus;

    /**
     * @var Date
     */
    public $dateMin;

    /**
     * @var Date
     */
    public $dateMax;

    /**
     * @var boolean
     */
    public $organisateur = false;

    /**
     * @var boolean
     */
    public $participant = false;

    /**
     * @var boolean
     */
    public $nonParticipant = false;

    /**
     * @var boolean
     */
    public $sortiesPassees = false;

}