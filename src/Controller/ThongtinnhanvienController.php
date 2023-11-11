<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ThongtinnhanvienController extends AbstractController
{
    #[Route('/thongtinnhanvien', name: 'app_thongtinnhanvien')]
    public function index(): Response
    {
        return $this->render('thongtinnhanvien/index.html.twig', [
            'controller_name' => 'ThongtinnhanvienController',
        ]);
    }
}
