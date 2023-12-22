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
        // Tính toán tổng chi phí của tuần hiện tại và tuần trước

        $chiphituanhientai = $this->chiphituanhientai($connection);
        $chiphituantruoc = $this->chiphituantruoc($connection);
        $phantram = ($chiphituantruoc > 0) ? (($chiphituanhientai - $chiphituantruoc) / $chiphituantruoc) * 100 : null;

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

        $sql = 'SELECT Name, Email
        FROM Customers
        WHERE username IS NOT NULL AND password IS NOT NULL
        ORDER BY CustomerID DESC
        LIMIT 5;
        
        ';
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
                JOIN SalesInvoiceDetails sid ON s.SalesInvoiceID = sid.SalesInvoiceId
            ORDER BY s.Date DESC
            LIMIT 5;
            
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

        $sql = '
                SELECT
                m.Name AS medicine_name,
                ROUND((SUM(sid.Quantity) / (
                    SELECT SUM(sid2.Quantity) 
                    FROM SalesInvoiceDetails sid2 
                    WHERE sid2.SalesInvoiceId IS NOT NULL
                )) * 100, 2) AS sales_percentage
                FROM
                Medicines m
                LEFT JOIN
                SalesInvoiceDetails sid ON m.MedicineID = sid.MedicineID
                WHERE
                sid.SalesInvoiceId IS NOT NULL
                GROUP BY
                m.MedicineID
                ORDER BY
                sales_percentage DESC
                LIMIT 3
                ';

        $statement = $connection->executeQuery($sql);
        $top3SellingMedicines = $statement->fetchAllAssociative();



        // Lấy ngày hôm nay và ngày hôm qua
        $todayDate = new \DateTime('now');
        $yesterdayDate = (new \DateTime('now'))->sub(new \DateInterval('P1D')); // Ngày hôm qua
        $todayDateString = $todayDate->format('Y-m-d');
        $yesterdayDateString = $yesterdayDate->format('Y-m-d');

        // Truy vấn CSDL để lấy tổng doanh số bán hàng của ngày hôm nay
        $queryToday = "SELECT SUM(Amount) AS totalSales 
                  FROM SalesInvoices 
                  WHERE Date = :todayDate";

        $resultToday = $connection->executeQuery($queryToday, ['todayDate' => $todayDateString]);
        $todaySalesTotal = $resultToday->fetchOne();

        // Truy vấn CSDL để lấy tổng doanh số bán hàng của ngày hôm qua
        $queryYesterday = "SELECT SUM(Amount) AS totalSales 
                      FROM SalesInvoices 
                      WHERE Date = :yesterdayDate";

        $resultYesterday = $connection->executeQuery($queryYesterday, ['yesterdayDate' => $yesterdayDateString]);
        $yesterdaySalesTotal = $resultYesterday->fetchOne();

        // Tính phần trăm thay đổi so với ngày hôm qua
        $percentageChange = 0;
        if ($yesterdaySalesTotal != 0) {
            $percentageChange = ($todaySalesTotal - $yesterdaySalesTotal) / abs($yesterdaySalesTotal) * 100;
        } elseif ($todaySalesTotal != 0) {
            $percentageChange = 100; // Nếu hôm nay có doanh số và hôm qua không có, tăng 100%
        }


        $queryToday = "SELECT SUM(Amount) AS todayExpense 
   FROM PurchaseInvoices 
   WHERE Date = :todayDate";

        $resultToday = $connection->executeQuery($queryToday, ['todayDate' => $todayDateString]);
        $todayExpense = $resultToday->fetchOne();

        // Truy vấn CSDL để lấy tổng chi phí của ngày hôm qua
        $queryYesterday = "SELECT SUM(Amount) AS yesterdayExpense 
