<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DangnhapController extends AbstractController
{
    #[Route('/dangnhap', name: 'app_dangnhap')]
    public function index(): Response
    {
        return $this->render('dangnhap/index.html.twig', [
            'controller_name' => 'DangnhapController',
        ]);
    }
}
