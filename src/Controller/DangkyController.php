<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class DangkyController extends AbstractController
{
 /**
     * @Route("/dangky", name="app_dangky")
     */    
    public function index(Request $request, Connection $connection, SessionInterface $session): Response
    {
        if ($request->isMethod('POST')) {
            $session_id = $session->getId();
            $HoTen = $request->request->get('HoTen');
            $TaiKhoan = $request->request->get('TaiKhoan');
            $MatKhau = $request->request->get('MatKhau');
            $DiaChi = $request->request->get('DiaChi');
            $SoDienThoai = $request->request->get('SoDienThoai');

            $sql_insert = "INSERT INTO BenhNhan (TaiKhoan, MatKhau, HoTen, SoDienThoai, DiaChi, session_id, role) 
                VALUES ('$TaiKhoan', '$MatKhau', '$HoTen', '$SoDienThoai', '$DiaChi', '$session_id', 3)";

            if ($connection->executeStatement($sql_insert)) {
                $session->set('TaiKhoan', $TaiKhoan);
                $session->set('HoTen', $HoTen);
                $session->set('DiaChi', $DiaChi);
                $session->set('SoDienThoai', $SoDienThoai);
                $session->set('session_id', $session_id);

                return $this->redirectToRoute('trang_chu'); // Điều hướng đến route tương ứng
            } else {
                $this->addFlash('error', 'Tạo tài khoản thất bại');
            }
        }

        return $this->render('dangky/index.html.twig');
    }

    }
