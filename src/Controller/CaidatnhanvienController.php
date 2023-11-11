<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CaidatnhanvienController extends AbstractController
{
    #[Route('/caidatnhanvien', name: 'app_caidatnhanvien')]
    public function index(): Response
    {
        return $this->render('caidatnhanvien/index.html.twig', [
            'controller_name' => 'CaidatnhanvienController',
        ]);
    }
}
