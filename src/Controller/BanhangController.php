<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BanhangController extends AbstractController
{
    #[Route('/banhang', name: 'app_banhang')]
    public function index(): Response
    {
        return $this->render('banhang/index.html.twig', [
            'controller_name' => 'BanhangController',
        ]);
    }
}
