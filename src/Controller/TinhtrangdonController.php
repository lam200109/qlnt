<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TinhtrangdonController extends AbstractController
{
    #[Route('/tinhtrangdon', name: 'app_tinhtrangdon')]
    public function index(): Response
    {
        return $this->render('tinhtrangdon/index.html.twig', [
            'controller_name' => 'TinhtrangdonController',
        ]);
    }
}
