<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{


     /**
     * @Route("/home/", name="home")
     */
    public function home()
    {
        dd('hello');
    }


}