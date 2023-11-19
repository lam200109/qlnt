<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;

class DanhsachkhachhangController extends AbstractController
{
    #[Route('/danhsachkhachhang', name: 'app_danhsachkhachhang')]
    public function index(Connection $connection): Response
    {
        $sql = "SELECT * FROM Customers";
        
        $rows = $connection->executeQuery($sql)->fetchAllAssociative();

  

        // Truyền dữ liệu vào view
        return $this->render('danhsachkhachhang/index.html.twig', [
            'controller_name' => 'DanhsachkhachhangController',
            'result' => $rows,
        ]);
    }
}
