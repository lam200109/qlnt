<?php

// src/Entity/Role.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RoleRepository")
 * @ORM\Table(name="Roles")
 */
class Role
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="RoleID", type="integer")
     */
    private $roleID;

    /**
     * @ORM\Column(name="RoleName", type="string", length=50)
     */
    private $roleName;

/**
     * @ORM\ManyToMany(targetEntity="Permission", mappedBy="roles")
     */
    private $rolePermissions;


    /**
 * @ORM\ManyToMany(targetEntity="Permission")
 * @ORM\JoinTable(name="RolePermissions",
 *      joinColumns={@ORM\JoinColumn(name="RoleID", referencedColumnName="RoleID")},
 *      inverseJoinColumns={@ORM\JoinColumn(name="PermissionID", referencedColumnName="PermissionID")}
 * )
 */
private $permissions;


    public function __construct()
    {
        $this->rolePermissions = new ArrayCollection();
        $this->permissions = new ArrayCollection();

    }

     /**
     * @return Collection|Permission[]
     */
    public function getPermissions(): Collection {
        return $this->permissions;
    }


//     public function getPermissionName(): ?string
// {
//     return $this->permissionName;
// }

    /**
     * @return Collection|Permission[]
     */
    public function getRolePermissions(): Collection
    {
        return $this->rolePermissions;
    }

    public function getRoleID(): ?int
    {
        return $this->roleID;
    }

    public function setRoleID(int $roleID): self
    {
        $this->roleID = $roleID;

        return $this;
    }

    public function getRoleName(): ?string
    {
        return $this->roleName;
    }

    public function setRoleName(string $roleName): self
    {
        $this->roleName = $roleName;

        return $this;
    }
}
