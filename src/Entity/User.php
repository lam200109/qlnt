<?php

// src/Entity/User.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
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
    private $userID;

    /**
     * @ORM\Column(name="FullName", type="string", length=255)
     */
    private $fullName;

    /**
     * @ORM\Column(name="Username", type="string", length=50)
     */
    private $username;

    /**
     * @ORM\Column(name="Password", type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(name="Email", type="string", length=100)
     */
    private $email;

//   /**
//      * @ORM\Column(type="json")
//      */
//     private $permissions = [];

    /**
     * @ORM\Column(name="Phone", type="string", length=20)
     */
    private $phone;

    /**
     * @ORM\Column(name="Avatar", type="string", length=255, nullable=true)
     */
    private $avatar;
    
  
/**
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="users", fetch="EAGER")
     * @ORM\JoinTable(name="UserRoles",
     *      joinColumns={@ORM\JoinColumn(name="UserID", referencedColumnName="UserID")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="RoleID", referencedColumnName="RoleID")}
     * )
     */
    private $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();

    }

     /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->userID;
    }


     /**
     * @return array
     */
    
     public function getRoles(): array
    {
        $roles = [];

        foreach ($this->roles as $role) {
            // Assume each role has a method to get its name, adjust accordingly
            $roles[] = $role->getRoleName(); // Change to the actual method if it's different
        }

        return array_unique($roles); // Make sure the roles are unique
    }

 /**
 * Check if the user has a specific permission.
 *
 * @param string $permissionName
 * @return bool
 */
public function hasPermission(string $permissionName): bool
{
    return in_array($permissionName, $this->getPermissions());
}



    // /**
    //  * @param array $permissions
    //  * @return $this
    //  */
    // public function setPermissions(array $permissions): self
    // {
    //     $this->permissions = new ArrayCollection($permissions);

    //     return $this;
    // }




/**
     * @return array
     */
    public function getPermissions(): array
    {
        $permissions = [];
        
        // Lặp qua từng vai trò của người dùng
        foreach ($this->roles as $role) {
            // Lấy tất cả quyền của vai trò
            $rolePermissions = $role->getPermissions();

            // Thêm các quyền vào mảng permissions
            foreach ($rolePermissions as $permission) {
                $permissions[] = $permission->getPermissionName();
            }
        }

        return $permissions;
    }



   
    

    public function eraseCredentials()
    {
        // Không cần thực hiện gì cả, vì thông tin nhạy cảm như mật khẩu đã được xử lý trong trình xác thực
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->getUserID();
    }
    


    public function getUserID(): ?int
    {
        return $this->userID;
    }

    public function setUserID(int $userID): self
    {
        $this->userID = $userID;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    // ... Định nghĩa các getter và setter cho các trường khác

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }
     /**
     * Check if the user is a customer.
     *
     * @return bool
     */
    public function isCustomer()
    {
        // Thực hiện logic kiểm tra nếu user là khách hàng
        // Ví dụ: return $this->role === 'ROLE_CUSTOMER';
        return true; // Hoặc thực hiện kiểm tra khác tùy thuộc vào logic của bạn
    }
}
