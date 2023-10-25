<?php

// src/Controller/YourLoginController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DangnhapController extends AbstractController
{
    /**
     * @Route("/dangnhap", name="dangnhap")
     */
    public function Trangchu()
    {   
        // Xử lý đăng nhập ở đây

        // Nếu đăng nhập thành công, chuyển hướng đến trang chủ
        return $this->redirectToRoute('trangchu');
    }
}
