<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;


class ChitiethoadonController extends AbstractController
{
   

    #[Route('/chi-tiet-hoa-don/{id}', name: 'app_chitiethoadon')]
    public function index($id, Connection $connection, Request $request): Response
    {
        // Lấy URL trước đó từ header Referer
        $referer = $request->headers->get('referer');

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
        } 
        // Nếu không phải cả hai trường hợp trên, có thể xác định một giá trị mặc định hoặc xử lý khác
        else {
            throw new \Exception('Không xác định được route trước đó.');
        }

        // Tạo câu truy vấn SQL
        $sql = "SELECT 
            $detailsTable.*, 
            $mainTable.*, 
            Medicines.*
        FROM 
            $detailsTable
        JOIN 
            $mainTable ON $detailsTable.$idColumn = $mainTable.$idColumn
        JOIN 
            Medicines ON $detailsTable.MedicineID = Medicines.MedicineID
        WHERE 
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
        ]);
    }

    






}
