<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;

class BaocaotonkhoController extends AbstractController
{
    /**
     * @Route("/baocaotonkho", name="baocaotonkho")
     */
    public function index(Connection $connection): Response
    {
        // Sử dụng Connection để thực hiện truy vấn SQL
        $sql = "
        SELECT
        m.MedicineID AS idThuoc,
        m.Name AS TenThuoc,
        m.Price AS GiaBan,
        COALESCE(SUM(pdn.Quantity), 0) AS TongSoLuongNhap,
        COALESCE(pdn.Price, 0) AS GiaNhap,
        COALESCE(SUM(six.Quantity), 0) AS TongSoLuongXuat,
        COALESCE(SUM(pdn.Quantity * COALESCE(pdn.Price, 0)), 0) AS TongTienNhap,
        COALESCE(SUM(six.Quantity * m.Price), 0) AS TongTienXuat,
        COALESCE(SUM(six.Quantity * m.Price), 0) - COALESCE(SUM(pdn.Quantity * COALESCE(pdn.Price, 0)), 0) AS DoanhThu,
        d.DistributorName AS NhaSanXuat,
        d.Email AS Email,
        (COALESCE(SUM(pdn.Quantity), 0) - COALESCE(SUM(six.Quantity), 0)) AS TonKhoHienTai  -- Tính tồn kho hiện tại
    FROM
        Medicines m
    LEFT JOIN PurchaseInvoiceDetails pdn ON m.MedicineID = pdn.MedicineID
    LEFT JOIN SalesInvoiceDetails six ON m.MedicineID = six.MedicineID
    LEFT JOIN Distributors d ON m.ManufacturerID = d.DistributorID
    GROUP BY
        m.MedicineID,
        m.Name,
        m.Price,
        pdn.Price,
        d.DistributorName;  
        ";

        $sql = $connection->executeQuery($sql)->fetchAllAssociative();


        // Đưa dữ liệu vào view hoặc xử lý theo ý muốn

        return $this->render('baocaotonkho/index.html.twig', [
            'result' => $sql,
        ]);
    }
}
