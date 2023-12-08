<?php

// src/Controller/DangnhapController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\DBAL\Connection;

class DangnhapController extends AbstractController
{
    /**
     * @Route("/dang-nhap", name="dang_nhap")
     */
    public function index(AuthenticationUtils $authenticationUtils): Response
{

     // Lấy thông tin lỗi đăng nhập (nếu có)
     $error = $authenticationUtils->getLastAuthenticationError();

     // Lấy tên người dùng đã nhập trước đó
     $lastUsername = $authenticationUtils->getLastUsername();

     

     return $this->render('dangnhap/index.html.twig', [
         'last_username' => $lastUsername,
         'error' => $error,
     ]);
}

 /**
     * @Route("/dang-xuat", name="dangxuat")
     */
    public function dangXuat(Connection $connection): Response
    {
        $user = $this->getUser();
        $userId = (int) $user->getUserIdentifier(); 
    
        // Ghi vào LogoutTime
        $this->recordLogoutTime($connection, $userId);
    
        // Đăng xuất người dùng (có thể sử dụng security.token_storage ở đây nếu cần)
    
        return $this->redirectToRoute('dang_nhap');    
    }
    
    private function recordLogoutTime(Connection $connection, $userId): void
    {
        $date = new \DateTime();
    
        $logOutQuery = "
            UPDATE Attendance
            SET LogoutTime = CURRENT_TIME
            WHERE UserID = :userId
            AND Date = :date
            AND LogoutTime IS NULL
        ";
    
        $connection->executeQuery($logOutQuery, [
            'userId' => $userId,
            'date' => $date->format('Y-m-d'),
        ]);
    }
    
    
}
