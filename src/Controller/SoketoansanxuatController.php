<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SoketoansanxuatController extends AbstractController
{
    #[Route('/soketoansanxuat', name: 'soketoansanxuat')]
    public function index(): Response
    {
        return $this->render('soketoansanxuat/index.html.twig', [
            'controller_name' => 'SoketoansanxuatController',
        ]);
    }
}
