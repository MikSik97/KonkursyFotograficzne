<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        if($this->getUser()){
            $username = $this->getUser()->getUsername();
        } else{
            $username = null;
        }
        return $this->render('main/home.html.twig',[
            'username' => $username,
            ]);
    }
}
