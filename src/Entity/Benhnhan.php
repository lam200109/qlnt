<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Benhnhan
 *
 * @ORM\Table(name="BenhNhan")
 * @ORM\Entity
 */
class Benhnhan
{
    /**
     * @var int
     *
     * @ORM\Column(name="IDBN", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $IDBN;
    public function getIdbn()
{
    return $this->IDBN;
}

    /**
     * @var int|null
     *
     * @ORM\Column(name="MaBN", type="integer", nullable=true)
     */
    private $MaBN;
    public function getMabn()
{
    return $this->MaBN;
}

    /**
     * @var string|null
     *
     * @ORM\Column(name="HoTen", type="string", length=45, nullable=true)
     */
    private $HoTen;
    public function getHoten()
{
    return $this->HoTen;
}

    /**
     * @var string|null
     *
     * @ORM\Column(name="Tuoi", type="string", length=45, nullable=true)
     */
    private $Tuoi;
    public function getTuoi()
    {
        return $this->Tuoi;
    }
    /**
     * @var string|null
     *
     * @ORM\Column(name="DiaChi", type="string", length=45, nullable=true)
     */
    private $DiaChi;
    public function getDiachi()
{
    return $this->DiaChi;
}
    /**
     * @var int|null
     *
     * @ORM\Column(name="SoDienThoai", type="integer", nullable=true)
     */
    private $SoDienThoai;
    public function getSodienthoai()
{
    return $this->SoDienThoai;
}
    /**
     * @var int|null
     *
     * @ORM\Column(name="Role", type="integer", nullable=true)
     */
    private $Role;
    public function getRole()
    {
        return $this->Role;
    }

     /**
     * @var string|null
     *
     * @ORM\Column(name="TaiKhoan", type="string", length=45, nullable=true)
     */
    private $TaiKhoan;
    public function getTaikhoan()
    {
        return $this->TaiKhoan;
    }

     /**
     * @var string|null
     *
     * @ORM\Column(name="MatKhau", type="string", length=45, nullable=true)
     */
    private $MatKhau;
    public function getMatkhau()
    {
        return $this->MatKhau;
    }

     /**
     * @var string|null
     *
     * @ORM\Column(name="GhiChu", type="string", length=45, nullable=true)
     */
    private $GhiChu;
    public function getGhichu()
    {
        return $this->GhiChu;
    }


        
}
