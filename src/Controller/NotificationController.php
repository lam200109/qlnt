<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;

class NotificationController extends AbstractController
{
    /**
     * @Route("/notification", name="notification")
     */
    public function index(Connection $connection): Response
    {
        // Sử dụng Connection để thực hiện truy vấn SQL
        $sql = "
        SELECT
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
        m.Name
    ORDER BY
        TonKhoHienTai ASC;
    
    
";

$sql = $connection->executeQuery($sql)->fetchAllAssociative();

foreach ($sql as &$item) {
    if ($item['TonKhoHienTai'] <= 50) {
        $item['LowStock'] = true;
    } else {
        $item['LowStock'] = false;
    }
    $item['NotificationTime'] = time(); // Lấy thời gian hiện tại

    // Nếu bạn cần biến $result (nếu có) cho trang 'base.html.twig'
    $result[] = $item;
}

// Truyền giá trị $sql (sau khi kiểm tra số lượng tồn kho) vào template của trang 'baocaotonkho/index.html.twig'
return $this->render('baocaotonkho/index.html.twig', [
    'result' => $sql,
]);

// Truyền giá trị $result (nếu có) vào template của trang 'base.html.twig'
return $this->render('base.html.twig', [
    'result' => $result ?? [],
]);
    }
}
