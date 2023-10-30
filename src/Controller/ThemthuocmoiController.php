<?php

// ThemthuocmoiController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Connection;
use Symfony\Component\Routing\Annotation\Route;

class ThemthuocmoiController extends AbstractController
{
    /**
     * @Route("/themthuocmoi", name="app_themthuocmoi")
     */
    public function index(Request $request, Connection $connection)
    {
        $sql = "SELECT * FROM NhomThuoc";
        $sql1 = "SELECT * FROM NhaSanXuat";
        $sql2 = "SELECT * from DonViTinh";

        $rs = $connection->executeQuery($sql)->fetchAllAssociative();
        $rs1 = $connection->executeQuery($sql1)->fetchAllAssociative();
        $rs2 = $connection->executeQuery($sql2)->fetchAllAssociative();

        return $this->render('themthuocmoi/index.html.twig', [
            'result' => $rs,
            'result1' => $rs1,
            'result2' => $rs2,
        ]);

        if ($request->isMethod('POST')) {
            $MaThuoc = $request->request->get('MaThuoc');
            $TenThuoc = $request->request->get('TenThuoc');
            $MaNhom = $request->request->get('MaNhom');
            $NguonGoc = $request->request->get('NguonGoc');
            $MaNSX = $request->request->get('MaNSX');
            $GiaBan = $request->request->get('GiaBan');
            $ThanhPhan = $request->request->get('ThanhPhan');
            $HamLuong = $request->request->get('HamLuong');
            $PhanTacDung = $request->request->get('PhanTacDung');
            $CachDung = $request->request->get('CachDung');
            $CongDung = $request->request->get('CongDung');
            $ChuY = $request->request->get('ChuY');
            $HanSuDung = $request->request->get('HanSuDung');
            $BaoQuan = $request->request->get('BaoQuan');
            $DangPhaChe = $request->request->get('DangPhaChe');
            $MaDVT = $request->request->get('MaDVT');

            // Thêm các thông tin khác tương tự

            if (!empty($MaThuoc) && !empty($TenThuoc)) {
                
                $sql4 = "SELECT COUNT(*) FROM Thuoc WHERE MaThuoc = '$MaThuoc' OR TenThuoc = '$TenThuoc'";
                $rs4 = $connection->executeQuery($sql4)->fetchAllAssociative();
                $count = $rs4[0]['COUNT(*)']; // Lấy giá trị đếm từ kết quả


                if ($count == 0) {
                    $sql5 = "INSERT INTO Thuoc (MaThuoc, TenThuoc, MaNhom, NguonGoc, MaNSX, SoLuong, GiaBan, ThanhPhan, HamLuong, PhanTacDung, CachDung, CongDung, ChuY, HanSuDung, BaoQuan, DangPhaChe, MaDVT)
                            VALUES ('$MaThuoc', '$TenThuoc', '$MaNhom', '$NguonGoc', '$MaNSX', 0, '$GiaBan', '$ThanhPhan', '$HamLuong', '$PhanTacDung', '$CachDung','$CongDung', '$ChuY', '$HanSuDung', '$BaoQuan', '$DangPhaChe', '$MaDVT')";
                    $rs5 = $connection->executeQuery($sql5)->fetchAllAssociative();


                    // Trả về thông báo thành công hoặc chuyển hướng đến trang khác
                    $this->addFlash('success', 'Thêm thuốc mới thành công.');
                } else {
                    // Mã thuốc hoặc Tên thuốc đã tồn tại, trả về thông báo lỗi
                    return $this->render('themthuocmoi/index.html.twig');
                }
            } else {
                // Trường thông tin chưa được nhập đầy đủ, trả về thông báo lỗi
                return $this->render('themthuocmoi/index.html.twig');
            }
        }
      
      
    }

  
}
