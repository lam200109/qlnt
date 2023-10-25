<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Nhaphanphoi
 *
 * @ORM\Table(name="NhaPhanPhoi")
 * @ORM\Entity
 */
class Nhaphanphoi
{
    /**
     * @var int
     *
     * @ORM\Column(name="MaNPP", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $MaNPP;
    public function getMaNPP()
    {
        return $this->MaNPP;
    }
    /**
     * @var string|null
     *
     * @ORM\Column(name="TenNPP", type="string", length=100, nullable=true)
     */
    private $TenNPP;
    public function getTenNPP()
    {
        return $this->TenNPP;
    }
    /**
     * @var string|null
     *
     * @ORM\Column(name="DiaChi", type="string", length=255, nullable=true)
     */
    private $DiaChi;
    public function getDiaChi()
    {
        return $this->DiaChi;
    }
    /**
     * @var int|null
     *
     * @ORM\Column(name="SoDienThoai", type="integer", nullable=true)
     */
    private $SoDienThoai;
    public function getSoDienThoai()
    {
        return $this->SoDienThoai;
    }
    /**
     * @var int|null
     *
     * @ORM\Column(name="Fax", type="integer", nullable=true)
     */
    private $Fax;
    public function getFax()
    {
        return $this->Fax;
    }
    /**
     * @var string|null
     *
     * @ORM\Column(name="Email", type="string", length=45, nullable=true)
     */
    private $Email;
    public function getEmail()
    {
        return $this->Email;
    }
    /**
     * @var int|null
     *
     * @ORM\Column(name="MaSoThue", type="integer", nullable=true)
     */
    private $MaSoThue;
    public function getMaSoThue()
    {
        return $this->MaSoThue;
    }
    /**
     * @var string|null
     *
     * @ORM\Column(name="GhiChu", type="string", length=255, nullable=true)
     */
    private $GhiChu;
    public function getGhiChu()
    {
        return $this->GhiChu;
    }

}
