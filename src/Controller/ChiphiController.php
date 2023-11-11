<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChiphiController extends AbstractController
{
    #[Route('/chiphi', name: 'app_chiphi')]
    public function index(): Response
    {
        return $this->render('chiphi/index.html.twig', [
            'controller_name' => 'ChiphiController',
        ]);
    }
}
