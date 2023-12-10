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
        // Truy vấn để lấy ra tổng TotalEarnings với PaymentStatus = 1 cho tháng hiện tại
        $sqlTotalEarningsPaid = "SELECT SUM(TotalEarnings) AS TotalEarningsPaid FROM Salary WHERE PaymentStatus = 1 AND DATE_FORMAT(Date, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')";
        $totalEarningsPaid = $connection->executeQuery($sqlTotalEarningsPaid)->fetchOne();

        // Truy vấn để lấy ra tổng TotalEarnings với PaymentStatus = 0 cho tháng hiện tại
        $sqlTotalEarningsNotPaid = "SELECT SUM(TotalEarnings) AS TotalEarningsNotPaid FROM Salary WHERE PaymentStatus = 0 AND DATE_FORMAT(Date, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')";
        $totalEarningsNotPaid = $connection->executeQuery($sqlTotalEarningsNotPaid)->fetchOne();

        // Truy vấn để lấy ra tổng TotalEarnings với PaymentStatus = 1 cho tháng trước
        $sqlTotalEarningsPaidLastMonth = "SELECT SUM(TotalEarnings) AS TotalEarningsPaidLastMonth FROM Salary WHERE PaymentStatus = 1 AND DATE_FORMAT(Date, '%Y-%m') = DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 MONTH), '%Y-%m')";
        $totalEarningsPaidLastMonth = $connection->executeQuery($sqlTotalEarningsPaidLastMonth)->fetchOne();

        // Truy vấn để lấy ra tổng TotalEarnings với PaymentStatus = 0 cho tháng trước
        $sqlTotalEarningsNotPaidLastMonth = "SELECT SUM(TotalEarnings) AS TotalEarningsNotPaidLastMonth FROM Salary WHERE PaymentStatus = 0 AND DATE_FORMAT(Date, '%Y-%m') = DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 MONTH), '%Y-%m')";
        $totalEarningsNotPaidLastMonth = $connection->executeQuery($sqlTotalEarningsNotPaidLastMonth)->fetchOne();


           // Truy vấn để lấy ra tổng Amount của PurchaseInvoices cho tháng hiện tại
           $sqlTotalExpenseThisMonth = "SELECT SUM(Amount) AS TotalExpenseThisMonth FROM PurchaseInvoices WHERE DATE_FORMAT(Date, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')";
           $totalExpenseThisMonth = $connection->executeQuery($sqlTotalExpenseThisMonth)->fetchOne();
   
           // Truy vấn để lấy ra tổng Amount của PurchaseInvoices cho tháng trước
           $sqlTotalExpenseLastMonth = "SELECT SUM(Amount) AS TotalExpenseLastMonth FROM PurchaseInvoices WHERE DATE_FORMAT(Date, '%Y-%m') = DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 MONTH), '%Y-%m')";
           $totalExpenseLastMonth = $connection->executeQuery($sqlTotalExpenseLastMonth)->fetchOne();
   
           // Tính toán % so với tháng trước
           $percentageExpenseChange = 0;
           if ($totalExpenseLastMonth != 0) {
               $percentageExpenseChange = (($totalExpenseThisMonth - $totalExpenseLastMonth) / $totalExpenseLastMonth) * 100;
           }
   
           // Round the percentage to 2 decimal places
           $percentageExpenseChange = round($percentageExpenseChange, 2);
   
        // Tính toán % so với tháng trước
        $percentageChange = 0;
        if ($totalEarningsPaidLastMonth != 0) {
            $percentageChange = (($totalEarningsPaid - $totalEarningsPaidLastMonth) / $totalEarningsPaidLastMonth) * 100;
        }

        // Round the percentage to 2 decimal places
        $percentageChange = round($percentageChange, 2);

        // Truy vấn thông tin chi phí từ bảng PurchaseInvoices và Distributors
        $sqlChiphi = "SELECT PurchaseInvoices.PurchaseInvoiceID, PurchaseInvoices.DistributorID, 
            PurchaseInvoices.Date, PurchaseInvoices.ExpenseType, 
            FORMAT(PurchaseInvoices.Amount, 2) AS Amount, Distributors.DistributorName
            FROM PurchaseInvoices
            JOIN Distributors ON PurchaseInvoices.DistributorID = Distributors.DistributorID";
        $result = $connection->executeQuery($sqlChiphi)->fetchAllAssociative();

        return $this->render('chiphi/index.html.twig', [
            'controller_name' => 'ChiphiController',
            'result' => $result,
            'totalEarningsPaid' => $totalEarningsPaid,
            'totalEarningsNotPaid' => $totalEarningsNotPaid,
            'totalEarningsPaidLastMonth' => $totalEarningsPaidLastMonth,
            'totalEarningsNotPaidLastMonth' => $totalEarningsNotPaidLastMonth,
            'percentageChange' => $percentageChange,
            'totalExpenseThisMonth' => $totalExpenseThisMonth,
            'totalExpenseLastMonth' => $totalExpenseLastMonth,
            'percentageExpenseChange' => $percentageExpenseChange,
        ]);
    }
}
