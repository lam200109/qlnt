<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ThemthuocmoiController extends AbstractController
{
    #[Route('/themthuocmoi', name: 'app_themthuocmoi')]
    public function index(): Response
    {
        return $this->render('themthuocmoi/index.html.twig', [
            'controller_name' => 'ThemthuocmoiController',
        ]);
    }
}
