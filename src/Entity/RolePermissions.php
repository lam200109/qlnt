<?php

// src/Entity/RolePermissions.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RolePermissionsRepository")
 * @ORM\Table(name="RolePermissions")
 */
class RolePermissions
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="RolePermissionsID", type="integer")
     */
    private $rolePermissionsID;

    /**
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumn(name="RoleID", referencedColumnName="RoleID", nullable=false)
     */
    private $role;

    /**
     * @ORM\ManyToOne(targetEntity="Permission")
     * @ORM\JoinColumn(name="PermissionID", referencedColumnName="PermissionID", nullable=false)
     */
    private $permission;

    // Định nghĩa các getter và setter tương ứng cho các trường

    public function getRolePermissionsID(): ?int
    {
        return $this->rolePermissionsID;
    }

    public function setRolePermissionsID(int $rolePermissionsID): self
    {
        $this->rolePermissionsID = $rolePermissionsID;

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

    public function getPermission(): ?Permission
    {
        return $this->permission;
    }

    public function setPermission(?Permission $permission): self
    {
        $this->permission = $permission;

        return $this;
    }
}
