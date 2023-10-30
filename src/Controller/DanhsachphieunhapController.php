<?php
// src/Controller/DanhsachphieunhapController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;


class DanhsachphieunhapController extends AbstractController
{
    /**
     * @Route("/danhsachphieunhap", name="app_danhsachphieunhap")
     */
    public function index(Request $request, Connection $connection): Response
        {
            $ngayHienTai = new \DateTime('now', new \DateTimeZone('Asia/Ho_Chi_Minh'));
            $ngayHienTai = $ngayHienTai->format('Y-m-d');
            $BatDau = $request->request->get('BatDau');
            $KetThuc = $request->request->get('KetThuc');
            $rs = [];

            // Thực hiện truy vấn SQL sử dụng Doctrine DBAL
            $sql = "SELECT HoaDonNhap.MaHDN, HoaDonNhap.TongTienHD, HoaDonNhap.NgayNhap, NhaPhanPhoi.TenNPP, Thuoc.TenThuoc, ChiTietHoaDonNhap.SoLuongNhap
                FROM HoaDonNhap 
                INNER JOIN NhaPhanPhoi ON NhaPhanPhoi.MaNPP = HoaDonNhap.MaNPP
                INNER JOIN ChiTietHoaDonNhap ON ChiTietHoaDonNhap.MaHDN = HoaDonNhap.MaHDN
                INNER JOIN Thuoc ON Thuoc.idThuoc = ChiTietHoaDonNhap.idThuoc";
            if (!empty($BatDau) && !empty($KetThuc)) {
            
                // Thực hiện truy vấn lọc dữ liệu
                
                $sql .= " WHERE HoaDonNhap.NgayNhap BETWEEN '$BatDau' AND '$KetThuc'";
                $rs = $connection->executeQuery($sql)->fetchAllAssociative();


            } else {

                $rs = $connection->executeQuery($sql)->fetchAllAssociative();
            }

    // Hiển thị kết quả trong template
    return $this->render('danhsachphieunhap/index.html.twig', [
        'result' => $rs,
        'ngayHienTai' => $ngayHienTai,

    ]);
    
}
}        
?>