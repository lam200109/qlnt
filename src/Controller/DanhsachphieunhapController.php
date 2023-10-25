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
        if ($request->isMethod('POST')) {
            $BatDau = $request->request->get('BatDau');
            $KetThuc = $request->request->get('KetThuc');

            // Thực hiện truy vấn SQL sử dụng Doctrine DBAL
            $sql = "SELECT HoaDonNhap.MaHDN, HoaDonNhap.TongTienHD, HoaDonNhap.NgayNhap, NhaPhanPhoi.TenNPP, Thuoc.TenThuoc, ChiTietHoaDonNhap.SoLuongNhap
                FROM HoaDonNhap 
                INNER JOIN NhaPhanPhoi ON NhaPhanPhoi.MaNPP = HoaDonNhap.MaNPP
                INNER JOIN ChiTietHoaDonNhap ON ChiTietHoaDonNhap.MaHDN = HoaDonNhap.MaHDN
                INNER JOIN Thuoc ON Thuoc.idThuoc = ChiTietHoaDonNhap.idThuoc";

            $sql1 = "SELECT HoaDonNhap.MaHDN, HoaDonNhap.TongTienHD, HoaDonNhap.NgayNhap, NhaPhanPhoi.TenNPP, Thuoc.TenThuoc, ChiTietHoaDonNhap.SoLuongNhap
                FROM HoaDonNhap 
                INNER JOIN NhaPhanPhoi ON NhaPhanPhoi.MaNPP = HoaDonNhap.MaNPP
                INNER JOIN ChiTietHoaDonNhap ON ChiTietHoaDonNhap.MaHDN = HoaDonNhap.MaHDN 
                INNER JOIN Thuoc ON Thuoc.idThuoc = ChiTietHoaDonNhap.idThuoc
                WHERE HoaDonNhap.NgayNhap >= :BatDau AND HoaDonNhap.NgayNhap <= :KetThuc";

            $rs = $connection->executeQuery($sql)->fetchAll();
            $rs1 = $connection->executeQuery($sql1, [
                'BatDau' => $BatDau,
                'KetThuc' => $KetThuc,
            ]);

            // Hiển thị kết quả trong template
            return $this->render('danhsachphieunhap/index.html.twig', [
                'result' => $rs,
                'result1' => $rs1,
            ]);
        }

        return $this->render('danhsachphieunhap/index.html.twig');
    }
}

