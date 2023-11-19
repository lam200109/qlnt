<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;

class NguonthuController extends AbstractController
{
    #[Route('/nguonthu', name: 'app_nguonthu')]
    public function index(Connection $connection): Response
    {
        $sql = "SELECT SalesInvoices.*, Customers.Name
        FROM SalesInvoices
        JOIN Customers ON SalesInvoices.CustomerID = Customers.CustomerID;        
        ";

        $result = $connection->executeQuery($sql)->fetchAllAssociative();

        return $this->render('nguonthu/index.html.twig', [
            'controller_name' => 'NguonthuController',
            'result' => $result,
        ]);
    }
}
