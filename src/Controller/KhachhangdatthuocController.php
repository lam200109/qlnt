<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;

class KhachhangdatthuocController extends AbstractController
{
    private $dbalConnection;

    public function __construct(Connection $dbalConnection)
    {
        $this->dbalConnection = $dbalConnection;
    }

    #[Route('/khachhangdatthuoc', name: 'app_khachhangdatthuoc')]
    public function index(SessionInterface $session): Response
    {
        $session->start();
        $customerId = $session->get('customer_id');
    
        // Truy vấn để lấy thông tin cơ bản của khách hàng và đơn hàng
        $query = "
            SELECT c.*, si.*
            FROM customers c
            LEFT JOIN salesinvoices si ON c.CustomerID = si.CustomerID
            WHERE c.CustomerID = :customerId
        ";
    
        $result = $this->dbalConnection->executeQuery($query, ['customerId' => $customerId])->fetchAllAssociative();
    
        if (!$result) {
            throw $this->createNotFoundException('Không tìm thấy dữ liệu cho khách hàng có ID ' . $customerId);
        }
    
        // Truy vấn để tính tổng số lượng đơn hàng
        $totalCountQuery = "
            SELECT COUNT(si.SalesInvoiceID) AS totalCount
            FROM salesinvoices si
            WHERE si.CustomerID = :customerId
        ";
    
        $totalCountResult = $this->dbalConnection->executeQuery($totalCountQuery, ['customerId' => $customerId])->fetchAssociative();
    
        $totalCount = $totalCountResult['totalCount'] ?? 0;
    
        return $this->render('khachhangdatthuoc/index.html.twig', [
            'controller_name' => 'KhachhangdatthuocController',
            'data' => $result,
            'totalCount' => $totalCount,
        ]);
    }
    

    #[Route('/khach-hang-dat-thuoc/{id}', name: 'app_chitietkhachhangdatthuoc')]
    public function chitietkhachhangdatthuoc($id, SessionInterface $session, Request $request): Response
    {
        $session->start();
    
        $customerId = $session->get('customer_id');
    
        $id = $request->get('id');
        // Thực hiện truy vấn để lấy thông tin chi tiết của khách hàng và đơn hàng
        $query = "
            SELECT c.*, si.*, sid.*, m.*
            FROM customers c
            LEFT JOIN salesinvoices si ON c.CustomerID = si.CustomerID
            LEFT JOIN salesinvoicedetails sid ON si.SalesInvoiceID = sid.SalesInvoiceID
            LEFT JOIN medicines m ON sid.MedicineID = m.MedicineID
            WHERE c.CustomerID = :customerId AND si.SalesInvoiceID = :id
        ";

        $query1 = "
            SELECT * FROM Customers WHERE CustomerID = :customerId
        ";
        $result = $this->dbalConnection->executeQuery($query, ['customerId' => $customerId, 'id' => $id])->fetchAllAssociative();

        $result1 = $this->dbalConnection->executeQuery($query1, ['customerId' => $customerId])->fetchAllAssociative();

        if (!$result) {
            throw $this->createNotFoundException('Không tìm thấy dữ liệu cho khách hàng có ID ' . $customerId);
        }
    
        return $this->render('chitietkhachhangdatthuoc/index.html.twig', [
            'controller_name' => 'KhachhangdatthuocController',
            'data' => $result,
            'data1' => $result1,

        ]);
    }
    
}