FROM PurchaseInvoices 
WHERE Date = :yesterdayDate";

        $resultYesterday = $connection->executeQuery($queryYesterday, ['yesterdayDate' => $yesterdayDateString]);
        $yesterdayExpense = $resultYesterday->fetchOne();

        // Tính phần trăm thay đổi so với ngày hôm qua
        $percentageChangeExpense = 0;
        if ($yesterdayExpense != 0) {
            $percentageChangeExpense = ($todayExpense - $yesterdayExpense) / abs($yesterdayExpense) * 100;
        } elseif ($todayExpense != 0) {
            $percentageChangeExpense = 100; // Nếu hôm nay có chi phí và hôm qua không có, tăng 100%
        }




        $currentMonth = date('Y-m');
        $query = "SELECT SUM(Amount) AS totalExpense
                  FROM PurchaseInvoices
                  WHERE DATE_FORMAT(Date, '%Y-%m') = :currentMonth";

        $currentMonthExpense = $connection->executeQuery($query, ['currentMonth' => $currentMonth])->fetchOne() ?? 0;

        // Lấy tổng chi phí của tháng trước
        $lastMonth = date('Y-m', strtotime('last month'));
        $query = "SELECT SUM(Amount) AS totalExpense
                  FROM PurchaseInvoices
                  WHERE DATE_FORMAT(Date, '%Y-%m') = :lastMonth";

        $lastMonthExpense = $connection->executeQuery($query, ['lastMonth' => $lastMonth])->fetchOne() ?? 0;

        // Tính phần trăm thay đổi
        $percentageChangeExpense = 0;
        if ($lastMonthExpense != 0) {
            $percentageChangeExpense = (($currentMonthExpense - $lastMonthExpense) / abs($lastMonthExpense)) * 100;
        } elseif ($currentMonthExpense != 0) {
            $percentageChangeExpense = 100;
        }


        $lastMonth = date('Y-m', strtotime('last month'));

        // Truy vấn dữ liệu từ bảng PurchaseInvoices cho tháng trước
        $purchaseInvoicesQuery = "SELECT SUM(Amount) AS totalExpense
                                  FROM PurchaseInvoices
                                  WHERE DATE_FORMAT(Date, '%Y-%m') = :lastMonth";
        $lastMonthExpense = $connection->executeQuery($purchaseInvoicesQuery, ['lastMonth' => $lastMonth])->fetchOne() ?? 0;

        // Truy vấn dữ liệu từ bảng SalesInvoices cho tháng trước
        $salesInvoicesQuery = "SELECT SUM(Amount) AS totalIncome
                               FROM SalesInvoices
                               WHERE DATE_FORMAT(Date, '%Y-%m') = :lastMonth";
        $lastMonthIncome = $connection->executeQuery($salesInvoicesQuery, ['lastMonth' => $lastMonth])->fetchOne() ?? 0;









        $sql = "
    SELECT 
    SalesInvoices.*,
    Customers.`Name` as CustomerName,
    Customers.Address,
    Customers.Email,
    Customers.Phone
FROM 
    SalesInvoices
JOIN 
    Customers ON SalesInvoices.CustomerID = Customers.CustomerID
WHERE 
    SalesInvoices.Status = 'Đã xác nhận đơn';

    LIMIT 5;
";

        // Thực hiện truy vấn
        $online = $connection->executeQuery($sql);
        $onlines = $online->fetchAllAssociative();





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
            'top3SellingMedicines' => $top3SellingMedicines,
            'todaySalesTotal' => $todaySalesTotal,
            'percentageChange' => $percentageChange,
            'todayExpense' => $todayExpense,
            'percentageChangeExpense' => $percentageChangeExpense,
            'currentMonthExpense' => $currentMonthExpense,
            'percentageChangeExpense' => $percentageChangeExpense,
            'lastMonthExpense' => $lastMonthExpense,
            'lastMonthIncome' => $lastMonthIncome,
            'top3SalesMedicines' => $top3SellingMedicines,
            'onlines' => $onlines,
            'phantram' => $phantram,
            'chiphituanhientai' => $chiphituanhientai,
            'chiphituantruoc' => $chiphituantruoc,


        ]);
    }
























  // Hàm tính tổng tiền của tuần hiện tại
  private function chiphituanhientai(Connection $connection): float
  {
      // Lấy ngày đầu tiên của tuần hiện tại
      $currentWeekStart = new \DateTime('monday this week');

      // Lấy ngày cuối cùng của tuần hiện tại
      $currentWeekEnd = new \DateTime('sunday this week');

      // Thực hiện truy vấn SQL để lấy tổng tiền của tuần hiện tại
      $sql = "SELECT SUM(PurchaseInvoices.Amount) AS total
              FROM PurchaseInvoices
              WHERE PurchaseInvoices.Date BETWEEN :start AND :end";

      $params = [
          'start' => $currentWeekStart->format('Y-m-d'),
          'end' => $currentWeekEnd->format('Y-m-d'),
      ];

      $result = $connection->executeQuery($sql, $params)->fetchAssociative();

      return (float) $result['total'];
  }

  // Hàm tính tổng tiền của tuần trước
  private function chiphituantruoc(Connection $connection): float
  {
      // Lấy ngày đầu tiên của tuần trước
      $lastWeekStart = new \DateTime('monday last week');

      // Lấy ngày cuối cùng của tuần trước
      $lastWeekEnd = new \DateTime('sunday last week');

      // Thực hiện truy vấn SQL để lấy tổng tiền của tuần trước
      $sql = "SELECT SUM(PurchaseInvoices.Amount) AS total
              FROM PurchaseInvoices
              WHERE PurchaseInvoices.Date BETWEEN :start AND :end";

      $params = [
          'start' => $lastWeekStart->format('Y-m-d'),
          'end' => $lastWeekEnd->format('Y-m-d'),
      ];

      $result = $connection->executeQuery($sql, $params)->fetchAssociative();

      return (float) $result['total'];
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
        $sql = "SELECT SUM(PurchaseInvoices.Amount) AS total
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
        $sql = "SELECT SUM(PurchaseInvoices.Amount) AS total
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
