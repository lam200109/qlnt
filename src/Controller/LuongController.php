<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class LuongController extends AbstractController
{
    #[Route('/luong', name: 'luong')]
    public function index(Connection $connection, Request $request): Response
    {
        // Đoạn SQL cần thực hiện
        $sqlQuery = "
        SELECT
        Users.UserID,
        Users.FullName,
        Users.CreatedDate,
        FORMAT(RolePermissions.Salary, 0) AS 'Salary',
        COUNT(DISTINCT Attendance.Date) AS 'TotalDays',
        IFNULL(Salary.PaymentStatus, 0) AS 'PaymentStatus',
        Salary.SalaryID  -- Add SalaryID to the SELECT statement
    FROM Users
    JOIN UserRoles ON Users.UserID = UserRoles.UserID
    JOIN RolePermissions ON UserRoles.RoleID = RolePermissions.RoleID
    LEFT JOIN Attendance ON Users.UserID = Attendance.UserID
    LEFT JOIN Salary ON Users.UserID = Salary.UserID
    GROUP BY Users.UserID, Users.FullName, Users.CreatedDate, RolePermissions.Salary, Salary.PaymentStatus, Salary.SalaryID;
    
        ";
        $sqlPermissions = "SELECT * FROM  Users";
        // Thực hiện truy vấn SQL
        $result = $connection->executeQuery($sqlQuery)->fetchAllAssociative();
        $permissionname = $connection->executeQuery($sqlPermissions)->fetchAllAssociative();



        // Lấy giá trị tháng từ tham số truy vấn
        $selectedMonth = $request->query->get('selectedMonth');

        // Nếu không có giá trị tháng được chọn, mặc định là 0 để lọc toàn bộ dữ liệu
        $selectedMonth = $selectedMonth ?: 0;

        // Thực hiện truy vấn với điều kiện lọc theo tháng đã chọn
        $sql = '
 SELECT
 U.UserID,
 U.FullName,
 U.CreatedDate,
 COUNT(DISTINCT A.Date) AS TotalDays,
 IFNULL(S.TotalEarnings, 0) AS TotalEarnings,
 IFNULL(S.PaymentStatus, 0) AS PaymentStatus,
 S.SalaryID,
 S.Date AS PaymentDate,
 IFNULL(S.TotalEarnings * COUNT(DISTINCT A.Date), 0) AS TotalPayment
FROM
 Users U
JOIN
 Attendance A ON U.UserID = A.UserID
LEFT JOIN
 Salary S ON U.UserID = S.UserID
LEFT JOIN
 UserRoles UR ON U.UserID = UR.UserID   
LEFT JOIN
 RolePermissions RP ON UR.RoleID = RP.RoleID
WHERE
 (:selectedMonth = 0 OR MONTH(S.Date) = :selectedMonth)
GROUP BY
 U.UserID, U.FullName, U.CreatedDate, S.TotalEarnings, S.PaymentStatus, S.SalaryID, S.Date;

';

        $params = ['selectedMonth' => $selectedMonth];
        $types = ['selectedMonth' => \PDO::PARAM_INT];
        $results = $connection->executeQuery($sql, $params, $types)->fetchAllAssociative();









        // Trả kết quả cho giao diện người dùng
        return $this->render('luong/index.html.twig', [
            'result' => $result,
            'permissionname' => $permissionname,
            'results' => $results,


        ]);
    }

    #[Route('/update-payment-status/{SalaryID}', name: 'update_payment_status', methods: ['POST'])]
    public function updatePaymentStatus(Connection $connection, Request $request): Response
    {
        // Lấy giá trị SalaryID từ yêu cầu POST
        $data = json_decode($request->getContent(), true);
        $SalaryID = $data['SalaryID'] ?? null;
        $totalPayment = $data['totalPayment'] ?? null;
        // Lấy ngày hiện tại
        $currentDate = new \DateTime();
        $formattedDate = $currentDate->format('Y-m-d');
        // Kiểm tra xem SalaryID có tồn tại không
        if ($SalaryID !== null) {
            // Thực hiện cập nhật trạng thái thanh toán trong bảng Salary
            $sql = "UPDATE Salary SET PaymentStatus = 1, Date = CURRENT_DATE WHERE SalaryID = :SalaryID";
            $params = ['SalaryID' => $SalaryID];

            // Thực hiện câu lệnh SQL và kiểm tra kết quả
            $result = $connection->executeStatement($sql, $params);


            $sql1 = "INSERT INTO PurchaseInvoices (Amount, Date, ExpenseType) VALUES (:totalPayment, :date, :expenseType)";
            $params = [
                'totalPayment' => $totalPayment, // Giả sử bạn có giá trị totalPayment để chèn vào Amount
                'date' => date('Y-m-d'), // Lấy ngày hiện tại
                'expenseType' => 'Trả lương nhân viên', // Giả sử bạn muốn đặt ExpenseType là 'Trả lương nhân viên'
            ];

            $result1 = $connection->executeStatement($sql1, $params);








            if ($result !== false) {
                // Trả về kết quả dưới dạng JSON
                return $this->json(['success' => true]);
            }
        }

        // Trả về kết quả khi có lỗi hoặc SalaryID không tồn tại
        return $this->json(['success' => false, 'error' => 'Invalid SalaryID or failed to update.']);
    }





    #[Route('/update-luong', name: 'update_luong', methods: ['POST'])]
    #[Security('is_granted("AdminAccess")')]
    public function updateLuong(Connection $connection, Request $request): Response
    {

        $newSalary = $request->request->get('Salary');
        $UserID = $request->request->get('UserID');

        // Thực hiện cập nhật lương dựa trên PermissionID
        $sql = "UPDATE Salary SET TotalEarnings = :newSalary WHERE UserID = :UserID";
        $params = ['newSalary' => $newSalary, 'UserID' => $UserID];

        $result = $connection->executeStatement($sql, $params);

        if ($result !== false) {
            // Nếu cập nhật thành công, sử dụng addFlash để hiển thị thông báo
            $this->addFlash('success', 'Lương đã được cập nhật thành công.');
        } else {
            // Nếu có lỗi, sử dụng addFlash để hiển thị thông báo lỗi
            $this->addFlash('error', 'Có lỗi khi cập nhật lương.');
        }

        // Chuyển hướng hoặc hiển thị trang tương ứng
        return $this->redirectToRoute('luong');
    }



    #[Route('/get-salary', name: 'get_salary', methods: ['POST'])]
    public function getSalary(Connection $connection, Request $request): JsonResponse
    {
        $UserID = $request->request->get('UserID');

        // Thực hiện câu truy vấn để lấy lương dựa trên PermissionID
        $sql = "SELECT TotalEarnings FROM Salary WHERE UserID = :UserID";
        $params = ['UserID' => $UserID];
        $result = $connection->executeQuery($sql, $params)->fetchAssociative();

        $salary = $result ? $result['TotalEarnings'] : null;

        // Trả về dữ liệu lương dưới dạng JSON
        return $this->json(['salary' => $salary]);
    }











    #[Route('/add-luong', name: 'add_luong', methods: ['POST'])]
    #[Security('is_granted("AdminAccess")')]
    public function addLuong(Connection $connection, Request $request): Response
    {
        $totalEarning = $request->request->get('totalEarning');
        $userID = $request->request->get('UserID');

        // Kiểm tra xem có giá trị hay không
        if ($totalEarning !== null && $userID !== null) {
            // Thực hiện câu lệnh SQL để chèn dữ liệu vào bảng Salary
            $sql = "INSERT INTO Salary (TotalEarnings, UserID) VALUES (:totalEarning, :UserID)";
            $params = ['totalEarning' => $totalEarning, 'UserID' => $userID];

            // Thực hiện câu lệnh SQL và kiểm tra kết quả
            $result = $connection->executeStatement($sql, $params);

            if ($result !== false) {
                // Nếu cập nhật thành công, sử dụng addFlash để hiển thị thông báo
                $this->addFlash('success', 'Lương đã được cập nhật thành công.');
            } else {
                // Nếu có lỗi, sử dụng addFlash để hiển thị thông báo lỗi
                $this->addFlash('error', 'Có lỗi khi cập nhật lương.');
            }
        } else {
            // Nếu không có giá trị, hiển thị thông báo lỗi
            $this->addFlash('error', 'Dữ liệu không hợp lệ.');
        }

        // Chuyển hướng hoặc hiển thị trang tương ứng
        return $this->redirectToRoute('luong');
    }
}
