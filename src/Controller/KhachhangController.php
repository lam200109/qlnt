<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class KhachhangController extends AbstractController
{
    #[Route('/khachhang', name: 'app_khachhang')]
    public function index(): Response
    {
        return $this->render('khachhang/index.html.twig', [
            'controller_name' => 'KhachhangController',
        ]);
    }
}
