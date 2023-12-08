<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class DanhsachnhasanxuatController extends AbstractController
{
    /**
     * @Route("/danh-sach-nha-san-xuat", name="danh_sach_nha_san_xuat", methods={"GET", "POST"})
     */    
    public function index(Connection $connection, Request $request, SessionInterface $session): Response
    {
      

         // Check if the form is submitted
         if ($request->isMethod('POST')) {
            try {
                $this->processFormData($request, $connection, $session);
                return $this->redirectToRoute('danh_sach_nha_san_xuat');
            } catch (\Exception $e) {
                return $this->render('error.html.twig', ['error' => $e->getMessage()]);
            }
        }

        // Hiển thị form nếu là yêu cầu GET

        $sql = "SELECT * FROM Distributors";
        $result = $connection->executeQuery($sql)->fetchAllAssociative();

        return $this->render('danhsachnhasanxuat/index.html.twig', [
            'controller_name' => 'DanhsachnhasanxuatController',
            'result' => $result,
        ]);
    }

    /**
     * Xử lý dữ liệu từ form.
     *
     * @param Request $request
     * @param Connection $connection
     * @param SessionInterface $session
     */
    private function processFormData(Request $request, Connection $connection, SessionInterface $session): void
    {
        // Lấy dữ liệu từ form
        $distributorName = $request->request->get('DistributorName');
        $address = $request->request->get('Address');
        $phone = $request->request->get('Phone');
        $fax = $request->request->get('Fax');
        $email = $request->request->get('Email');
        $note = $request->request->get('Note');

        // Thực hiện truy vấn SQL để thêm nhà sản xuất
        $sql = "INSERT INTO Distributors (DistributorName, Address, Phone, Fax, Email, Note) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $params = [$distributorName, $address, $phone, $fax, $email, $note];
        $connection->executeQuery($sql, $params);

        // Thêm Flash message để hiển thị thông báo thành công (cần được hiển thị trong template Twig)
        $this->addFlash('success', 'Thêm nhà phân phối thành công');
    }
}

