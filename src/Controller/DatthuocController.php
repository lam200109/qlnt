<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class DatthuocController extends AbstractController
{
    private $connection;


    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }


    #[Route('/dat-thuoc', name: 'app_datthuoc')]
    public function index(Connection $connection): Response
    {
        $query = "SELECT MedicineID, Name, Image,Price, Category, Description FROM medicines";

        // Sử dụng executeQuery và fetchAll của PDO
        $medicines = $connection->executeQuery($query)->fetchAllAssociative(\PDO::FETCH_ASSOC);



        $sql = 'SELECT Category FROM Medicines';
        $result = $connection->executeQuery($sql);
        $category = $result->fetchAssociative();
        return $this->render('datthuoc/index.html.twig', [
            'controller_name' => 'DatthuocController',
            'medicines' => $medicines,
            'category' => $category,

        ]);
    }


    #[Route('/dat-thuoc-order', name: 'dat_thuoc_order', methods: ['POST'])]
    public function order(Request $request, SessionInterface $session): JsonResponse
    {
        $session->start();

        $customerId = $session->get('customer_id');

        // Lấy dữ liệu đặt hàng từ request
        $jsonData = $request->getContent();

        // Kiểm tra và giải mã JSON
        $data = json_decode($jsonData, true);

        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(['status' => 'error', 'message' => 'Lỗi khi giải mã JSON']);
        }

        // Lấy giá trị từ dữ liệu JSON
        $medicineId = $data['medicineId'] ?? null;
        $quantity = $data['quantity'] ?? null;

        // Chuyển đổi giá trị "price" thành số nguyên
        $price = isset($data['price']) ? (int) $data['price'] : null;

        // Kiểm tra null trước khi truy cập vào các phần tử
        if ($medicineId === null || $quantity === null || $price === null) {
            return new JsonResponse(['status' => 'error', 'message' => 'Dữ liệu không đầy đủ']);
        }



        // Tính toán tổng tiền cho đơn hàng
        $amount = $quantity * $price;

        // Kiểm tra nếu MaBN chưa tồn tại
        $query = "SELECT CustomerID FROM Customers WHERE CustomerID = ?";
        $IDBN = $this->connection->fetchOne($query, [$customerId]) ?? null;

        if ($IDBN) {
            // Thêm hóa đơn xuất vào CSDL
            $query = "INSERT INTO SalesInvoices (CustomerID, Date, Amount, Status) VALUES (?, NOW(), ?,'Chưa xác nhận')";
            $this->connection->executeStatement($query, [$IDBN, $amount]);

            // Lấy mã hóa đơn xuất vừa thêm vào
            $SalesInvoiceID = $this->connection->lastInsertId();

            // Kiểm tra số lượng tồn kho trước khi thêm chi tiết hóa đơn xuất
            $query = "SELECT InStock FROM Medicines WHERE MedicineID = ?";
            $InStock = $this->connection->fetchOne($query, [$medicineId]);

            if ($InStock >= $quantity) {
                // Thêm chi tiết hóa đơn xuất vào CSDL
                $query = "INSERT INTO SalesInvoiceDetails (SalesInvoiceID, MedicineID, Quantity, Price, Total, CreatedDate) VALUES (?, ?, ?, ?, ?, NOW())";
                $this->connection->executeStatement($query, [$SalesInvoiceID, $medicineId, $quantity, $price, $amount]);

                return new JsonResponse(['status' => 'success']);
            } else {
                return new JsonResponse(['status' => 'error', 'message' => 'Số lượng tồn kho không đủ']);
            }
        } else {
            return new JsonResponse(['status' => 'error', 'message' => 'Tạo đơn thất bại']);
        }
    }
}
