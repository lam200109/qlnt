<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DanhsachphieuxuatController extends AbstractController
{
    #[Route('/danhsachphieuxuat', name: 'app_danhsachphieuxuat')]
    public function index(): Response
    {
        return $this->render('danhsachphieuxuat/index.html.twig', [
            'controller_name' => 'DanhsachphieuxuatController',
        ]);
    }
}
