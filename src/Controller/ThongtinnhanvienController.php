<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Connection;

class ThongtinnhanvienController extends AbstractController
{
    #[Route('/thongtinnhanvien/{id}', name: 'app_thongtinnhanvien')]
    public function index($id, Connection $connection): Response
    {
        // Thực hiện truy vấn SQL để lấy thông tin của người dùng với ID tương ứng
        $sql = "SELECT * FROM Users WHERE UserID = :id";
        $user = $connection->executeQuery($sql, ['id' => $id])->fetchAssociative();

        // Kiểm tra nếu không có người dùng với ID tương ứng
        if (!$user) {
            throw $this->createNotFoundException('Không tìm thấy người dùng với ID ' . $id);
        }

        // Truyền dữ liệu vào view
        return $this->render('thongtinnhanvien/index.html.twig', [
            'result' => $user,
        ]);
    }
}
