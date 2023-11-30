<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LuongController extends AbstractController
{
    #[Route('/luong', name: 'luong')]
    public function index(): Response
    {
        return $this->render('luong/index.html.twig', [
            'controller_name' => 'LuongController',
        ]);
    }
}
