<?php

// src/Entity/User.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="Users")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="UserID", type="integer")
     */
    private $UserID;

    /**
     * @ORM\Column(name="FullName", type="string", length=255, nullable=true)
     */
    private $FullName;

    /**
     * @ORM\Column(name="Username", type="string", length=50, unique=true)
     */
    private $Username;

    /**
     * @ORM\Column(name="Password", type="string", length=255)
     */
    private $Password;

    /**
     * @ORM\Column(name="Email", type="string", length=100)
     */
    private $Email;

    /**
     * @ORM\Column(name="Phone", type="string", length=20)
     */
    private $Phone;

    /**
     * @ORM\Column(name="Avatar", type="string", length=255)
     */
    private $Avatar;

    public function __construct()
    {
        // Một số giá trị mặc định hoặc khởi tạo ở đây (nếu cần)
    }

    // Các getters và setters

    public function getUserId(): ?int
    {
        return $this->UserID;
    }

    public function getRoles(): array
    {
        // Trả về một mảng chứa các vai trò của người dùng, ví dụ ['ROLE_USER']
        return ['ROLE_USER'];
    }

    public function setRoles(array $roles): self
    {
        // Do không có cột 'user_roles', nên chỉ định vai trò trong phương thức này sẽ không ảnh hưởng đến cơ sở dữ liệu
        return $this;
    }

    public function getSalt()
    {
        // Chưa sử dụng salt trong trường hợp này
        return null;
    }

    public function eraseCredentials()
    {
        // Nếu có thông tin nhạy cảm cần xoá sau khi xác thực, bạn có thể triển khai ở đây
    }

    public function getUsername()
    {
        return $this->Username;
    }

    public function getPassword(): ?string
    {
        return $this->Password;
    }

    public function getUserIdentifier(): string
    {
        return $this->Username;
    }

    // ... (các phương thức khác)
}
