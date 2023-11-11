<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrangchuController extends AbstractController
{
    #[Route('/trangchu', name: 'app_trangchu')]
    public function index(): Response
    {
        return $this->render('trangchu/index.html.twig', [
            'controller_name' => 'TrangchuController',
        ]);
    }
}
