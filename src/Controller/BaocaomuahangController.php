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
        // Lấy tháng và năm từ dữ liệu nhập liệu của người dùng
        $month = $request->query->get('month');
        $year = $request->query->get('year');

        // Gán giá trị mặc định nếu tháng và năm không có giá trị
        $month = $month ?: date('n');  // Sử dụng tháng hiện tại nếu không có giá trị
        $year = $year ?: date('Y');    // Sử dụng năm hiện tại nếu không có giá trị

        // Tổng tiền nhập hàng trong tháng và năm đã chọn
        $totalAmountSql = "SELECT FORMAT(SUM(Amount), 0) AS totalAmount
                           FROM PurchaseInvoices
                           WHERE MONTH(Date) = :month AND YEAR(Date) = :year";

        $totalAmount = $connection->executeQuery($totalAmountSql, ['month' => $month, 'year' => $year])->fetchOne();

        // Tổng tiền nhập hàng so với tháng trước
        $lastMonthAmountSql = "SELECT FORMAT(SUM(Amount), 0) AS lastMonthAmount
                               FROM PurchaseInvoices
                               WHERE MONTH(Date) = :lastMonth AND YEAR(Date) = :year";

        $lastMonthAmount = $connection->executeQuery($lastMonthAmountSql, ['lastMonth' => $month - 1, 'year' => $year])->fetchOne();

        // Các hóa đơn mua hàng
        $purchaseInvoicesSql = "SELECT pi.*, d.*, FORMAT(pi.Amount, 0) AS Amount
                               FROM PurchaseInvoices pi
                               JOIN Distributors d ON pi.DistributorID = d.DistributorID
                               WHERE (MONTH(pi.Date) = :month OR :month IS NULL)
                                 AND (YEAR(pi.Date) = :year OR :year IS NULL)";

        $purchaseInvoices = $connection->executeQuery($purchaseInvoicesSql, ['month' => $month, 'year' => $year])->fetchAllAssociative();

        // Dữ liệu về doanh số trong tuần
        $weeklySalesSql = "SELECT 
                WEEK(PurchaseInvoices.Date) as weekNumber,
                SUM(PurchaseInvoices.Amount) as weeklyAmount,
                COUNT(DISTINCT PurchaseInvoices.PurchaseInvoiceID) as numberOfInvoices
            FROM 
                PurchaseInvoices
            WHERE 
                WEEK(PurchaseInvoices.Date) = WEEK(CURDATE()) - 1
                AND (YEAR(PurchaseInvoices.Date) = :year OR :year IS NULL)
            GROUP BY weekNumber";

        $weeklySales = $connection->executeQuery($weeklySalesSql, ['year' => $year])->fetchAssociative();

        return $this->render('baocaomuahang/index.html.twig', [
            'totalAmount' => $totalAmount,
            'lastMonthAmount' => $lastMonthAmount,
            'weeklySales' => $weeklySales,
            'purchaseInvoices' => $purchaseInvoices,
            'selectedMonth' => $month,
            'selectedYear' => $year,
        ]);
    }
}
