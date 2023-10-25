<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BanhangcatlieuController extends AbstractController
{
    #[Route('/banhangcatlieu', name: 'app_banhangcatlieu')]
    public function index(): Response
    {
        return $this->render('banhangcatlieu/index.html.twig', [
            'controller_name' => 'BanhangcatlieuController',
        ]);
    }
}
