<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User; // Kiểm tra xem đây có phải là đường dẫn đúng của class User không
use App\Entity\Role;
use Doctrine\DBAL\Connection;

class TrangchuController extends AbstractController
{
    #[Route('/trang-chu', name: 'trangchu')]
    public function index(UserInterface $user, Connection $connection): Response
    {
        // Tính toán tổng tiền của tuần hiện tại và tuần trước
        $currentWeekTotal = $this->calculateTotalForCurrentWeek($connection);
        $lastWeekTotal = $this->calculateTotalForLastWeek($connection);
        $changePercentage = ($lastWeekTotal > 0) ? (($currentWeekTotal - $lastWeekTotal) / $lastWeekTotal) * 100 : null;

        // Tính toán tổng tiền của tháng hiện tại và tháng trước
        $currentMonthTotal = $this->calculateTotalForCurrentMonth($connection);
        $lastMonthTotal = $this->calculateTotalForLastMonth($connection);
        $monthChangePercentage = ($lastMonthTotal > 0) ? (($currentMonthTotal - $lastMonthTotal) / $lastMonthTotal) * 100 : null;
        

        // Tính toán tổng chi phí của ngày hiện tại và tuần trước
        $todayExpense = $this->calculateTodayExpense($connection);
        $lastWeekExpense = $this->calculateLastWeekExpense($connection);
        $expenseChangePercentage = ($lastWeekExpense > 0) ? (($todayExpense - $lastWeekExpense) / $lastWeekExpense) * 100 : null;


        $sql = 'SELECT COUNT(*) AS TongSoDonHang FROM SalesInvoices';
        $result = $connection->executeQuery($sql);
        $data = $result->fetchAssociative();
        $tongSoDonHang = $data['TongSoDonHang'];

        $sql = 'SELECT COUNT(*) AS TongSoKhachHang FROM Customers';
        $result = $connection->executeQuery($sql);
        $data = $result->fetchAssociative();
        $tongSoKhachHang = $data['TongSoKhachHang'];

        $sql = 'SELECT COUNT(*) AS TongSoThuoc FROM Medicines';
        $result = $connection->executeQuery($sql);
        $data = $result->fetchAssociative();
        $tongSoThuoc = $data['TongSoThuoc'];

        $sql = 'SELECT COUNT(DISTINCT category) AS TongDanhMuc FROM Medicines';
        $result = $connection->executeQuery($sql);
        $data = $result->fetchAssociative();
        $tongDanhMuc = $data['TongDanhMuc'];

        $sql = 'SELECT Name, Email FROM Customers';
        $result = $connection->executeQuery($sql);
        $customerInfo = $result->fetchAllAssociative();



        $sql = '
        SELECT
            s.SalesInvoiceID AS invoiceNo,
            c.Name AS customer,
            s.Date AS date,
            sid.MedicineID AS ref,
            sid.Quantity * sid.Price AS amount
        FROM
            SalesInvoices s
            JOIN Customers c ON s.CustomerId = c.CustomerID
            JOIN SalesInvoiceDetails sid ON s.SalesInvoiceID = sid.SalesInvoiceId;
    ';
    $result = $connection->executeQuery($sql);
    $invoices = $result->fetchAllAssociative();


     // Thực hiện truy vấn SQL để lấy tổng số lượng và tổng doanh thu từ đầu tuần đến thời điểm hiện tại
     $sql = '
     SELECT
         SUM(Amount) AS totalAmount
     FROM
         SalesInvoices
     WHERE
         Date >= CURDATE() - INTERVAL (WEEKDAY(CURDATE()) + 1) DAY
         AND Date <= CURDATE();
 ';
    $result = $connection->executeQuery($sql);
    $data = $result->fetchAssociative();

    // Thực hiện truy vấn SQL để lấy tổng số lượng và tổng doanh thu từ đầu tuần trước đến hết tuần trước
    $lastWeekSql = '
    SELECT
    SUM(Amount) AS lastWeekAmount
FROM
    SalesInvoices
WHERE
    Date >= (CURDATE() - INTERVAL (WEEKDAY(CURDATE()) + 1) - 7 DAY)
    AND Date <= (CURDATE() - INTERVAL 7 DAY);

    ';
    $lastWeekResult = $connection->executeQuery($lastWeekSql);
    $lastWeekData = $lastWeekResult->fetchAssociative();

    // Tính toán phần trăm thay đổi so với tuần trước
    $percentChange = 0;
    if ($lastWeekData['lastWeekAmount'] > 0) {
        $percentChange = (($data['totalAmount'] - $lastWeekData['lastWeekAmount']) / $lastWeekData['lastWeekAmount']) * 100;
    }

    $doanhThuQuery = "
    SELECT
        WEEK(Date) AS WeekNumber,
        COALESCE(SUM(Amount), 0) AS TotalIncome
    FROM
        SalesInvoices
    WHERE
        YEAR(Date) = YEAR(CURRENT_DATE())
    GROUP BY
        WeekNumber
    ORDER BY
        WeekNumber ASC
";

$chiPhiQuery = "
    SELECT
        WEEK(Date) AS WeekNumber,
        COALESCE(SUM(Amount), 0) AS TotalExpense
    FROM
        PurchaseInvoices
    WHERE
        YEAR(Date) = YEAR(CURRENT_DATE())
    GROUP BY
        WeekNumber
    ORDER BY
        WeekNumber ASC
";


$doanhThuData = $connection->executeQuery($doanhThuQuery)->fetchAllAssociative();
$chiPhiData = $connection->executeQuery($chiPhiQuery)->fetchAllAssociative();



        return $this->render('trangchu/index.html.twig', [
            'currentWeekTotal' => $currentWeekTotal,
            'lastWeekTotal' => $lastWeekTotal,
            'changePercentage' => $changePercentage,
            'currentMonthTotal' => $currentMonthTotal,
            'lastMonthTotal' => $lastMonthTotal,
            'monthChangePercentage' => $monthChangePercentage,
            'todayExpense' => $todayExpense,
            'lastWeekExpense' => $lastWeekExpense,
            'expenseChangePercentage' => $expenseChangePercentage,
            'tongSoDonHang' => $tongSoDonHang,
            'tongSoKhachHang' => $tongSoKhachHang,
            'tongSoThuoc' => $tongSoThuoc,
            'tongDanhMuc' => $tongDanhMuc,
            'customerInfo' => $customerInfo,
            'invoices' => $invoices,
            'totalAmount' => $data['totalAmount'],
            'percentChange' => $percentChange,
            'doanhThuData' => $doanhThuData,
            'chiPhiData' => $chiPhiData,
        ]);
    }

