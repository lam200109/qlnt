<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DoitacController extends AbstractController
{
    #[Route('/doitac', name: 'app_doitac')]
    public function index(): Response
    {
        return $this->render('doitac/index.html.twig', [
            'controller_name' => 'DoitacController',
        ]);
    }
}
