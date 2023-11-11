<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DanhsachnhanvienController extends AbstractController
{
    #[Route('/danhsachnhanvien', name: 'app_danhsachnhanvien')]
    public function index(): Response
    {
        return $this->render('danhsachnhanvien/index.html.twig', [
            'controller_name' => 'DanhsachnhanvienController',
        ]);
    }
}
