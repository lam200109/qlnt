<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BaocaotonkhoController extends AbstractController
{
    #[Route('/baocaotonkho', name: 'app_baocaotonkho')]
    public function index(): Response
    {
        return $this->render('baocaotonkho/index.html.twig', [
            'controller_name' => 'BaocaotonkhoController',
        ]);
    }
}
