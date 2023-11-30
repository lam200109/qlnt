<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DangkyController extends AbstractController
{
    #[Route('/dangky', name: 'dangky')]
    public function index(): Response
    {
        return $this->render('dangky/index.html.twig', [
            'controller_name' => 'DangkyController',
        ]);
    }
}
