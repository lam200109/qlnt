<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use App\Entity\User; // Thêm dòng này để sử dụng namespace của Entity User
use Symfony\Component\HttpFoundation\Request;

class ChamcongController extends AbstractController
{

    
        /**
         * @Route("/chamcong/filter", name="chamcong_filter", methods={"POST"})
         */    
        public function index(Connection $connection, Request $request): Response
        {
            // Lấy thông tin người dùng hiện tại từ phiên đăng nhập
            $user = $this->getUser();
            $userId = (int) $user->getUserIdentifier();
        
            // Khai báo biến $filteredData để tránh lỗi Undefined variable
            $filteredData = [];
        
            // Kiểm tra quyền truy cập BaoCaoTaiChinhAccess hoặc AdminAccess
            if ($this->isGranted('BaoCaoTaiChinhAccess') || $this->isGranted('AdminAccess')) {
                $attendanceData = $this->getAttendanceData($connection, $userId);
        
        
          
        
                $date = new \DateTime();
                $shiftId = $this->determineShiftId(); // Thay bằng logic của bạn
        
                $hasClockedInToday = $this->hasClockedInToday($connection, $userId, $date, $shiftId);
        
                if ($hasClockedInToday) {
                    $this->addFlash('danger', 'Bạn đã chấm công trong ngày.');
                } else {
                    // Thực hiện chấm công
                    $this->clockInToday($connection, $userId, $date, $shiftId);
                    $this->addFlash('success', 'Chấm công thành công.');
                }
        
                return $this->render('chamcong/index.html.twig', [
                    'attendanceData' => $attendanceData,
                    'filteredData' => $filteredData,
                ]);
        
            }
        
            $date = new \DateTime();
            $shiftId = $this->determineShiftId(); // Thay bằng logic của bạn
        
            $hasClockedInToday = $this->hasClockedInToday($connection, $userId, $date, $shiftId);
        
            if ($hasClockedInToday) {
                $this->addFlash('danger', 'Bạn đã chấm công trong ngày.');
            } else {
                // Thực hiện chấm công
                $this->clockInToday($connection, $userId, $date, $shiftId);
                $this->addFlash('success', 'Chấm công thành công.');
            }
        
            return $this->redirectToRoute('trang_chu');
        }
        




     private function getAttendanceData(Connection $connection): array
{
    $query = "
        SELECT Users.FullName, Attendance.Date, Attendance.LoginTime, Attendance.LogoutTime, Attendance.ShiftID
        FROM Users
        INNER JOIN Attendance ON Users.UserID = Attendance.UserID
        ORDER BY Attendance.Date DESC
    ";

    
    $attendanceData = $connection->executeQuery($query)->fetchAllAssociative();

    // Tính tổng số giờ làm và thêm vào mảng kết quả
    foreach ($attendanceData as &$entry) {
        $loginTime = new \DateTime($entry['LoginTime']);
        $logoutTime = new \DateTime($entry['LogoutTime']);
        $interval = $loginTime->diff($logoutTime);

        // Định dạng tổng số giờ làm là "9hr 0min"
        $entry['TotalHours'] = $interval->format('%hh %imin');
    }

    return $attendanceData;}
 
    
    private function hasClockedInToday(Connection $connection, $userId, \DateTime $date, $shiftId): bool
    {
        $hasClockedInQuery = "
            SELECT COUNT(*)
            FROM Attendance
            WHERE UserID = :userId
            AND DATE = :date
            AND ShiftID = :shiftId
        ";
    
        $result = $connection->executeQuery($hasClockedInQuery, [
            'userId' => $userId,
            'date' => $date->format('Y-m-d'),
            'shiftId' => $shiftId,
        ])->fetchOne();
    
        return $result > 0;
    }
    
     
    /**
     * Thực hiện chấm công cho người dùng vào ngày hiện tại
     */
    private function clockInToday(Connection $connection, $userId, \DateTime $date, $shiftId): void
    {
        $clockInQuery = "
            INSERT INTO Attendance (UserID, Date, LoginTime, ShiftID)
            VALUES (:userId, :date, CURRENT_TIME, :shiftId)
        ";

        $connection->executeQuery($clockInQuery, [
            'userId' => $userId,
            'date' => $date->format('Y-m-d'),
            'shiftId' => $shiftId,
        ]);
    }

    
    private function determineShiftId(): string
{
    $currentTime = new \DateTime();
    $currentTime->setTimezone(new \DateTimeZone('Asia/Ho_Chi_Minh'));
    $currentHour = (int) $currentTime->format('H');


    if ($currentHour >= 6 && $currentHour < 10) {
        return 'Ca sáng';
    } elseif ($currentHour >= 10 && $currentHour < 14) {
        return 'Ca trưa';
    } elseif ($currentHour >= 14 && $currentHour < 18) {
        return 'Ca chiều';
    } elseif ($currentHour >= 18 && $currentHour < 22) {
        return 'Ca tối';
    } else {
        return 'Không trong giờ làm việc';
    }
}

    
}
