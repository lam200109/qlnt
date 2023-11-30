<?php
// src/Controller/YourController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    /**
     * @Route("/them_khach_hang", name="them_khach_hang")
     */
    public function themKhachHang(): Response
    {
        $this->denyAccessUnlessGranted('BanThuocAccess', null, 'Bạn không có quyền truy cập trang này!');

        // Xử lý logic khi người dùng có quyền truy cập
        // ...

        return $this->render('your_template/them_khach_hang.html.twig');
    }

    /**
     * @Route("/danh_sach_khach_hang", name="danh_sach_khach_hang")
     */
    public function danhSachKhachHang(): Response
    {
        $this->denyAccessUnlessGranted('QuanLyKhachHangAccess', null, 'Bạn không có quyền truy cập trang này!');

        // Xử lý logic khi người dùng có quyền truy cập
        // ...

        return $this->render('your_template/danh_sach_khach_hang.html.twig');
    }

    /**
     * @Route("/danh_sach_thuoc", name="danh_sach_thuoc")
     */
    public function danhSachThuoc(): Response
    {
        $this->denyAccessUnlessGranted('BanThuocAccess', null, 'Bạn không có quyền truy cập trang này!');

        // Xử lý logic khi người dùng có quyền truy cập
        // ...

        return $this->render('your_template/danh_sach_thuoc.html.twig');
    }

    /**
     * @Route("/chi_tiet_thuoc", name="chi_tiet_thuoc")
     */
    public function chiTietThuoc(): Response
    {
        $this->denyAccessUnlessGranted('BanThuocAccess', null, 'Bạn không có quyền truy cập trang này!');

        // Xử lý logic khi người dùng có quyền truy cập
        // ...

        return $this->render('your_template/chi_tiet_thuoc.html.twig');
    }

    /**
     * @Route("/danh_sach_nha_san_xuat", name="danh_sach_nha_san_xuat")
     */
    public function danhSachNhaSanXuat(): Response
    {
        $this->denyAccessUnlessGranted('NhapHangAccess', null, 'Bạn không có quyền truy cập trang này!');

        // Xử lý logic khi người dùng có quyền truy cập
        // ...

        return $this->render('your_template/danh_sach_nha_san_xuat.html.twig');
    }

    // Tương tự cho các controller khác...

}
