<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class KiemkekhoController extends AbstractController
{
    #[Route('/kiemkekho', name: 'app_kiemkekho')]
    public function index(): Response
    {
        return $this->render('kiemkekho/index.html.twig', [
            'controller_name' => 'KiemkekhoController',
        ]);
    }
}
