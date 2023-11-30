<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResetmatkhauController extends AbstractController
{
    #[Route('/resetmatkhau', name: 'resetmatkhau')]
    public function index(): Response
    {
        return $this->render('resetmatkhau/index.html.twig', [
            'controller_name' => 'ResetmatkhauController',
        ]);
    }
}
