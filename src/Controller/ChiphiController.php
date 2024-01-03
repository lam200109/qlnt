<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;

class ChiphiController extends AbstractController
{
    #[Route('/chiphi', name: 'chiphi')]
    public function index(Request $request, Connection $connection): Response
    {
        // Lấy tháng và năm từ dữ liệu nhập liệu của người dùng
        $month = $request->query->get('month');
        $year = $request->query->get('year');

        // Gán giá trị mặc định nếu tháng và năm không có giá trị
        $month = $month ?: date('n');  // Sử dụng tháng hiện tại nếu không có giá trị
        $year = $year ?: date('Y');    // Sử dụng năm hiện tại nếu không có giá trị

        // Truy vấn để lấy ra tổng TotalEarnings với PaymentStatus = 1 cho tháng và năm đã chọn
        $sqlTotalEarningsPaid = "SELECT SUM(Amount) AS TotalEarningsPaid FROM PurchaseInvoices WHERE ExpenseType = 'Trả lương nhân viên' AND MONTH(Date) = :month AND YEAR(Date) = :year";
        $totalEarningsPaid = $connection->executeQuery($sqlTotalEarningsPaid, ['month' => $month, 'year' => $year])->fetchOne();

        // Truy vấn để lấy ra tổng TotalEarnings với PaymentStatus = 0 cho tháng và năm đã chọn
        $sqlTotalEarningsNotPaid = "SELECT SUM(TotalEarnings) AS TotalEarningsNotPaid FROM Salary WHERE PaymentStatus = 0 AND MONTH(Date) = :month AND YEAR(Date) = :year";
        $totalEarningsNotPaid = $connection->executeQuery($sqlTotalEarningsNotPaid, ['month' => $month, 'year' => $year])->fetchOne();

        // Truy vấn để lấy ra tổng TotalEarnings với PaymentStatus = 1 cho tháng trước
        $sqlTotalEarningsPaidLastMonth = "SELECT SUM(TotalEarnings) AS TotalEarningsPaidLastMonth FROM Salary WHERE PaymentStatus = 1 AND MONTH(Date) = :lastMonth AND YEAR(Date) = :year";
        $totalEarningsPaidLastMonth = $connection->executeQuery($sqlTotalEarningsPaidLastMonth, ['lastMonth' => $month - 1, 'year' => $year])->fetchOne();

        // Truy vấn để lấy ra tổng TotalEarnings với PaymentStatus = 0 cho tháng trước
        $sqlTotalEarningsNotPaidLastMonth = "SELECT SUM(TotalEarnings) AS TotalEarningsNotPaidLastMonth FROM Salary WHERE PaymentStatus = 0 AND MONTH(Date) = :lastMonth AND YEAR(Date) = :year";
        $totalEarningsNotPaidLastMonth = $connection->executeQuery($sqlTotalEarningsNotPaidLastMonth, ['lastMonth' => $month - 1, 'year' => $year])->fetchOne();

        // Truy vấn để lấy ra tổng Amount của PurchaseInvoices cho tháng và năm đã chọn
        $sqlTotalExpenseThisMonth = "SELECT SUM(Amount) AS TotalExpenseThisMonth FROM PurchaseInvoices WHERE ExpenseType = 'Nhập hàng' AND MONTH(Date) = :month AND YEAR(Date) = :year";
        $totalExpenseThisMonth = $connection->executeQuery($sqlTotalExpenseThisMonth, ['month' => $month, 'year' => $year])->fetchOne();

        // Truy vấn để lấy ra tổng Amount của PurchaseInvoices cho tháng trước và năm đã chọn
        $sqlTotalExpenseLastMonth = "SELECT SUM(Amount) AS TotalExpenseLastMonth FROM PurchaseInvoices WHERE MONTH(Date) = :lastMonth AND YEAR(Date) = :year";
        $totalExpenseLastMonth = $connection->executeQuery($sqlTotalExpenseLastMonth, ['lastMonth' => $month - 1, 'year' => $year])->fetchOne();

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

        // Truy vấn thông tin chi phí từ bảng PurchaseInvoices và Distributors cho tháng và năm đã chọn
        $sqlChiphi = "SELECT * FROM PurchaseInvoices
        WHERE MONTH(PurchaseInvoices.Date) = :month AND YEAR(PurchaseInvoices.Date) = :year;
        ;
 ";
        $result = $connection->executeQuery($sqlChiphi, ['month' => $month, 'year' => $year])->fetchAllAssociative();


        if ($request->isMethod('POST')) {
            $expenseType = $request->request->get('ExpenseType');
            $amount = $request->request->get('Amount');

            // Kiểm tra xem cả hai trường ExpenseType và Amount đã được điền đầy đủ hay không
            if (empty($expenseType) || empty($amount)) {
                $this->addFlash('error', 'Vui lòng điền đầy đủ thông tin loại phí và tổng phí.');
                return $this->redirectToRoute('chi_phi');
            }

            // Thực hiện truy vấn SQL để chèn dữ liệu
            $sql = "INSERT INTO PurchaseInvoices (ExpenseType, Amount, Date) VALUES (?, ?, NOW())";
            $connection->executeStatement($sql, [$expenseType, $amount]);

            // Thêm flash message thành công
            $this->addFlash('success', 'Dữ liệu đã được thêm thành công.');

            // Chuyển hướng hoặc hiển thị thông báo thành công nếu cần thiết
            return $this->redirectToRoute('chi_phi');
        }


// Đoạn mã SQL để tính tổng của cột TotalAmount
$sql = "
    SELECT 
        SUM(TotalAmount) AS GrandTotal
    FROM (
        SELECT 
            SUM(Amount) AS TotalAmount
        FROM 
            PurchaseInvoices
        WHERE 
            ExpenseType NOT IN ('Nhập hàng', 'Trả lương nhân viên')
        GROUP BY 
            ExpenseType
    ) AS Subquery
";

// Thực hiện câu truy vấn và lấy kết quả
$grandTotal = $connection->executeQuery($sql)->fetchOne();











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
            'grandTotal' => $grandTotal,
        ]);
    }
}
