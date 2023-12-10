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
        $sqlPermissions = "SELECT * FROM  Permissions";
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
         Users.UserID,
         Users.FullName,
         Users.CreatedDate,
         FORMAT(RolePermissions.Salary, 0) AS Salary,
         COUNT(DISTINCT Attendance.Date) AS TotalDays,
         IFNULL(Salary.PaymentStatus, 0) AS PaymentStatus,
         Salary.SalaryID,
         Salary.Date AS PaymentDate
     FROM Users
     JOIN UserRoles ON Users.UserID = UserRoles.UserID
     JOIN RolePermissions ON UserRoles.RoleID = RolePermissions.RoleID
     LEFT JOIN Attendance ON Users.UserID = Attendance.UserID
     LEFT JOIN Salary ON Users.UserID = Salary.UserID
     WHERE (:selectedMonth = 0 OR MONTH(Salary.Date) = :selectedMonth)
     GROUP BY Users.UserID, Users.FullName, Users.CreatedDate, RolePermissions.Salary, Salary.PaymentStatus, Salary.SalaryID, Salary.Date
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
    
        // Kiểm tra xem SalaryID có tồn tại không
        if ($SalaryID !== null) {
            // Thực hiện cập nhật trạng thái thanh toán trong bảng Salary
            $sql = "UPDATE Salary SET PaymentStatus = 1, Date = CURRENT_DATE WHERE SalaryID = :SalaryID";
            $params = ['SalaryID' => $SalaryID];
    
            // Thực hiện câu lệnh SQL và kiểm tra kết quả
            $result = $connection->executeStatement($sql, $params);
    
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
      
        $permissionID = $request->request->get('PermissionName');
    $newSalary = $request->request->get('Salary');

    // Thực hiện cập nhật lương dựa trên PermissionID
    $sql = "UPDATE RolePermissions SET Salary = :newSalary WHERE PermissionID = :permissionID";
    $params = ['newSalary' => $newSalary, 'permissionID' => $permissionID];

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
        $permissionID = $request->request->get('PermissionID');
    
        // Thực hiện câu truy vấn để lấy lương dựa trên PermissionID
        $sql = "SELECT Salary FROM RolePermissions WHERE PermissionID = :permissionID";
        $params = ['permissionID' => $permissionID];
        $result = $connection->executeQuery($sql, $params)->fetchAssociative();
    
        $salary = $result ? number_format($result['Salary'], 0, ',', '.') : null;
    
        // Trả về dữ liệu lương dưới dạng JSON
        return $this->json(['salary' => $salary]);
    }
    
    
    
}
