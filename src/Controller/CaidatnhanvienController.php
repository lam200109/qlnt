<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection; // Import thư viện Connection
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class CaidatnhanvienController extends AbstractController
{
    #[Route('/caidatnhanvien', name: 'caidatnhanvien')]
    public function index(Connection $connection): Response
    {
        // Thực hiện truy vấn để lấy thông tin từ 4 bảng
        $sql = "
        SELECT 
        u.UserID, 
        u.FullName, 
        u.Username, 
        u.Email, 
        u.CreatedDate, 
        GROUP_CONCAT(DISTINCT p.PermissionName ORDER BY p.PermissionName ASC) AS PermissionNames,
        GROUP_CONCAT(DISTINCT r.RoleName ORDER BY r.RoleName ASC) AS RoleNames
    FROM Users u
    LEFT JOIN UserRoles ur ON u.UserID = ur.UserID
    LEFT JOIN RolePermissions rp ON ur.RoleID = rp.RoleID
    LEFT JOIN Permissions p ON rp.PermissionID = p.PermissionID
    LEFT JOIN Roles r ON ur.RoleID = r.RoleID
    GROUP BY u.UserID, u.FullName, u.Username, u.Email, u.CreatedDate;
    
    
        ";

        $result = $connection->fetchAllAssociative($sql);
        $sql1 = "SELECT PermissionID, PermissionName FROM Permissions";
        $permissions = $connection->fetchAllAssociative($sql1);

        $sql2 = "SELECT *  FROM Roles";
        $Roles = $connection->fetchAllAssociative($sql2);
        // TODO: Thực hiện các xử lý khác với dữ liệu kết hợp từ 4 bảng

        return $this->render('caidatnhanvien/index.html.twig', [
            'controller_name' => 'CaidatnhanvienController',
            'result' => $result,
            'permissions' => $permissions,
            'roles' => $Roles,


        ]);
    }

    #[Route('/add_permission', name: 'add_permission', methods: ['POST'])]
    public function addPermission(Request $request, Connection $connection): Response
    {
        // Lấy dữ liệu từ form
        $userID = $request->request->get('user_id');
        $roleID =  $request->request->get('role_id');
        $permissionID = $request->request->get('permission_id');

        // Kiểm tra xem vai trò đã được gán cho người dùng chưa
        $checkUserRoleQuery = "SELECT p.PermissionName
        FROM Users u
        JOIN UserRoles ur ON u.UserID = ur.UserID
        JOIN RolePermissions rp ON ur.RoleID = rp.RoleID
        JOIN Permissions p ON rp.PermissionID = p.PermissionID
        WHERE u.UserID = :userID AND p.PermissionID = :permissionID;
        ;";


        $checkUserRoleParams = ['userID' => $userID, 'permissionID' => $permissionID];
        $existingUserRole = $connection->fetchAllAssociative($checkUserRoleQuery, $checkUserRoleParams);
    
        if (empty($existingUserRole)) {
            // Nếu vai trò chưa được gán, thêm vai trò cho người dùng
            $insertUserRoleQuery = "INSERT INTO UserRoles (UserID, RoleID) VALUES (:userID, :roleID)";
            $insertUserRoleParams = ['userID' => $userID, 'roleID' => $roleID];
            $connection->executeQuery($insertUserRoleQuery, $insertUserRoleParams);
    
            // Thêm thông báo thành công
            $this->addFlash('success', 'Tạo quyền truy cập thành công.');
        } else {
            // Thêm thông báo nếu vai trò đã được gán trước đó
            $this->addFlash('warning', 'Vai trò đã được gán cho người dùng trước đó.');
        }
    
        // Thực hiện thêm quyền cho vai trò trong bảng RolePermissions
        $this->addRolePermission($roleID, $userID, $request, $connection);
    
        // Chuyển hướng hoặc trả về response tùy thuộc vào kết quả xử lý
        return $this->redirectToRoute('cai_dat_nhan_vien');
    }
    


    private function addRolePermission($roleID, $userID, Request $request, Connection $connection): void
    {
        // Lấy dữ liệu từ form
        $permissionID = $request->request->get('permission_id');
        $salary = $request->request->get('salary'); // Thêm giá trị lương nếu có
    
        // Kiểm tra xem quyền đã được gán cho vai trò của người dùng chưa
        $checkRolePermissionQuery = "SELECT * FROM RolePermissions WHERE RoleID = :roleID AND PermissionID = :permissionID";
        $checkRolePermissionParams = ['roleID' => $roleID, 'permissionID' => $permissionID];
        $existingRolePermission = $connection->fetchAllAssociative($checkRolePermissionQuery, $checkRolePermissionParams);
    
        if (empty($existingRolePermission)) {
            // Nếu quyền chưa được gán, thêm quyền cho vai trò của người dùng
            $insertRolePermissionQuery = "INSERT INTO RolePermissions (RoleID, PermissionID, Salary) VALUES (:roleID, :permissionID, :salary)";
            $insertRolePermissionParams = ['roleID' => $roleID, 'permissionID' => $permissionID, 'salary' => $salary];
            $connection->executeQuery($insertRolePermissionQuery, $insertRolePermissionParams);
        }
        
    }
    


    
    #[Route('/delete_permission', name: 'delete_permission', methods: ['POST'])]
public function deletePermission(Request $request, Connection $connection): Response
{
    // Lấy dữ liệu từ form
    $roleID = $request->request->get('role_id');
    $permissionID = $request->request->get('permission_id');

    // Xoá quyền cho vai trò trong bảng RolePermissions
    $deletePermissionQuery = "DELETE FROM RolePermissions WHERE RoleID = :roleID AND PermissionID = :permissionID";
    $deletePermissionParams = ['roleID' => $roleID, 'permissionID' => $permissionID];
    $connection->executeQuery($deletePermissionQuery, $deletePermissionParams);

    // Thêm thông báo thành công
    $this->addFlash('success', 'Quyền đã được xoá cho vai trò thành công.');

    // Chuyển hướng hoặc trả về response tùy thuộc vào kết quả xử lý
    return $this->redirectToRoute('cai_dat_nhan_vien');
}

    
    
}
