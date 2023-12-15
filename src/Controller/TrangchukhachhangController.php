<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TrangchukhachhangController extends AbstractController
{
    #[Route('/trangchukhachhang', name: 'app_trangchukhachhang')]
    public function index(SessionInterface $session): Response
    {
        $customerId = $session->get('customer_id');

        return $this->render('trangchukhachhang/index.html.twig', [
            'controller_name' => 'TrangchukhachhangController',
            'customer_id' => $customerId,
        ]);
    }
}
