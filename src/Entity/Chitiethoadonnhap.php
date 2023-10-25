<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Chitiethoadonnhap
 *
 * @ORM\Table(name="ChiTietHoaDonNhap")
 * @ORM\Entity
 */
class Chitiethoadonnhap
{
    /**
     * @var int
     *
     * @ORM\Column(name="MaCTHDN", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $MaCTHDN;
    public function getMaCTHDN()
    {
        return $this->MaCTHDN;
    }
    /**
     * @var int|null
     *
     * @ORM\Column(name="MaHDN", type="integer", nullable=true)
     */
    private $MaHDN;
    public function getMaHDN()
{
    return $this->MaHDN;
}

    /**
     * @var int|null
     *
     * @ORM\Column(name="IDThuoc", type="integer", nullable=true)
     */
    private $idThuoc;
    public function getIdThuoc()
{
    return $this->idThuoc;
}

    /**
     * @var int|null
     *
     * @ORM\Column(name="SoLuongNhap", type="integer", nullable=true)
     */
    private $SoLuongNhap;
    public function getSoLuongNhap()
{
    return $this->SoLuongNhap;
}

    /**
     * @var int|null
     *
     * @ORM\Column(name="GiaNhap", type="integer", nullable=true)
     */
    private $GiaNhap;
    public function getGiaNhap()
{
    return $this->GiaNhap;
}


}
