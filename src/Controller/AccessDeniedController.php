<?php

// src/Controller/AccessDeniedController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccessDeniedController extends AbstractController
{
    #[Route('/403', name: 'access_denied')]
    public function show(): Response
    {
        return $this->render('error/index.html.twig');
    }
}
