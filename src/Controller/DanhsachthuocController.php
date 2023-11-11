<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DanhsachthuocController extends AbstractController
{
    #[Route('/danhsachthuoc', name: 'app_danhsachthuoc')]
    public function index(): Response
    {
        return $this->render('danhsachthuoc/index.html.twig', [
            'controller_name' => 'DanhsachthuocController',
        ]);
    }
}
