<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;

class DanhsachthuocController extends AbstractController
{
    #[Route('/danhsachthuoc', name: 'danhsachthuoc')]
    public function index(Connection $connection): Response
    {
        $sql = "SELECT * FROM Medicines
        ";
        
        $rows = $connection->executeQuery($sql)->fetchAllAssociative();

  

        // Truyền dữ liệu vào view
        return $this->render('danhsachthuoc/index.html.twig', [
            'controller_name' => 'DanhsachthuocController',
            'result' => $rows,
        ]);
    }
}
