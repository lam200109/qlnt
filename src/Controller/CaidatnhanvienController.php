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
        Users.*,
        GROUP_CONCAT(DISTINCT Roles.RoleName) AS RoleNames,
        GROUP_CONCAT(DISTINCT Permissions.PermissionName) AS PermissionNames
    FROM Users
    LEFT JOIN UserRoles ON Users.UserID = UserRoles.UserID
    LEFT JOIN Roles ON UserRoles.RoleID = Roles.RoleID
    LEFT JOIN RolePermissions ON Roles.RoleID = RolePermissions.RoleID
    LEFT JOIN Permissions ON RolePermissions.PermissionID = Permissions.PermissionID
    GROUP BY Users.UserID;
    
        ";

        $result = $connection->fetchAllAssociative($sql);
        $sql1 = "SELECT PermissionID, PermissionName FROM Permissions";
        $permissions = $connection->fetchAllAssociative($sql1);
        // TODO: Thực hiện các xử lý khác với dữ liệu kết hợp từ 4 bảng

        return $this->render('caidatnhanvien/index.html.twig', [
            'controller_name' => 'CaidatnhanvienController',
            'result' => $result,
            'permissions' => $permissions,

        ]);
    }

    #[Route('/add_permission', name: 'add_permission', methods: ['POST'])]
    public function addPermission(Request $request, Connection $connection): Response
    {
        // Lấy dữ liệu từ form
        $userID = $request->request->get('user_id');
        $roleID = $request->request->get('role_id') ?? 2; // Sử dụng giá trị mặc định là 2 nếu không có giá trị từ form
    
        // Kiểm tra xem vai trò đã được gán cho người dùng chưa
        $checkUserRoleQuery = "SELECT * FROM UserRoles WHERE UserID = :userID AND RoleID = :roleID";
        $checkUserRoleParams = ['userID' => $userID, 'roleID' => $roleID];
        $existingUserRole = $connection->fetchAllAssociative($checkUserRoleQuery, $checkUserRoleParams);
    
        if (empty($existingUserRole)) {
            // Nếu vai trò chưa được gán, thêm vai trò cho người dùng
            $insertUserRoleQuery = "INSERT INTO UserRoles (UserID, RoleID) VALUES (:userID, :roleID)";
            $insertUserRoleParams = ['userID' => $userID, 'roleID' => $roleID];
            $connection->executeQuery($insertUserRoleQuery, $insertUserRoleParams);
    
            // Thêm thông báo thành công
            $this->addFlash('success', 'Tạo quyền tru cập thành công.');
        } else {
            // Thêm thông báo nếu vai trò đã được gán trước đó
            $this->addFlash('warning', 'Vai trò đã được gán cho người dùng trước đó.');
        }
    
        // Thực hiện thêm quyền cho vai trò trong bảng RolePermissions
        $this->addRolePermission($roleID, $request, $connection);
    
        // Chuyển hướng hoặc trả về response tùy thuộc vào kết quả xử lý
        return $this->redirectToRoute('cai_dat_nhan_vien');
    }

    private function addRolePermission($roleID, Request $request, Connection $connection): void
    {
        // Lấy dữ liệu từ form
        $permissionID = $request->request->get('permission_id');
        var_dump($permissionID);

        $salary = $request->request->get('salary'); // Thêm giá trị lương nếu có
        $defaultRoleID = 2; // Giá trị mặc định cho RoleID

        // Kiểm tra xem quyền đã được gán cho vai trò chưa
        $checkRolePermissionQuery = "SELECT * FROM RolePermissions WHERE RoleID = :roleID AND PermissionID = :permissionID";
        $checkRolePermissionParams = ['roleID' => $roleID, 'permissionID' => $permissionID];
        $existingRolePermission = $connection->fetchAllAssociative($checkRolePermissionQuery, $checkRolePermissionParams);

        if (empty($existingRolePermission)) {
            // Nếu quyền chưa được gán, thêm quyền cho vai trò
            $insertRolePermissionQuery = "INSERT INTO RolePermissions (RoleID, PermissionID, Salary) VALUES (:roleID, :permissionID, :salary)";
            $insertRolePermissionParams = ['roleID' => $defaultRoleID, 'permissionID' => $permissionID, 'salary' => $salary];
            $connection->executeQuery($insertRolePermissionQuery, $insertRolePermissionParams);
        }
    }


    
    #[Route('/delete_permission', name: 'delete_permission', methods: ['POST'])]
public function deletePermission(Request $request, Connection $connection): Response
{
    // Lấy dữ liệu từ form
    $roleID = $request->request->get('role_id') ?? 2;
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
