<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class DangnhapController extends AbstractController
{
    
    private $authenticationUtils;

    public function __construct(AuthenticationUtils $authenticationUtils)
    {
        $this->authenticationUtils = $authenticationUtils;
    }

    #[Route('/dangnhap', name: 'dang_nhap', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $error = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();

        if ($error) {
            $this->addFlash('error', 'Đăng nhập không thành công. Vui lòng kiểm tra lại tài khoản và mật khẩu.');
        }

        return $this->render('dangnhap/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/them-khach-hang', name: 'them_khach_hang', methods: ['GET', 'POST'])]
    #[Security('is_granted("ROLE_BanThuocAccess")')]
    public function banThuoc(): Response
    {
        return $this->render('themkhachhang/index.html.twig');
    }

    #[Route('/danh-sach-khach-hang', name: 'danh_sach_khach_hang', methods: ['GET'])]
    #[Security('is_granted("ROLE_QuanLyKhachHangAccess")')]
    public function quanLyKhachHang(): Response
    {
        return $this->render('danhsachkhachhang/index.html.twig');
    }

    #[Route('/bao-cao-ban-hang', name: 'bao_cao_ban_hang', methods: ['GET'])]
    #[Security('is_granted("ROLE_BaoCaoTaiChinhAccess")')]
    public function baoCaoBanHang(): Response
    {
        return $this->render('baocaomuahang/index.html.twig');
    }

    #[Route('/bao-cao-ton-kho', name: 'bao_cao_ton_kho', methods: ['GET'])]
    #[Security('is_granted("ROLE_NhapHangAccess")')]
    public function nhapHang(): Response
    {
        return $this->render('baocaotonkho/index.html.twig');
    }

    // #[Route('/nhap-thuoc', name: 'nhap_thuoc', methods: ['GET'])]
    // #[Security('is_granted("ROLE_NhapHangAccess")')]
    // public function admin(): Response
    // {
    //     return $this->render('trangchu/index.html.twig');
    // }
}
