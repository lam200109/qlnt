<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;

class ChiphiController extends AbstractController
{
    #[Route('/chiphi', name: 'chiphi')]
    public function index(Connection $connection): Response
    {
        $sql = "SELECT PurchaseInvoices.*, Distributors.DistributorName
        FROM PurchaseInvoices
        JOIN Distributors ON PurchaseInvoices.DistributorID = Distributors.DistributorID;        
        ";

        $result = $connection->executeQuery($sql)->fetchAllAssociative();

        return $this->render('chiphi/index.html.twig', [
            'controller_name' => 'ChiphiController',
            'result' => $result,
        ]);
    }
}
