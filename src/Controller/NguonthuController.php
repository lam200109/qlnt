<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NguonthuController extends AbstractController
{
    #[Route('/nguonthu', name: 'app_nguonthu')]
    public function index(): Response
    {
        return $this->render('nguonthu/index.html.twig', [
            'controller_name' => 'NguonthuController',
        ]);
    }
}
