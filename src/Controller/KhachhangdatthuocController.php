<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class KhachhangdatthuocController extends AbstractController
{
    #[Route('/khachhangdatthuoc', name: 'app_khachhangdatthuoc')]
    public function index(): Response
    {
        return $this->render('khachhangdatthuoc/index.html.twig', [
            'controller_name' => 'KhachhangdatthuocController',
        ]);
    }
}
