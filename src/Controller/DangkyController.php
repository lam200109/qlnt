<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;

class DangkyController extends AbstractController
{
    #[Route('/dang-ky', name: 'dang_ky_customers', methods: ['POST'])]
    public function index(Request $request, Connection $connection, SessionInterface $session): Response
    {
        $name = $request->request->get('Name');
        $username = $request->request->get('Username');
        $password = $request->request->get('Password');
    
        // Kiểm tra xem có khoảng trắng trong $name, $username, và $password hay không
        if (mb_strlen($name) < 1 || mb_strlen($username) < 1 || mb_strlen($password) < 1) {
            $this->addFlash('error', 'Thông tin phải chứa ít nhất một ký tự. Vui lòng kiểm tra lại!');
            return $this->redirectToRoute('dang_ky_customers');
        }
    
        // Thực hiện truy vấn SQL để chèn dữ liệu
        $sql = 'INSERT INTO customers (name, username, password) VALUES (?, ?, ?)';
        $success = $connection->executeQuery($sql, [$name, $username, $password]);
    
        // Kiểm tra xem truy vấn có thành công không
        if ($success) {
            // Nếu thành công, thêm thông báo flash và chuyển hướng tới trang_chu_khach_hang
            $this->addFlash('success', 'Đăng ký thành công!');
            return $this->redirectToRoute('dang_ky_customers');
        } else {
            // Nếu không thành công, thêm thông báo flash và chuyển hướng tới dang_ky
            $this->addFlash('error', 'Đăng ký không thành công. Vui lòng kiểm tra lại thông tin!');
            return $this->redirectToRoute('dang_ky_customers');
        }
    
        return $this->render('dangky/index.html.twig', [
            'controller_name' => 'DangkyController',
        ]);
    }

    #[Route('/dang-ky', name: 'dang_ky', methods: ['GET'])]
    public function dangky(Request $request, Connection $connection, SessionInterface $session): Response
    {
        return $this->render('dangky/index.html.twig', [
            'controller_name' => 'DangkyController',
        ]);
    }
}
