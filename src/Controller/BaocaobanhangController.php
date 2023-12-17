<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;

class BaocaobanhangController extends AbstractController
{
    /**
 * @Route("/baocaobanhang", name="baocaobanhang")
 */
public function index(Request $request, Connection $connection): Response
{
    // Lấy tháng và năm từ dữ liệu nhập liệu của người dùng
    $month = $request->query->get('month');
    $year = $request->query->get('year');

    // Gán giá trị mặc định nếu tháng và năm không có giá trị
    $month = $month ?: date('n');  // Sử dụng tháng hiện tại nếu không có giá trị
    $year = $year ?: date('Y');    // Sử dụng năm hiện tại nếu không có giá trị

    // Lấy tổng tiền thu được trong tháng
    $totalAmountSql = "SELECT FORMAT(SUM(Amount), 0) as totalAmount FROM SalesInvoices WHERE MONTH(Date) = :month AND YEAR(Date) = :year";
    $totalAmount = $connection->executeQuery($totalAmountSql, ['month' => $month, 'year' => $year])->fetchOne();

    // Lấy tổng tiền thu được so với tháng trước
    $lastMonthAmountSql = "SELECT FORMAT(SUM(Amount), 0) as lastMonthAmount FROM SalesInvoices WHERE MONTH(Date) = :lastMonth AND YEAR(Date) = :year";
    $lastMonthAmount = $connection->executeQuery($lastMonthAmountSql, ['lastMonth' => $month - 1, 'year' => $year])->fetchOne();

    // Lấy thông tin về thuốc bán chạy nhất trong 30 ngày gần đây
    $bestSellingMedicineSql = "SELECT Medicines.*, SUM(SalesInvoiceDetails.Quantity) as totalQuantity
                                FROM SalesInvoiceDetails
                                JOIN Medicines ON SalesInvoiceDetails.MedicineID = Medicines.MedicineID
                                WHERE SalesInvoiceDetails.CreatedDate >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                                  AND (MONTH(SalesInvoiceDetails.CreatedDate) = :month OR :month IS NULL)
                                  AND (YEAR(SalesInvoiceDetails.CreatedDate) = :year OR :year IS NULL)
                                GROUP BY SalesInvoiceDetails.MedicineID
                                ORDER BY totalQuantity DESC
                                LIMIT 1";

    $bestSellingMedicine = $connection->executeQuery($bestSellingMedicineSql, ['month' => $month, 'year' => $year])->fetchAssociative();

    // Lấy thông tin về số lượng bán và doanh số so với tuần trước
    $weeklySalesSql = "SELECT 
            WEEK(SalesInvoices.Date) as weekNumber,
            SUM(SalesInvoices.Amount) as weeklyAmount,
            COUNT(DISTINCT SalesInvoices.SalesInvoiceID) as numberOfInvoices
        FROM 
            SalesInvoices
        WHERE 
            WEEK(SalesInvoices.Date) = WEEK(CURDATE()) - 1
            AND (YEAR(SalesInvoices.Date) = :year OR :year IS NULL)
        GROUP BY weekNumber";

    $weeklySales = $connection->executeQuery($weeklySalesSql, ['year' => $year])->fetchAssociative();

    // Lấy thông tin về sản phẩm đã bán
    $soldProductsSql = "SELECT 
                    Medicines.Name as productName,
                    FORMAT(SUM(SalesInvoiceDetails.Total), 0) as totalAmount,
                    'Bán hàng' as productCategory,
                    SUM(SalesInvoiceDetails.Quantity) as totalQuantity
                FROM 
                    SalesInvoiceDetails
                JOIN 
                    Medicines ON SalesInvoiceDetails.MedicineID = Medicines.MedicineID
                WHERE 
                    (MONTH(SalesInvoiceDetails.CreatedDate) = :month OR :month IS NULL)
                    AND (YEAR(SalesInvoiceDetails.CreatedDate) = :year OR :year IS NULL)
                GROUP BY 
                    SalesInvoiceDetails.MedicineID
                ORDER BY 
                    totalAmount DESC";

    $soldProducts = $connection->executeQuery($soldProductsSql, ['month' => $month, 'year' => $year])->fetchAllAssociative();

    // Truyền dữ liệu vào view
    return $this->render('baocaobanhang/index.html.twig', [
        'totalAmount' => $totalAmount,
        'lastMonthAmount' => $lastMonthAmount,
        'bestSellingMedicine' => $bestSellingMedicine,
        'weeklySales' => $weeklySales,
        'soldProducts' => $soldProducts,
        'selectedMonth' => $month,
        'selectedYear' => $year,
    ]);
}

}
