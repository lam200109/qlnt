<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;

class BaocaomuahangController extends AbstractController
{
    /**
     * @Route("/baocaomuahang", name="baocaomuahang")
     */
    public function index(Connection $connection): Response
    {
        $purchaseInvoicesSql = "SELECT * FROM PurchaseInvoices pi           
            JOIN Distributors d ON pi.DistributorID = d.DistributorID";

        $purchaseInvoices = $connection->executeQuery($purchaseInvoicesSql)->fetchAllAssociative();

        return $this->render('baocaomuahang/index.html.twig', [
            'purchaseInvoices' => $purchaseInvoices,
        ]);
    }
}
