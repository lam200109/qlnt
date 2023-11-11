<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CaidatController extends AbstractController
{
    #[Route('/caidat', name: 'app_caidat')]
    public function index(): Response
    {
        return $this->render('caidat/index.html.twig', [
            'controller_name' => 'CaidatController',
        ]);
    }
}
