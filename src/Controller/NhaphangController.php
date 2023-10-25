<?php


// src/Controller/NhapHangController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Connection;

class NhaphangController extends AbstractController
{
    /**
     * @Route("/nhap-hang", name="nhap_hang", methods={"POST"})
     */
    public function nhapHang(Request $request, EntityManagerInterface $entityManager): Response
    {
        
        $MaNPP = $request->request->get('MaNPP');
        $NguoiGiao = $request->request->get('NguoiGiao');
        $NguoiNhan = $request->request->get('NguoiNhan');
        $SoLuongNhap = $request->request->get('SoLuongNhap');
        $TongTienHD = $request->request->get('TongTienHD');
        $NgayNhap = $request->request->get('NgayNhap');
        $idThuoc = $request->request->get('idThuoc');
        $GiaNhap = $request->request->get('GiaNhap');

        $conn = $entityManager->getConnection();

        $sql4 = "SELECT GiaBan FROM Thuoc WHERE idThuoc = :idThuoc";
        $result = $conn->executeQuery($sql4, ['idThuoc' => $idThuoc]);
        $row = $result->fetchAssociative();
        
        $GiaBan = null;

        // Kiểm tra xem mảng $row có giá trị hợp lệ không
        if (is_array($row) && array_key_exists('GiaBan', $row)) {
            $GiaBan = $row['GiaBan'];
            
            // Tiếp tục xử lý dữ liệu
        } else {
            // Xử lý trường hợp không có dữ liệu hoặc có lỗi
            $this->addFlash('error', 'Không thể lấy giá bán từ cơ sở dữ liệu.');
        }
        
        if ($GiaNhap <= $GiaBan) {
            $sql = "INSERT INTO HoaDonNhap (MaNPP, NguoiGiao, NguoiNhan, TongTienThuoc, TongTienHD, NgayNhap) 
                VALUES (:MaNPP, :NguoiGiao, :NguoiNhan, :TongTienThuoc, :TongTienHD, :NgayNhap)";

            $stmt = $conn->prepare($sql);
            $stmt->execute([
                'MaNPP' => $MaNPP,
                'NguoiGiao' => $NguoiGiao,
                'NguoiNhan' => $NguoiNhan,
                'TongTienThuoc' => $SoLuongNhap * $GiaNhap,
                'TongTienHD' => $TongTienHD,
                'NgayNhap' => $NgayNhap,
            ]);

            $MaHDN = $conn->lastInsertId();

            $sql2 = "INSERT INTO ChiTietHoaDonNhap (MaHDN, idThuoc, SoLuongNhap, GiaNhap)
                VALUES (:MaHDN, :idThuoc, :SoLuongNhap, :GiaNhap)";

            $stmt2 = $conn->prepare($sql2);
            $stmt2->execute([
                'MaHDN' => $MaHDN,
                'idThuoc' => $idThuoc,
                'SoLuongNhap' => $SoLuongNhap,
                'GiaNhap' => $GiaNhap,
            ]);

            $sql3 = "UPDATE Thuoc SET SoLuong = SoLuong + :SoLuongNhap WHERE idThuoc = :idThuoc";
            $stmt3 = $conn->prepare($sql3);
            $stmt3->execute([
                'SoLuongNhap' => $SoLuongNhap,
                'idThuoc' => $idThuoc,
            ]);

            $this->addFlash('success', 'Thêm thuốc mới thành công.');
        } else {
            $this->addFlash('error', 'Giá nhập phải nhỏ hơn hoặc bằng giá bán.');
        }


        
        $sql4 = "SELECT MaNPP, TenNPP FROM NhaPhanPhoi";
        $nhaPhanPhoiData = $conn->executeQuery($sql4)->fetchAll();

        $sql5 = "SELECT idThuoc, TenThuoc, SoLuong from Thuoc";
        $thuocData = $conn->executeQuery($sql5)->fetchAll();
        // Redirect hoặc trả về JSON response tùy theo tình huống

        $sql6 = "SELECT * FROM BenhNhan WHERE role = 2 OR role = 3;";
        $benhnhanData = $conn->executeQuery($sql6)->fetchAll();

        return $this->render('nhaphang/index.html.twig', [
            'nhaPhanPhoiData' => $nhaPhanPhoiData,
            'thuocData' => $thuocData,
            'benhnhanData' => $benhnhanData,

        ]);    
    }
}
