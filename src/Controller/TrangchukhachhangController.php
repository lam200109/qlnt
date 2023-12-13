<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrangchukhachhangController extends AbstractController
{
    #[Route('/trangchukhachhang', name: 'app_trangchukhachhang')]
    public function index(): Response
    {
        return $this->render('trangchukhachhang/index.html.twig', [
            'controller_name' => 'TrangchukhachhangController',
        ]);
    }
}
