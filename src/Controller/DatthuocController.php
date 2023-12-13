<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;

class DatthuocController extends AbstractController
{
    #[Route('/datthuoc', name: 'app_datthuoc')]
    public function index(Connection $connection): Response
    {
        $query = "SELECT MedicineID, Name, Image,Price, Category FROM medicines";
        
        // Sử dụng executeQuery và fetchAll của PDO
        $medicines = $connection->executeQuery($query)->fetchAllAssociative(\PDO::FETCH_ASSOC);



        $sql = 'SELECT Category FROM Medicines';
        $result = $connection->executeQuery($sql);
        $category = $result->fetchAssociative();
        return $this->render('datthuoc/index.html.twig', [
            'controller_name' => 'DatthuocController',
            'medicines' => $medicines,
            'category' => $category,

        ]);
    }
}
