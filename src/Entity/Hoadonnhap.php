<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Hoadonnhap
 *
 * @ORM\Table(name="HoaDonNhap")
 * @ORM\Entity
 */
class Hoadonnhap
{
    /**
     * @var int
     *
     * @ORM\Column(name="MaHDN", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $MaHDN;
    public function getMaHDN()
    {
        return $this->MaHDN;
    }
    /**
     * @var string|null
     *
     * @ORM\Column(name="MaNPP", type="string", length=45, nullable=true)
     */
    private $MaNPP;
    public function getMaNPP()
    {
        return $this->MaNPP;
    }
    /**
     * @var string|null
     *
     * @ORM\Column(name="NguoiGiao", type="string", length=45, nullable=true)
     */
    private $NguoiGiao;
    public function getNguoiGiao()
    {
        return $this->NguoiGiao;
    }
    /**
     * @var string|null
     *
     * @ORM\Column(name="NguoiNhan", type="string", length=45, nullable=true)
     */
    private $NguoiNhan;
    public function getNguoiNhan()
    {
        return $this->NguoiNhan;
    }
    /**
     * @var int|null
     *
     * @ORM\Column(name="TongTienThuoc", type="integer", nullable=true)
     */
    private $TongTienThuoc;
    public function getTongTienThuoc()
    {
        return $this->TongTienThuoc;
    }
    /**
     * @var int|null
     *
     * @ORM\Column(name="TongThue", type="integer", nullable=true)
     */
    private $TongThue;
    public function getTongThue()
    {
        return $this->TongThue;
    }
    /**
     * @var int|null
     *
     * @ORM\Column(name="TongTienHD", type="integer", nullable=true)
     */
    private $TongTienHD;
    public function getTongTienHD()
    {
        return $this->TongTienHD;
    }
    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="NgayNhap", type="datetime", nullable=true)
     */
    private $NgayNhap;
    public function getNgayNhap()
    {
        return $this->NgayNhap;
    }

}