    // Hàm tính tổng tiền của tuần hiện tại
    private function calculateTotalForCurrentWeek(Connection $connection): float
    {
        // Lấy ngày đầu tiên của tuần hiện tại
        $currentWeekStart = new \DateTime('monday this week');

        // Lấy ngày cuối cùng của tuần hiện tại
        $currentWeekEnd = new \DateTime('sunday this week');

        // Thực hiện truy vấn SQL để lấy tổng tiền của tuần hiện tại
        $sql = "SELECT SUM(SalesInvoices.Amount) AS total
                FROM SalesInvoices
                WHERE SalesInvoices.Date BETWEEN :start AND :end";
        
        $params = [
            'start' => $currentWeekStart->format('Y-m-d'),
            'end' => $currentWeekEnd->format('Y-m-d'),
        ];

        $result = $connection->executeQuery($sql, $params)->fetchAssociative();

        return (float) $result['total'];
    }

    // Hàm tính tổng tiền của tuần trước
    private function calculateTotalForLastWeek(Connection $connection): float
    {
        // Lấy ngày đầu tiên của tuần trước
        $lastWeekStart = new \DateTime('monday last week');

        // Lấy ngày cuối cùng của tuần trước
        $lastWeekEnd = new \DateTime('sunday last week');

        // Thực hiện truy vấn SQL để lấy tổng tiền của tuần trước
        $sql = "SELECT SUM(SalesInvoices.Amount) AS total
                FROM SalesInvoices
                WHERE SalesInvoices.Date BETWEEN :start AND :end";
        
        $params = [
            'start' => $lastWeekStart->format('Y-m-d'),
            'end' => $lastWeekEnd->format('Y-m-d'),
        ];

        $result = $connection->executeQuery($sql, $params)->fetchAssociative();

        return (float) $result['total'];
    }

