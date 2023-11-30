<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChamcongController extends AbstractController
{
    #[Route('/chamcong', name: 'chamcong')]
    public function index(): Response
    {
        return $this->render('chamcong/index.html.twig', [
            'controller_name' => 'ChamcongController',
        ]);
    }
}
