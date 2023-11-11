<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BaocaomuahangController extends AbstractController
{
    #[Route('/baocaomuahang', name: 'app_baocaomuahang')]
    public function index(): Response
    {
        return $this->render('baocaomuahang/index.html.twig', [
            'controller_name' => 'BaocaomuahangController',
        ]);
    }
}