    // Hàm tính tổng tiền của tháng hiện tại
    private function calculateTotalForCurrentMonth(Connection $connection): float
    {
        // Lấy ngày đầu tiên của tháng hiện tại
        $currentMonthStart = new \DateTime('first day of this month');

        // Lấy ngày cuối cùng của tháng hiện tại
        $currentMonthEnd = new \DateTime('last day of this month');

        // Thực hiện truy vấn SQL để lấy tổng tiền của tháng hiện tại
        $sql = "SELECT SUM(SalesInvoices.Amount) AS total
                FROM SalesInvoices
                WHERE SalesInvoices.Date BETWEEN :start AND :end";
        
        $params = [
            'start' => $currentMonthStart->format('Y-m-d'),
            'end' => $currentMonthEnd->format('Y-m-d'),
        ];

        $result = $connection->executeQuery($sql, $params)->fetchAssociative();

        return (float) $result['total'];
    }

    // Hàm tính tổng tiền của tháng trước
    private function calculateTotalForLastMonth(Connection $connection): float
    {
        // Lấy ngày đầu tiên của tháng trước
        $lastMonthStart = new \DateTime('first day of last month');

        // Lấy ngày cuối cùng của tháng trước
        $lastMonthEnd = new \DateTime('last day of last month');

        // Thực hiện truy vấn SQL để lấy tổng tiền của tháng trước
        $sql = "SELECT SUM(SalesInvoices.Amount) AS total
                FROM SalesInvoices
                WHERE SalesInvoices.Date BETWEEN :start AND :end";
        
        $params = [
            'start' => $lastMonthStart->format('Y-m-d'),
            'end' => $lastMonthEnd->format('Y-m-d'),
        ];

        $result = $connection->executeQuery($sql, $params)->fetchAssociative();

        return (float) $result['total'];
    }

    // Hàm tính tổng chi phí của ngày hiện tại
    private function calculateTodayExpense(Connection $connection): float
    {
        // Lấy ngày hôm nay
        $today = new \DateTime();

        // Thực hiện truy vấn SQL để lấy tổng chi phí của ngày hiện tại
        $sql = "SELECT SUM(PurchaseInvoices.Expense) AS total
                FROM PurchaseInvoices
                WHERE DATE(PurchaseInvoices.Date) = :today";
        
        $params = [
            'today' => $today->format('Y-m-d'),
        ];

        $result = $connection->executeQuery($sql, $params)->fetchAssociative();

        return (float) $result['total'];
    }

    // Hàm tính tổng chi phí của tuần trước
    private function calculateLastWeekExpense(Connection $connection): float
    {
        // Lấy ngày đầu tiên của tuần trước
        $lastWeekStart = new \DateTime('monday last week');

        // Lấy ngày cuối cùng của tuần trước
        $lastWeekEnd = new \DateTime('sunday last week');

        // Thực hiện truy vấn SQL để lấy tổng chi phí của tuần trước
        $sql = "SELECT SUM(PurchaseInvoices.Expense) AS total
                FROM PurchaseInvoices
                WHERE PurchaseInvoices.Date BETWEEN :start AND :end";
        
        $params = [
            'start' => $lastWeekStart->format('Y-m-d'),
            'end' => $lastWeekEnd->format('Y-m-d'),
        ];

        $result = $connection->executeQuery($sql, $params)->fetchAssociative();

        return (float) $result['total'];
    }








    
    /**
     * Lấy danh sách PermissionName từ người dùng.
     *
     * @param UserInterface $user
     * @return array
     */
    private function getUserPermissions(UserInterface $user): array
{
    $permissions = [];

    // Lặp qua từng vai trò của người dùng
    foreach ($user->getRoles() as $role) {
        // Kiểm tra xem $role có phải là đối tượng Role hay không
        if ($role instanceof Role) {
            // Lấy tất cả quyền của vai trò
            $rolePermissions = $role->getPermissions();

            // Thêm các quyền vào mảng permissions
            foreach ($rolePermissions as $permission) {
                $permissions[] = $permission->getPermissionName();
            }
        }
    }

    return $permissions;
}

}

