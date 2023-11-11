<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ThemkhachhangController extends AbstractController
{
    #[Route('/themkhachhang', name: 'app_themkhachhang')]
    public function index(): Response
    {
        return $this->render('themkhachhang/index.html.twig', [
            'controller_name' => 'ThemkhachhangController',
        ]);
    }
}
