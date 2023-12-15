<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class DondathangController extends AbstractController
{

    private $dbalConnection;
    public function __construct(Connection $dbalConnection)
    {
        $this->dbalConnection = $dbalConnection;
    }

    #[Route('/dondathang', name: 'app_dondathang')]
    public function index(SessionInterface $session): Response
    {
       
    
        // Truy vấn để lấy thông tin cơ bản của khách hàng và đơn hàng
        $query = "
            SELECT c.*, si.*
            FROM customers c
            LEFT JOIN salesinvoices si ON c.CustomerID = si.CustomerID
          
        ";
    
        $result = $this->dbalConnection->executeQuery($query)->fetchAllAssociative();
    
        // Truy vấn để tính tổng số lượng đơn hàng
        $totalCountQuery = "
            SELECT COUNT(si.SalesInvoiceID) AS totalCount
            FROM salesinvoices si
        ";
    
        $totalCountResult = $this->dbalConnection->executeQuery($totalCountQuery)->fetchAssociative();
    
        $totalCount = $totalCountResult['totalCount'] ?? 0;
    
        return $this->render('dondathang/index.html.twig', [
            'controller_name' => 'DondathangController',
            'data' => $result,
            'totalCount' => $totalCount,
        ]);
    }


    #[Route('/chi-tiet-don-dat-hang/{id}', name: 'chi_tiet_don_dat_hang')]
    public function chitietdondathang($id, SessionInterface $session): Response
    {
        // Kiểm tra xem $customerId và $id có khớp không
        $customerId = $session->get('customer_id');
    
        // Thực hiện truy vấn để lấy thông tin chi tiết của khách hàng và đơn hàng
        $query = "
            SELECT c.*, si.*, sid.*, m.*
            FROM customers c
            LEFT JOIN salesinvoices si ON c.CustomerID = si.CustomerID
            LEFT JOIN salesinvoicedetails sid ON si.SalesInvoiceID = sid.SalesInvoiceID
            LEFT JOIN medicines m ON sid.MedicineID = m.MedicineID
            WHERE c.CustomerID = :customerId
        ";
    
        $query1 = "
            SELECT * FROM Customers WHERE CustomerID = :customerId
        ";
    
        $result = $this->dbalConnection->executeQuery($query, ['customerId' => $id])->fetchAllAssociative();
        $result1 = $this->dbalConnection->executeQuery($query1, ['customerId' => $id])->fetchAllAssociative();
    
        if (!$result) {
            throw $this->createNotFoundException('Không tìm thấy dữ liệu cho khách hàng có ID ' . $id);
        }
    
        return $this->render('chitietdondathang/index.html.twig', [
            'controller_name' => 'ChitietdondathangController',
            'data' => $result,
            'data1' => $result1,
        ]);
    }
    
    
}
