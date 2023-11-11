<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DanhsachhoadonController extends AbstractController
{
    #[Route('/danhsachhoadon', name: 'app_danhsachhoadon')]
    public function index(): Response
    {
        return $this->render('danhsachhoadon/index.html.twig', [
            'controller_name' => 'DanhsachhoadonController',
        ]);
    }
}
