<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;

class DondathangController extends AbstractController
{

    private $dbalConnection;
    public function __construct(Connection $dbalConnection)
    {
        $this->dbalConnection = $dbalConnection;
    }

    #[Route('/don-dat-hang', name: 'app_dondathang')]
    public function index(SessionInterface $session): Response
    {


        // Truy vấn để lấy thông tin cơ bản của khách hàng và đơn hàng
        $query = "
        SELECT *
        FROM SalesInvoices si
        JOIN SalesInvoiceDetails sid ON si.SalesInvoiceID = sid.SalesInvoiceID
        JOIN Medicines m ON sid.MedicineID = m.MedicineID
        WHERE si.Status IS NOT NULL;
        
          
        ";


        // Truy vấn để tính tổng số lượng đơn hàng
        $totalCountQuery = "
            SELECT COUNT(si.SalesInvoiceID) AS totalCount
            FROM salesinvoices si
        ";
        $result = $this->dbalConnection->executeQuery($query)->fetchAllAssociative();

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

    /**
     * @Route("/xac-nhan-don-dat-hang", name="xac_nhan_don_dat_hang", methods={"POST"})
     */
    public function acceptDondathang(Connection $connection, Request $request): Response
    {

        $medicineID = $request->request->get('MedicineID');
        $salesInvoiceID = $request->request->get('SalesInvoiceID');
        $quantity = (int)$request->request->get('Quantity');

        $connection->executeQuery("UPDATE SalesInvoices SET Status='Đã xác nhận đơn' WHERE SalesInvoiceID = ?", [$salesInvoiceID]);

        // SELECT SoLuong FROM Thuoc WHERE idThuoc = $idThuoc
        $row = $connection->fetchOne("SELECT InStock FROM Medicines WHERE MedicineID = ?", [$medicineID]);
    
        if ($row !== false && $row >= $quantity) {
            // UPDATE Thuoc SET SoLuong = SoLuong - $SoLuongXuat WHERE idThuoc = $idThuoc
            $connection->executeQuery("UPDATE Medicines SET InStock = InStock - ? WHERE MedicineID = ?", [$quantity, $medicineID]);
            $this->addFlash('success', 'Đã xác nhận đơn hàng thành công.');

            return $this->redirectToRoute('don_dat_hang'); // Thay 'success_route' bằng tên route bạn muốn chuyển hướng đến khi thành công.
        } else {
            $this->addFlash('error', 'Không thể xác nhận đơn hàng. Số lượng tồn kho không đủ.');

            return $this->redirectToRoute('don_dat_hang'); // Thay 'failure_route' bằng tên route bạn muốn chuyển hướng đến khi có lỗi.
        }



        return $this->redirectToRoute('don_dat_hang');
    }
}
