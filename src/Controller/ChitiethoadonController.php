<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChitiethoadonController extends AbstractController
{
    #[Route('/chitiethoadon', name: 'app_chitiethoadon')]
    public function index(): Response
    {
        return $this->render('chitiethoadon/index.html.twig', [
            'controller_name' => 'ChitiethoadonController',
        ]);
    }
}
