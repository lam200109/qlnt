<?php

// src/Controller/DangnhapController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class DangnhapController extends AbstractController
{
    /**
     * @Route("/dangnhap", name="app_dangnhap")
     */
    public function index(Request $request, Connection $connection, SessionInterface $session): Response
    {
        $TaiKhoan = $MatKhau = "";
        $TaiKhoan_err = $MatKhau_err = "";

        if ($request->isMethod('POST')) {
            $TaiKhoan = $request->request->get('TaiKhoan');
            $MatKhau = $request->request->get('MatKhau');

            if (empty($TaiKhoan)) {
                $TaiKhoan_err = "Vui lòng nhập Tài Khoản.";
            }

            if (empty($MatKhau)) {
                $MatKhau_err = "Vui lòng nhập Mật Khẩu.";
            }

            if (empty($TaiKhoan_err) && empty($MatKhau_err)) {
                $sql = "SELECT IDBN, TaiKhoan, MatKhau, HoTen, DiaChi, role FROM BenhNhan WHERE TaiKhoan = '$TaiKhoan'";
                $stmt = $connection->executeQuery($sql);


                $result = $stmt->fetchAssociative();
                if ($result) {
                    if ($MatKhau == $result['MatKhau']) {
                        // Mật khẩu chính xác, lưu session và chuyển hướng đến trang chào mừng
                        $session = $request->getSession();
                        $session->set('loggedin', true);
                        $session->set('id', $result['IDBN']);
                        $session->set('TaiKhoan', $result['TaiKhoan']);
                        $session->set('role', $result['role']);

                        if ($result['role'] == 1) {
                            return $this->redirectToRoute('quan_ly');
                        } elseif ($result['role'] == 2) {
                            return $this->redirectToRoute('khach_hang');
                        } elseif ($result['role'] == 3) {
                            return $this->redirectToRoute('trang_chu');
                        }
                    } else {
                        $MatKhau_err = "Mật khẩu không chính xác.";
                    }
                } else {
                    $TaiKhoan_err = "Tài Khoản không tồn tại.";
                }
            }
        }

        return $this->render('dangnhap/index.html.twig', [
            'TaiKhoan' => $TaiKhoan,
            'MatKhau' => $MatKhau,
            'TaiKhoan_err' => $TaiKhoan_err,
            'MatKhau_err' => $MatKhau_err,
        ]);
    }
}
