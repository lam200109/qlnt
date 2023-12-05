<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


class ThemkhachhangController extends AbstractController
{
    #[Route('/them-khach-hang', name: 'themkhachhang')]
    public function index(Connection $connection): Response
    {
        $sql = "SELECT * FROM Medicines";
        $rows = $connection->executeQuery($sql)->fetchAllAssociative();
        // Truyền dữ liệu vào view
        return $this->render('themkhachhang/index.html.twig', [
            'controller_name' => 'ThemkhachhangController',
            'result' => $rows,
        ]);
        
}
        
        #[Route('/search-medicine', name: 'search_medicine', methods: ['POST'])]
        public function searchMedicine(Request $request, Connection $connection): Response
        {
            $searchTerm = '%' . $request->request->get('searchTerm') . '%';

            $sql = "SELECT * FROM Medicines WHERE Name LIKE ?";
            $rows = $connection->executeQuery($sql, [$searchTerm])->fetchAllAssociative();

            return $this->json(['result' => $rows]);

            
            
            
        }
}
