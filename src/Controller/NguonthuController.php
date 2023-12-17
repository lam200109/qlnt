<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;

class NguonthuController extends AbstractController
{
    #[Route('/nguonthu', name: 'nguonthu')]
    public function index(Connection $connection, Request $request): Response
    {
        $sql = "SELECT SalesInvoices.SalesInvoiceID, SalesInvoices.CustomerID, SalesInvoices.Date, SalesInvoices.IncomeType, 
        FORMAT(SalesInvoices.Amount, 2) AS Amount, Customers.Name
 FROM SalesInvoices
 JOIN Customers ON SalesInvoices.CustomerID = Customers.CustomerID;        
 ";

$result = $connection->executeQuery($sql)->fetchAllAssociative();
$month = $request->query->get('month');
$year = $request->query->get('year');

// Gán giá trị mặc định nếu tháng và năm không có giá trị
$month = $month ?: date('n');  // Sử dụng tháng hiện tại nếu không có giá trị
$year = $year ?: date('Y');    // Sử dụng năm hiện tại nếu không có giá trị




         // Tính toán thu nhập của tháng này và tháng trước
        $thisMonthIncome = $this->calculateThisMonthIncome($connection);
        $lastMonthIncome = $this->calculateLastMonthIncome($connection);
        $incomeChangePercentage = ($lastMonthIncome > 0) ? (($thisMonthIncome - $lastMonthIncome) / $lastMonthIncome) * 100 : null;

        // Tính toán thu nhập của năm nay và năm trước
        $thisYearIncome = $this->calculateThisYearIncome($connection);
        $lastYearIncome = $this->calculateLastYearIncome($connection);
        $incomeChangePercentage = ($lastYearIncome > 0) ? (($thisYearIncome - $lastYearIncome) / $lastYearIncome) * 100 : null;


        // Tính toán thu nhập của tuần này và tuần trước
        $thisWeekIncome = $this->calculateThisWeekIncome($connection);
        $lastWeekIncome = $this->calculateLastWeekIncome($connection);
        $incomeChangePercentage = ($lastWeekIncome > 0) ? (($thisWeekIncome - $lastWeekIncome) / $lastWeekIncome) * 100 : null;


        // Tính toán thu nhập hôm nay và thay đổi so với ngày hôm qua
    $todayIncome = $this->calculateTodayIncome($connection);
    $yesterdayIncome = $this->calculateYesterdayIncome($connection);
    $todayChangePercentage = ($yesterdayIncome > 0) ? (($todayIncome - $yesterdayIncome) / $yesterdayIncome) * 100 : null;

        return $this->render('nguonthu/index.html.twig', [
            'controller_name' => 'NguonthuController',
            'result' => $result,
            'thisYearIncome' => $thisYearIncome,
            'lastYearIncome' => $lastYearIncome,
            'incomeChangePercentage' => $incomeChangePercentage,
            'thisMonthIncome' => $thisMonthIncome,
            'lastMonthIncome' => $lastMonthIncome,
            'incomeChangePercentage' => $incomeChangePercentage,
            'thisWeekIncome' => $thisWeekIncome,
            'lastWeekIncome' => $lastWeekIncome,
            'incomeChangePercentage' => $incomeChangePercentage,
            'todayIncome' => $todayIncome,
            'todayChangePercentage' => $todayChangePercentage,
        ]);
    }

    // Hàm tính thu nhập của ngày hôm nay
private function calculateTodayIncome(Connection $connection): float
{
    // Lấy ngày hôm nay
    $today = new \DateTime();

    // Thực hiện truy vấn SQL để lấy tổng thu nhập của ngày hôm nay
    $sql = "SELECT SUM(SalesInvoices.Amount) AS total
            FROM SalesInvoices
            WHERE DATE(SalesInvoices.Date) = :today";

    $params = [
        'today' => $today->format('Y-m-d'),
    ];

    $result = $connection->executeQuery($sql, $params)->fetchAssociative();

    return (float) $result['total'];
}

// Hàm tính thu nhập của ngày hôm qua
private function calculateYesterdayIncome(Connection $connection): float
{
    // Lấy ngày hôm qua
    $yesterday = new \DateTime('-1 day');

    // Thực hiện truy vấn SQL để lấy tổng thu nhập của ngày hôm qua
    $sql = "SELECT SUM(SalesInvoices.Amount) AS total
            FROM SalesInvoices
            WHERE DATE(SalesInvoices.Date) = :yesterday";

    $params = [
        'yesterday' => $yesterday->format('Y-m-d'),
    ];

    $result = $connection->executeQuery($sql, $params)->fetchAssociative();

    return (float) $result['total'];
}

