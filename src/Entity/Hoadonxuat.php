<?php

namespace AppBundle;

use Doctrine\ORM\Mapping as ORM;

/**
 * Hoadonxuat
 *
 * @ORM\Table(name="HoaDonXuat")
 * @ORM\Entity
 */
class Hoadonxuat
{
    /**
     * @var int
     *
     * @ORM\Column(name="MaHDX", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $mahdx;
    public function getMahdx()
    {
        return $this->mahdx;
    }
    /**
     * @var int|null
     *
     * @ORM\Column(name="IDBN", type="integer", nullable=true)
     */
    private $idbn;
    public function getIdbn()
    {
        return $this->idbn;
    }
    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="NgayXuat", type="datetime", nullable=true)
     */
    private $ngayxuat;
    public function getNgayxuat()
    {
        return $this->ngayxuat;
    }
    /**
     * @var int|null
     *
     * @ORM\Column(name="TongTienThuoc", type="integer", nullable=true)
     */
    private $tongtienthuoc;
    public function getTongtienthuoc()
    {
        return $this->tongtienthuoc;
    }
    /**
     * @var int|null
     *
     * @ORM\Column(name="TongThue", type="integer", nullable=true)
     */
    private $tongthue;
    public function getTongthue()
    {
        return $this->tongthue;
    }
    /**
     * @var int|null
     *
     * @ORM\Column(name="TongTienHD", type="integer", nullable=true)
     */
    private $tongtienhd;
    public function getTongtienhd()
    {
        return $this->tongtienhd;
    }

}
