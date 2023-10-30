<?php

// src/Controller/LichSuGiaoDichController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;

class LichsugiaodichController extends AbstractController
{
    /**
     * @Route("/lichsugiaodich", name="app_lichsugiaodich")
     */
    public function index(Request $request, Connection $connection): Response
    {
        $ngayHienTai = new \DateTime('now', new \DateTimeZone('Asia/Ho_Chi_Minh'));
        $ngayHienTai = $ngayHienTai->format('Y-m-d');
        $BatDau = $request->request->get('BatDau');
        $KetThuc = $request->request->get('KetThuc');
        $rs = [];
        $sql = "SELECT bn.IDBN, bn.HoTen, bn.DiaChi, bn.SoDienThoai, hdx.MaHDX, hdx.TongTienHD, hdx.NgayXuat, cthdx.SoLuong, t.TenThuoc
        FROM BenhNhan bn
        JOIN HoaDonXuat hdx ON bn.IDBN = hdx.IDBN
        JOIN ChiTietHoaDonXuat cthdx ON hdx.MaHDX = cthdx.MaHDX
        JOIN Thuoc t ON cthdx.idThuoc = t.idThuoc";


            if (!empty($BatDau) && !empty($KetThuc)) {
              
               $sql .= " WHERE hdx.NgayXuat BETWEEN '$BatDau' AND '$KetThuc'";
               $rs = $connection->executeQuery($sql)->fetchAllAssociative();

            } else {
                $rs = $connection->executeQuery($sql)->fetchAllAssociative();

            }
        

        return $this->render('lichsugiaodich/index.html.twig', [
            'result' => $rs,
        ]);
    }
}
