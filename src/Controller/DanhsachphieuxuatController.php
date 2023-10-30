<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Connection;


class DanhsachphieuxuatController extends AbstractController
{
   /**
     * @Route("/danhsachphieuxuat", name="app_danhsachphieuxuat")
     */
    public function index(Request $request, Connection $connection): Response
        {
            $ngayHienTai = new \DateTime('now', new \DateTimeZone('Asia/Ho_Chi_Minh'));
            $ngayHienTai = $ngayHienTai->format('Y-m-d');
            $BatDau = $request->request->get('BatDau');
            $KetThuc = $request->request->get('KetThuc');
            $rs = [];

            // Thực hiện truy vấn SQL sử dụng Doctrine DBAL
            $sql = "SELECT HoaDonXuat.MaHDX, BenhNhan.HoTen, Thuoc.TenThuoc, ChiTietHoaDonXuat.SoLuong, HoaDonXuat.TongTienHD, HoaDonXuat.NgayXuat
            FROM BenhNhan
            JOIN HoaDonXuat ON BenhNhan.IDBN = HoaDonXuat.IDBN
            JOIN ChiTietHoaDonXuat ON HoaDonXuat.MaHDX = ChiTietHoaDonXuat.MaHDX
            JOIN Thuoc ON ChiTietHoaDonXuat.idThuoc = Thuoc.idThuoc
            ";
            

            if (!empty($BatDau) && !empty($KetThuc)) {
               
                // Thực hiện truy vấn lọc dữ liệu
                
                $sql .= " WHERE HoaDonXuat.NgayXuat BETWEEN '$BatDau' AND '$KetThuc'";
                $rs = $connection->executeQuery($sql)->fetchAllAssociative();


            } else {

                $rs = $connection->executeQuery($sql)->fetchAllAssociative();
            }

    // Hiển thị kết quả trong template
    return $this->render('danhsachphieuxuat/index.html.twig', [
        'result' => $rs,
        'ngayHienTai' => $ngayHienTai,

    ]);
    
}
}

?>