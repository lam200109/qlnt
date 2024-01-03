<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;

class BaocaotonkhoController extends AbstractController
{
    /**
     * @Route("/baocaotonkho", name="baocaotonkho")
     */
    public function index(Request $request, Connection $connection): Response
    {

        $sql = "
            SELECT
                m.ExpirationDate,
                m.LotNumber,
                MAX(m.MedicineID) AS idThuoc,
                m.Name AS TenThuoc,
                MAX(m.Price) AS GiaBan,
                COALESCE(SUM(pdn.Quantity), 0) AS TongSoLuongNhap,
                COALESCE(MAX(pdn.Price), 0) AS GiaNhap,
                COALESCE(SUM(six.Quantity), 0) AS TongSoLuongXuat,
                COALESCE(SUM(pdn.Quantity * COALESCE(pdn.Price, 0)), 0) AS TongTienNhap,
                COALESCE(SUM(six.Quantity * m.Price), 0) AS TongTienXuat,
                COALESCE(SUM(six.Quantity * m.Price), 0) - COALESCE(SUM(pdn.Quantity * COALESCE(pdn.Price, 0)), 0) AS DoanhThu,
                MAX(d.DistributorName) AS NhaSanXuat,
                MAX(d.Email) AS Email,
                (COALESCE(SUM(pdn.Quantity), 0) - COALESCE(SUM(six.Quantity), 0)) AS TonKhoHienTai
            FROM
                Medicines m
            LEFT JOIN PurchaseInvoiceDetails pdn ON m.MedicineID = pdn.MedicineID
            LEFT JOIN SalesInvoiceDetails six ON m.MedicineID = six.MedicineID
            LEFT JOIN Distributors d ON m.ManufacturerID = d.DistributorID
            GROUP BY
                m.ExpirationDate, m.LotNumber, m.Name
            ORDER BY
                TonKhoHienTai ASC;
            ";
    
            $sqlResult = $connection->executeQuery($sql)->fetchAllAssociative();
    
            $result = [];
        foreach ($sqlResult as &$item) {
            if ($item['TonKhoHienTai'] <= 50) {
                $item['LowStock'] = true;
            } else {
                $item['LowStock'] = false;
            }
            $item['NotificationTime'] = time(); // Lấy thời gian hiện tại

            // Nếu bạn cần biến $result (nếu có) cho trang 'base.html.twig'
            $result[] = $item;
        }


    
          





     // Kiểm tra nếu form đã được submit
     if ($request->isMethod('POST')) {
        // Lấy giá trị từ form
        $filterOption = $request->request->get('filterOption');
        $quantityThreshold = $request->request->get('quantityThreshold');

        // Sử dụng Connection để thực hiện truy vấn SQL
        $sql = "
            SELECT
                m.ExpirationDate,
                m.LotNumber,
                MAX(m.MedicineID) AS idThuoc,
                m.Name AS TenThuoc,
                MAX(m.Price) AS GiaBan,
                COALESCE(SUM(pdn.Quantity), 0) AS TongSoLuongNhap,
                COALESCE(MAX(pdn.Price), 0) AS GiaNhap,
                COALESCE(SUM(six.Quantity), 0) AS TongSoLuongXuat,
                COALESCE(SUM(pdn.Quantity * COALESCE(pdn.Price, 0)), 0) AS TongTienNhap,
                COALESCE(SUM(six.Quantity * m.Price), 0) AS TongTienXuat,
                COALESCE(SUM(six.Quantity * m.Price), 0) - COALESCE(SUM(pdn.Quantity * COALESCE(pdn.Price, 0)), 0) AS DoanhThu,
                MAX(d.DistributorName) AS NhaSanXuat,
                MAX(d.Email) AS Email,
                (COALESCE(SUM(pdn.Quantity), 0) - COALESCE(SUM(six.Quantity), 0)) AS TonKhoHienTai
            FROM
                Medicines m
            LEFT JOIN PurchaseInvoiceDetails pdn ON m.MedicineID = pdn.MedicineID
            LEFT JOIN SalesInvoiceDetails six ON m.MedicineID = six.MedicineID
            LEFT JOIN Distributors d ON m.ManufacturerID = d.DistributorID
            WHERE 1 = 1"; // Điều kiện mặc định luôn đúng

        // Thêm điều kiện vào truy vấn dựa trên giá trị của filterOption và quantityThreshold
        if ($filterOption === 'saphetdate') {
            // Thay đổi điều kiện để chỉ hiển thị sản phẩm hiện tại và hạn sử dụng cách nhau 1 tháng
            $sql .= " AND m.ExpirationDate > CURDATE() AND m.ExpirationDate <= DATE_ADD(CURDATE(), INTERVAL 1 MONTH) GROUP BY m.ExpirationDate, m.LotNumber, m.Name ORDER BY TonKhoHienTai ASC";
        } elseif ($filterOption === 'saphethang') {
            $sql .= " GROUP BY m.ExpirationDate, m.LotNumber, m.Name HAVING TonKhoHienTai BETWEEN 0 AND 50 OR TonKhoHienTai <= :quantityThreshold AND TonKhoHienTai >= 0 ORDER BY TonKhoHienTai ASC";
        } elseif ($filterOption === 'hethang') {
            $sql .= " GROUP BY m.ExpirationDate, m.LotNumber, m.Name HAVING TonKhoHienTai <= 0 ORDER BY TonKhoHienTai ASC";
        } else {
            $sql .= " GROUP BY m.ExpirationDate, m.LotNumber, m.Name ORDER BY TonKhoHienTai ASC";
        }
        
        // Thực hiện truy vấn SQL và gán kết quả vào biến $sqlResult
        $sqlResult = $connection->executeQuery($sql, ['quantityThreshold' => $quantityThreshold])->fetchAllAssociative();

        // ... Các bước xử lý kết quả

        // Truyền giá trị $sqlResult (sau khi kiểm tra số lượng tồn kho) vào template của trang 'baocaotonkho/index.html.twig'
        return $this->render('baocaotonkho/index.html.twig', [
            'result' => $sqlResult,
        ]);
    }

    return $this->render('baocaotonkho/index.html.twig', [
        'result' => $sqlResult,
    ]);}
}