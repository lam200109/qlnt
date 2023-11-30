<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;

class DanhsachnhanvienController extends AbstractController
{
    #[Route('/danhsachnhanvien', name: 'danhsachnhanvien')]
    public function index(Connection $connection): Response
    {
        // Sử dụng prepared statements để ngăn chặn SQL Injection
        $sql = "SELECT Users.*, Roles.RoleName
                FROM Users
                JOIN UserRoles ON Users.UserID = UserRoles.UserID
                JOIN Roles ON UserRoles.RoleID = Roles.RoleID";
        
        $rows = $connection->executeQuery($sql)->fetchAllAssociative();

        // Tính toán số lượng thành viên
        $totalMembers = count($rows);

        // Truyền dữ liệu vào view
        return $this->render('danhsachnhanvien/index.html.twig', [
            'controller_name' => 'DanhsachnhanvienController',
            'result' => $rows,
            'totalMembers' => $totalMembers,
        ]);
    }
}
