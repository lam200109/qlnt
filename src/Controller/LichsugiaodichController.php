<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LichsugiaodichController extends AbstractController
{
    #[Route('/lichsugiaodich', name: 'app_lichsugiaodich')]
    public function index(): Response
    {
        return $this->render('lichsugiaodich/index.html.twig', [
            'controller_name' => 'LichsugiaodichController',
        ]);
    }
}
