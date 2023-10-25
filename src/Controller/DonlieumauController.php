<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DonlieumauController extends AbstractController
{
    #[Route('/donlieumau', name: 'app_donlieumau')]
    public function index(): Response
    {
        return $this->render('donlieumau/index.html.twig', [
            'controller_name' => 'DonlieumauController',
        ]);
    }
}
