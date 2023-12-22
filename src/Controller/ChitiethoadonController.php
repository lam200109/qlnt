<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;


class ChitiethoadonController extends AbstractController
{
   

    #[Route('/chi-tiet-hoa-don/{id}', name: 'chitiethoadon')]
    public function index($id, Connection $connection, Request $request): Response
{
    // Lấy URL trước đó từ header Referer
    $referer = $request->headers->get('referer');
    
    // Khai báo các biến mặc định
    $idColumn = '';
    $mainTable = '';
    $detailsTable = '';
    $additionalTables = ''; // Chuỗi thêm vào câu truy vấn cho bảng bổ sung
    $isChiPhi = false; // Mặc định là false

    // Kiểm tra nếu URL chứa "/nguon-thu" thì route trước đó là "/nguon-thu"
    if (strpos($referer, '/nguon-thu') !== false) {
        $idColumn = 'SalesInvoiceID';
        $mainTable = 'SalesInvoices';
        $detailsTable = 'SalesInvoiceDetails';
    } 
    // Kiểm tra nếu URL chứa "/chi-phi" thì route trước đó là "/chi-phi"
    elseif (strpos($referer, '/chi-phi') !== false) {
        $idColumn = 'PurchaseInvoiceID';
        $mainTable = 'PurchaseInvoices';
        $detailsTable = 'PurchaseInvoiceDetails';
        $additionalTables = ", Distributors.*"; // Chọn tất cả các cột từ bảng Distributors
        $isChiPhi = true; // Đánh dấu là chi phí

    } 
    // Nếu không phải cả hai trường hợp trên, có thể xác định một giá trị mặc định hoặc xử lý khác
    else {
        throw new \Exception('Không xác định được route trước đó.');
    }

    // Tạo câu truy vấn SQL
    $sql = "SELECT 
        $detailsTable.*, 
        $mainTable.*, 
        Medicines.*";
    
    // Thêm JOIN cho bảng Customers khi route là "/nguon-thu"
    if (strpos($referer, '/nguon-thu') !== false) {
        $sql .= ", Customers.`Name` as CustomerName, Customers.Address, Customers.Phone";
    }

    $sql .= $additionalTables; // Thêm các bảng bổ sung

    $sql .= " FROM 
        $detailsTable
    JOIN 
        $mainTable ON $detailsTable.$idColumn = $mainTable.$idColumn
    JOIN 
        Medicines ON $detailsTable.MedicineID = Medicines.MedicineID";

    // Thêm JOIN cho bảng Customers khi route là "/nguon-thu"
    if (strpos($referer, '/nguon-thu') !== false) {
        $sql .= " JOIN Customers ON $mainTable.CustomerID = Customers.CustomerID";
    }

    // Thêm JOIN cho bảng Distributors khi route là "/chi-phi"
    if (strpos($referer, '/chi-phi') !== false) {
        $sql .= " LEFT JOIN Distributors ON $mainTable.DistributorID = Distributors.DistributorID";
    }
    
    $sql .= " WHERE 
        $mainTable.$idColumn = :id";

    // Thực hiện truy vấn
    $invoices = $connection->executeQuery($sql, ['id' => $id])->fetchAllAssociative();
    // Kiểm tra nếu không có hoá đơn với ID tương ứng
    if (!$invoices) {
        throw $this->createNotFoundException('Không tìm thấy hoá đơn với ID ' . $id);
    }

    // Truyền dữ liệu vào view
    return $this->render('chitiethoadon/index.html.twig', [
        'result' => $invoices,
        'sqlQuery' => $sql,
        'invoiceId' => $id,
        'isChiPhi' => $isChiPhi, // Truyền biến này vào template

    ]);
}
}






