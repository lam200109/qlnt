<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DanhsachkhachhangController extends AbstractController
{
    #[Route('/danhsachkhachhang', name: 'app_danhsachkhachhang')]
    public function index(): Response
    {
        return $this->render('danhsachkhachhang/index.html.twig', [
            'controller_name' => 'DanhsachkhachhangController',
        ]);
    }
}
