<?php
// src/Entity/Customer.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @ORM\Table(name="Customers")
 * @ORM\Entity(repositoryClass="App\Repository\CustomerRepository")
 */
class Customer implements UserInterface, PasswordAuthenticatedUserInterface
{
     /**
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @ORM\Column(name="CustomerID", type="integer")
     */
    private $customerID;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(name="Username", type="string", length=45)
     */
    private $username;

    /**
     * @ORM\Column(name="Password", type="string", length=45)
     */
    private $password;

    // Getter and Setter methods...

    public function getId(): ?int
    {
        return $this->customerID;
        ;
    }

    // Other getter and setter methods for each property...

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }


    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    
    public function getSalt()
    {
        // Nếu bạn không sử dụng bcrypt hoặc argon2i, bạn có thể cần cài đặt hàm này,
        // nhưng ở đây, chúng ta sử dụng bcrypt, không cần salt riêng
        return null;
    }

    public function eraseCredentials()
    {
        // Nếu bạn cần xử lý xóa thông tin đăng nhập nhạy cảm, bạn có thể thực hiện ở đây
    }
    public function getRoles(): array
    {
        return ['ROLE_CUSTOMER']; // Define customer role
    }
    public function getUserIdentifier(): string
    {
        return $this->getUsername();
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
    // Add this method to your Customer entity
public function getCustomerID(): ?int
{
    return $this->customerID;
}


}
