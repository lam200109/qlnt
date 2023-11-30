<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;

class DanhsachhoadonController extends AbstractController
{
    #[Route('/danhsachhoadon', name: 'danhsachhoadon')]
    public function index(Connection $connection): Response
    {
        $sql = "SELECT 
        'SalesInvoice' AS Type,
        SalesInvoices.SalesInvoiceID AS ID, 
        SalesInvoices.CustomerID, 
        SalesInvoices.Amount, 
        SalesInvoices.Date, 
    
        Customers.Name AS Name
    FROM 
        SalesInvoices
    JOIN 
        Customers ON SalesInvoices.CustomerID = Customers.CustomerID
    
    UNION ALL
    
    SELECT 
        'PurchaseInvoice' AS InvoiceType,
        PurchaseInvoices.PurchaseInvoiceID AS ID, 
        PurchaseInvoices.DistributorID, 
        PurchaseInvoices.Amount, 
        PurchaseInvoices.Date, 
    
        Distributors.DistributorName AS RelatedParty
    FROM 
        PurchaseInvoices
    JOIN 
        Distributors ON PurchaseInvoices.DistributorID = Distributors.DistributorID;
    
        ";
        
        $rows = $connection->executeQuery($sql)->fetchAllAssociative();

  

        // Truyền dữ liệu vào view
        return $this->render('danhsachhoadon/index.html.twig', [
            'controller_name' => 'DanhsachhoadonController',
            'result' => $rows,
        ]);
    }
}