    // Hàm tính thu nhập của năm nay
    private function calculateThisYearIncome(Connection $connection): float
    {
        // Lấy năm hiện tại
        $currentYear = date('Y');

        // Thực hiện truy vấn SQL để lấy tổng thu nhập của năm nay
        $sql = "SELECT SUM(Amount) AS total
                FROM SalesInvoices
                WHERE YEAR(Date) = :year";

        $params = [
            'year' => $currentYear,
        ];

        $result = $connection->executeQuery($sql, $params)->fetchAssociative();

        return (float) $result['total'];
    }

    // Hàm tính thu nhập của năm trước
    private function calculateLastYearIncome(Connection $connection): float
    {
        // Lấy năm trước
        $lastYear = date('Y') - 1;

        // Thực hiện truy vấn SQL để lấy tổng thu nhập của năm trước
        $sql = "SELECT SUM(Amount) AS total
                FROM SalesInvoices
                WHERE YEAR(Date) = :year";

        $params = [
            'year' => $lastYear,
        ];

        $result = $connection->executeQuery($sql, $params)->fetchAssociative();

        return (float) $result['total'];
    }
        // Hàm tính thu nhập của tháng này
private function calculateThisMonthIncome(Connection $connection): float
{
    // Lấy tháng và năm hiện tại
    $currentMonth = date('m');
    $currentYear = date('Y');

    // Thực hiện truy vấn SQL để lấy tổng thu nhập của tháng này
    $sql = "SELECT SUM(Amount) AS total
            FROM SalesInvoices
            WHERE MONTH(Date) = :month AND YEAR(Date) = :year";

    $params = [
        'month' => $currentMonth,
        'year' => $currentYear,
    ];

    $result = $connection->executeQuery($sql, $params)->fetchAssociative();

    return (float) $result['total'];
}

// Hàm tính thu nhập của tháng trước
    private function calculateLastMonthIncome(Connection $connection): float
    {
        // Lấy tháng và năm trước
        $lastMonth = date('m') - 1;
        $lastYear = date('Y');

        // Xử lý năm trước nếu tháng trước là tháng 1
        if ($lastMonth === 0) {
            $lastMonth = 12;
            $lastYear = date('Y') - 1;
        }

        // Thực hiện truy vấn SQL để lấy tổng thu nhập của tháng trước
        $sql = "SELECT SUM(Amount) AS total
                FROM SalesInvoices
                WHERE MONTH(Date) = :month AND YEAR(Date) = :year";

        $params = [
            'month' => $lastMonth,
            'year' => $lastYear,
        ];

        $result = $connection->executeQuery($sql, $params)->fetchAssociative();

        return (float) $result['total'];
    }

        // Hàm tính thu nhập của tuần này
private function calculateThisWeekIncome(Connection $connection): float
{
    // Lấy ngày đầu tiên và cuối cùng của tuần hiện tại
    $currentWeekStart = new \DateTime('monday this week');
    $currentWeekEnd = new \DateTime('sunday this week');

    // Thực hiện truy vấn SQL để lấy tổng thu nhập của tuần này
    $sql = "SELECT SUM(Amount) AS total
            FROM SalesInvoices
            WHERE Date BETWEEN :start AND :end";

    $params = [
        'start' => $currentWeekStart->format('Y-m-d'),
        'end' => $currentWeekEnd->format('Y-m-d'),
    ];

    $result = $connection->executeQuery($sql, $params)->fetchAssociative();

    return (float) $result['total'];
}

    // Hàm tính thu nhập của tuần trước
    private function calculateLastWeekIncome(Connection $connection): float
    {
        // Lấy ngày đầu tiên và cuối cùng của tuần trước
        $lastWeekStart = new \DateTime('monday last week');
        $lastWeekEnd = new \DateTime('sunday last week');

        // Thực hiện truy vấn SQL để lấy tổng thu nhập của tuần trước
        $sql = "SELECT SUM(Amount) AS total
                FROM SalesInvoices
                WHERE Date BETWEEN :start AND :end";

        $params = [
            'start' => $lastWeekStart->format('Y-m-d'),
            'end' => $lastWeekEnd->format('Y-m-d'),
        ];

        $result = $connection->executeQuery($sql, $params)->fetchAssociative();

        return (float) $result['total'];
    }
}
