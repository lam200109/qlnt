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
    public function dangXuat(): Response
    {
        return $this->redirectToRoute('dang_nhap'); // Chuyển hướng sau khi đăng xuất
    }


   

    
}
