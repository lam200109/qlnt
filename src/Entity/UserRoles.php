<?php

/// src/Entity/UserRoles.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRolesRepository")
 * @ORM\Table(name="UserRoles")
 */
class UserRoles
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="UserRolesID", type="integer")
     */
    private $userRolesID;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="UserID", referencedColumnName="UserID", nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumn(name="RoleID", referencedColumnName="RoleID", nullable=false)
     */
    private $role;

    // Định nghĩa các getter và setter tương ứng cho các trường

    public function getUserRolesID(): ?int
    {
        return $this->userRolesID;
    }

    public function setUserRolesID(int $userRolesID): self
    {
        $this->userRolesID = $userRolesID;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;

        return $this;
    }
}
