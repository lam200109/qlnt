<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DondathangonlineController extends AbstractController
{
    #[Route('/dondathangonline', name: 'app_dondathangonline')]
    public function index(): Response
    {
        return $this->render('dondathangonline/index.html.twig', [
            'controller_name' => 'DondathangonlineController',
        ]);
    }
}
