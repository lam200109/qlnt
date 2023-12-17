<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;

class NhanvienController extends AbstractController
{
    #[Route('/nhanvien', name: 'app_nhanvien')]
    public function index(Connection $connection, Request $request): Response
    {
        $sql = "SELECT * FROM Users";
        $users = $connection->executeQuery($sql)->fetchAllAssociative();


        if ($request->isMethod('POST')) {
            $fullName = $request->request->get('FullName');
            $username = $request->request->get('Username');
            $password = $request->request->get('Password');
            $now = new \DateTime();
            $createdDate = $now->format('Y-m-d H:i:s');
            
            // Thực hiện xử lý dữ liệu, ví dụ: lưu vào cơ sở dữ liệu
            // (Bạn cần thêm logic lưu vào cơ sở dữ liệu tại đây)
            $sql = "INSERT INTO Users (FullName, Username, Password, CreatedDate) VALUES (:fullName, :username, :password, :createdDate)";
            $params = [
                'fullName' => $fullName,
                'username' => $username,
                'password' => $password,
                'createdDate' => $createdDate,
            ];

            $connection->executeQuery($sql, $params);

            // Hiển thị thông báo thành công hoặc chuyển hướng đến trang khác
            $this->addFlash('success', 'Thêm nhân viên thành công!');
            return $this->redirectToRoute('nhan_vien');
        }


        return $this->render('nhanvien/index.html.twig', [
            'users' => $users,
        ]);
    }


    #[Route('/nhanvien/delete/{id}', name: 'delete_user')]
    public function deleteUser(Connection $connection, $id): Response
    {
        // Thực hiện xóa người dùng với ID tương ứng từ cơ sở dữ liệu
        $sql = "DELETE FROM Users WHERE UserID = :id";
        $params = ['id' => $id];
        $connection->executeQuery($sql, $params);

        // Hiển thị thông báo thành công hoặc chuyển hướng đến trang khác
        $this->addFlash('success', 'Xóa nhân viên thành công!');
        return $this->redirectToRoute('app_nhanvien');
    }
}
