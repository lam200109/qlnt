<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;

class BaocaomuahangController extends AbstractController
{
    /**
     * @Route("/baocaomuahang", name="baocaomuahang")
     */
    public function index(Request $request, Connection $connection): Response
    {
        // Lấy tháng, năm, ngày bắt đầu và ngày kết thúc từ dữ liệu nhập liệu của người dùng
     // ...

     $month = $request->query->get('month');
     $year = $request->query->get('year');
     $startDate = $request->query->get('startDate');
     $endDate = $request->query->get('endDate');
     
     // Gán giá trị mặc định nếu tháng và năm không có giá trị
     $month = $month ?: date('n');  // Sử dụng tháng hiện tại nếu không có giá trị
     $year = $year ?: date('Y');    // Sử dụng năm hiện tại nếu không có giá trị
     
     // Các hóa đơn mua hàng
     $purchaseInvoicesSql = "SELECT pi.*, d.*, FORMAT(pi.Amount, 0) AS Amount
     FROM PurchaseInvoices pi
     JOIN Distributors d ON pi.DistributorID = d.DistributorID
     WHERE (:month IS NOT NULL AND :year IS NOT NULL AND MONTH(pi.Date) = :month AND YEAR(pi.Date) = :year)
     OR (:startDate IS NOT NULL AND (:endDate IS NULL OR (pi.Date BETWEEN :startDate AND :endDate OR pi.Date IS NULL)))";

     $params = [
         'month' => $month,
         'year' => $year,
         'startDate' => $startDate !== '' ? $startDate : null,
         'endDate' => $endDate !== '' ? $endDate : null,
     ];
     
     $purchaseInvoices = $connection->executeQuery($purchaseInvoicesSql, $params)->fetchAllAssociative();
     
// Dữ liệu về doanh số trong tuần

return $this->render('baocaomuahang/index.html.twig', [
    'purchaseInvoices' => $purchaseInvoices,
    'selectedMonth' => $month,
    'selectedYear' => $year,
]);
    }
    
}
