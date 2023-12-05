<?php

/// src/Entity/Permission.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PermissionRepository")
 * @ORM\Table(name="Permissions")
 */
class Permission
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="PermissionID", type="integer")
     */
    private $permissionID;

    /**
     * @ORM\Column(name="PermissionName", type="string", length=50)
     */
    private $permissionName;

    /**
     * @ORM\Column(name="Description", type="string", length=255)
     */
    private $description;
    
    /**
 * @ORM\ManyToMany(targetEntity="Role", mappedBy="permissions")
 */
private $roles;

    // Định nghĩa các getter và setter tương ứng cho các trường

    public function getPermissionID(): ?int
    {
        return $this->permissionID;
    }

    public function setPermissionID(int $permissionID): self
    {
        $this->permissionID = $permissionID;

        return $this;
    }

    public function getPermissionName(): ?string
    {
        return $this->permissionName;
    }

    public function setPermissionName(string $permissionName): self
    {
        $this->permissionName = $permissionName;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
