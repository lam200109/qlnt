<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Doctrine\DBAL\Connection;
use App\Security\User; // Import class User
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class CustomUserProvider implements UserProviderInterface
{
    private $dbConnection;

    public function __construct(Connection $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
{
    // Thực hiện truy vấn SQL hoặc bất kỳ phương thức nào bạn muốn để tìm thông tin người dùng
    $userData = $this->dbConnection->fetchAssociative('
    SELECT u.*, GROUP_CONCAT(DISTINCT r.RoleName) AS Roles
    FROM Users u
    LEFT JOIN Roles r ON u.RoleID = r.RoleID
    WHERE u.Username = :username
    GROUP BY u.UserID
', ['username' => $identifier]);




    if (!$userData) {
        throw new AuthenticationException(sprintf('User with identifier "%s" not found.', $identifier));
    }

    // Kiểm tra xem key 'Roles' có tồn tại trong $userData không
    if (!array_key_exists('Roles', $userData)) {
        throw new \RuntimeException('Missing "Roles" key in user data.');
    }

    // Trả về một đối tượng UserInterface (có thể là custom User class của bạn)
    return new User($userData['UserID'], $userData['Username'], $userData['Password'], explode(',', $userData['Roles']));
}


    public function loadUserByUsername(string $username): UserInterface
    {
        return $this->loadUserByIdentifier($username);
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'App\Security\User';
    }
}
