<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class MainController extends AbstractController
{


    /**
     *
     * @Route("/home", name="main_home")
     */
    public function home()
    {
        return $this->render('main/home.html.twig');
    }
}
