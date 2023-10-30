<?php

// src/Controller/DanhsachthuocController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Connection;
use Symfony\Component\Routing\Annotation\Route;


class DanhsachthuocController extends AbstractController
{

    /**
 * @Route("/danhsachthuoc/update/{id}", name="app_danhsachthuoc_update")
 */
public function updateThuoc(Request $request, Connection $connection, $id): Response
{
    $TenThuoc = $request->request->get('TenThuoc');
    $SoLuong = $request->request->get('SoLuong');
    $GiaBan = $request->request->get('GiaBan');

    $sql = "UPDATE thuoc SET SoLuong = :SoLuong, GiaBan = :GiaBan WHERE idThuoc = :id";
    $connection->executeStatement($sql, [
        'SoLuong' => $SoLuong,
        'GiaBan' => $GiaBan,
        'id' => $id,
    ]);

    // Trả về một thông báo hoặc redirect theo ý muốn của bạn
    // Ví dụ: chuyển hướng đến trang danh sách thuốc sau khi cập nhật
    return $this->redirectToRoute('app_danhsachthuoc');
}

    /**
     * @Route("/danhsachthuoc/delete/{id}", name="app_danhsachthuoc_delete")
     */
    public function deleteThuoc(Connection $connection, $id): Response
    {
        // Xử lý xóa thuốc ở đây sử dụng $id

        // Xóa dữ liệu từ bảng chitiethoadonnhap
        $sql1 = "DELETE FROM chitiethoadonnhap WHERE idThuoc = :id";
        $connection->executeStatement($sql1, ['id' => $id]);

        // Xóa dữ liệu từ bảng chitiethoadonxuat
        $sql2 = "DELETE FROM chitiethoadonxuat WHERE idThuoc = :id";
        $connection->executeStatement($sql2, ['id' => $id]);

        // Xóa dữ liệu từ bảng thuoc
        $sql = "DELETE FROM thuoc WHERE idThuoc = :id";
        $connection->executeStatement($sql, ['id' => $id]);

        // Trả về một thông báo hoặc redirect theo ý muốn của bạn
        // Ví dụ: chuyển hướng đến trang danh sách thuốc sau khi xóa
        return $this->redirectToRoute('app_danhsachthuoc');
    }

  /**
     * @Route("/danhsachthuoc", name="app_danhsachthuoc")
     */

     public function index(Request $request, Connection $connection): Response
     {
         $sql = "SELECT * FROM Thuoc ORDER BY SoLuong ASC";
     
     
         if ($request->isMethod('POST') && $request->request->has('submit')) {
             $TenThuoc = $request->request->get('TenThuoc');
     
             // Kiểm tra và xử lý giá trị TenThuoc để đảm bảo an toàn trước khi sử dụng nó trong câu lệnh SQL
             $TenThuoc = $this->sanitizeInput($TenThuoc);
     
             // Câu lệnh SQL tìm kiếm theo TenThuoc
             $sql1 = "SELECT * FROM Thuoc WHERE TenThuoc LIKE '%$TenThuoc'";
     
             // Thực thi câu lệnh SQL
             $rs = $connection->executeQuery($sql1)->fetchAllAssociative();
         } else {

            $rs = $connection->executeQuery($sql)->fetchAllAssociative();
        }
     
         // Thực hiện các thao tác cần thiết với $rs và $rs1 ở đây
         // Ví dụ: trả về một trang web với dữ liệu đã truy vấn
     
         return $this->render('danhsachthuoc/index.html.twig', [
             'result' => $rs,
         ]);
     }
     
     private function sanitizeInput($input)
     {
         // Hàm xử lý và làm sạch giá trị đầu vào ở đây (ví dụ: sử dụng PDO hoặc sử dụng Prepared Statement)
         // Đảm bảo bạn đã xử lý giá trị đầu vào để tránh tấn công SQL Injection
         return $input;
     }
     
}
