<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;

class BaocaobanhangController extends AbstractController
{
    /**
     * @Route("/baocaobanhang", name="baocaobanhang")
     */
    public function index(Connection $connection): Response
    {
        // Lấy tổng tiền thu được trong tháng
        $totalAmountSql = "SELECT SUM(Amount) as totalAmount FROM SalesInvoices WHERE MONTH(Date) = MONTH(CURRENT_DATE())";
        $totalAmount = $connection->executeQuery($totalAmountSql)->fetchOne();

        // Lấy tổng tiền thu được so với tháng trước
        $lastMonthAmountSql = "SELECT SUM(Amount) as lastMonthAmount FROM SalesInvoices WHERE MONTH(Date) = MONTH(CURRENT_DATE()) - 1";
        $lastMonthAmount = $connection->executeQuery($lastMonthAmountSql)->fetchOne();

        // Lấy thông tin về thuốc bán chạy nhất trong 30 ngày gần đây
        $bestSellingMedicineSql = "SELECT Medicines.*, SUM(SalesInvoiceDetails.Quantity) as totalQuantity
                                    FROM SalesInvoiceDetails
                                    JOIN Medicines ON SalesInvoiceDetails.MedicineID = Medicines.MedicineID
                                    WHERE SalesInvoiceDetails.CreatedDate >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                                    GROUP BY SalesInvoiceDetails.MedicineID
                                    ORDER BY totalQuantity DESC
                                    LIMIT 1";

        $bestSellingMedicine = $connection->executeQuery($bestSellingMedicineSql)->fetchAssociative();

                // Lấy thông tin về số lượng bán và doanh số so với tuần trước
        $weeklySalesSql = "SELECT 
                WEEK(SalesInvoices.Date) as weekNumber,
                SUM(SalesInvoices.Amount) as weeklyAmount,
                COUNT(DISTINCT SalesInvoices.SalesInvoiceID) as numberOfInvoices
            FROM 
                SalesInvoices
            WHERE 
                WEEK(SalesInvoices.Date) = WEEK(CURDATE()) - 1
            GROUP BY weekNumber";

        $weeklySales = $connection->executeQuery($weeklySalesSql)->fetchAssociative();


       // Lấy thông tin về sản phẩm đã bán
// Lấy thông tin về sản phẩm đã bán
$soldProductsSql = "SELECT 
                        Medicines.Name as productName,
                        SUM(SalesInvoiceDetails.Total) as totalAmount,
                        'Bán hàng' as productCategory,
                        SUM(SalesInvoiceDetails.Quantity) as totalQuantity
                    FROM 
                        SalesInvoiceDetails
                    JOIN 
                        Medicines ON SalesInvoiceDetails.MedicineID = Medicines.MedicineID
                    GROUP BY 
                        SalesInvoiceDetails.MedicineID
                    ORDER BY 
                        totalAmount DESC";



        $soldProducts = $connection->executeQuery($soldProductsSql)->fetchAllAssociative();

        // Truyền dữ liệu vào view
        return $this->render('baocaobanhang/index.html.twig', [
            'totalAmount' => $totalAmount,
            'lastMonthAmount' => $lastMonthAmount,
            'bestSellingMedicine' => $bestSellingMedicine,
            'weeklySales' => $weeklySales,
            'soldProducts' => $soldProducts,
        ]);
    }
}
