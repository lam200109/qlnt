<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChitietthuocController extends AbstractController
{
    #[Route('/chitietthuoc', name: 'app_chitietthuoc')]
    public function index(): Response
    {
        return $this->render('chitietthuoc/index.html.twig', [
            'controller_name' => 'ChitietthuocController',
        ]);
    }
}
