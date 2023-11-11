<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BaocaobanhangController extends AbstractController
{
    #[Route('/baocaobanhang', name: 'app_baocaobanhang')]
    public function index(): Response
    {
        return $this->render('baocaobanhang/index.html.twig', [
            'controller_name' => 'BaocaobanhangController',
        ]);
    }
}
