<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ThongkedoanhthuController extends AbstractController
{
    #[Route('/thongkedoanhthu', name: 'app_thongkedoanhthu')]
    public function index(): Response
    {
        return $this->render('thongkedoanhthu/index.html.twig', [
            'controller_name' => 'ThongkedoanhthuController',
        ]);
    }
}
