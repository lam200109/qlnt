<?php

namespace AppBundle;

use Doctrine\ORM\Mapping as ORM;

/**
 * Chitiethoadonxuat
 *
 * @ORM\Table(name="ChiTietHoaDonXuat")
 * @ORM\Entity
 */
class Chitiethoadonxuat
{
    /**
     * @var int
     *
     * @ORM\Column(name="MaCTHDX", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $macthdx;
    public function getMacthdx()
    {
        return $this->macthdx;
    }
    /**
     * @var int|null
     *
     * @ORM\Column(name="MaHDX", type="integer", nullable=true)
     */
    private $mahdx;
    public function getMahdx()
    {
        return $this->mahdx;
    }
    /**
     * @var int|null
     *
     * @ORM\Column(name="IDThuoc", type="integer", nullable=true)
     */
    private $idthuoc;
    public function getIdthuoc()
    {
        return $this->idthuoc;
    }
    /**
     * @var int|null
     *
     * @ORM\Column(name="SoLuong", type="integer", nullable=true)
     */
    private $soluong;
    public function getSoluong()
    {
        return $this->soluong;
    }
    /**
     * @var int|null
     *
     * @ORM\Column(name="GiaBan", type="integer", nullable=true)
     */
    private $giaban;
    public function geGiaban()
    {
        return $this->giaban;
    }
    /**
     * @var int|null
     *
     * @ORM\Column(name="Thue", type="integer", nullable=true)
     */
    private $thue;
    public function getThue()
    {
        return $this->thue;
    }
    /**
     * @var string|null
     *
     * @ORM\Column(name="DonVI", type="string", length=45, nullable=true)
     */
    private $donvi;
    public function getDonvi()
    {
        return $this->donvi;
    }

}
