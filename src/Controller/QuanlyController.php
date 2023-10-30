<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuanlyController extends AbstractController
{
    #[Route('/quanly', name: 'app_quanly')]
    public function index(): Response
    {
        return $this->render('quanly/index.html.twig', [
            'controller_name' => 'QuanlyController',
        ]);
    }
}
