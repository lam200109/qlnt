<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DanhsachnhasanxuatController extends AbstractController
{
    #[Route('/danhsachnhasanxuat', name: 'app_danhsachnhasanxuat')]
    public function index(): Response
    {
        return $this->render('danhsachnhasanxuat/index.html.twig', [
            'controller_name' => 'DanhsachnhasanxuatController',
        ]);
    }
}
