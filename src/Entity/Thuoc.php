<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Thuoc
 *
 * @ORM\Table(name="Thuoc")
 * @ORM\Entity
 */
class Thuoc
{
    /**
     * @var int
     *
     * @ORM\Column(name="IDThuoc", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idThuoc;
    public function getIdThuoc()
    {
        return $this->idThuoc;
    }
    /**
     * @var int|null
     *
     * @ORM\Column(name="MaThuoc", type="integer", nullable=true)
     */
    private $MaThuoc;
    public function getMathuoc()
    {
        return $this->MaThuoc;
    }
    /**
     * @var string|null
     *
     * @ORM\Column(name="TenThuoc", type="string", length=255, nullable=true)
     */
    private $TenThuoc;
    public function getTenThuoc()
    {
        return $this->TenThuoc;
    }
    /**
     * @var int|null
     *
     * @ORM\Column(name="MaNhom", type="integer", nullable=true)
     */
    private $MaNhom;
    public function getMaNhom()
    {
        return $this->MaNhom;
    }
    /**
     * @var string|null
     *
     * @ORM\Column(name="NguonGoc", type="string", length=255, nullable=true)
     */
    private $NguonGoc;
    public function getNguonGoc()
    {
        return $this->NguonGoc;
    }
    /**
     * @var int|null
     *
     * @ORM\Column(name="MaNSX", type="integer", nullable=true)
     */
    private $MaNSX;
    public function getMaNSX()
    {
        return $this->MaNSX;
    }
    /**
     * @var int|null
     *
     * @ORM\Column(name="SoLuong", type="integer", nullable=true)
     */
    private $SoLuong;
    public function getSoLuong()
    {
        return $this->SoLuong;
    }
    /**
     * @var int|null
     *
     * @ORM\Column(name="GiaBan", type="integer", nullable=true)
     */
    private $GiaBan;
    public function getGiaBan()
    {
        return $this->GiaBan;
    }
    /**
     * @var string|null
     *
     * @ORM\Column(name="MaDVT", type="string", length=255, nullable=true)
     */
    private $MaDVT;
    public function getMaDVT()
    {
        return $this->MaDVT;
    }
    /**
     * @var string|null
     *
     * @ORM\Column(name="ThanhPhan", type="string", length=255, nullable=true)
     */
    private $ThanhPhan;
    public function getThanhPhan()
    {
        return $this->ThanhPhan;
    }
    /**
     * @var string|null
     *
     * @ORM\Column(name="HamLuong", type="string", length=255, nullable=true)
     */
    private $HamLuong;
    public function getHamLuong()
    {
        return $this->HamLuong;
    }
    /**
     * @var string|null
     *
     * @ORM\Column(name="CongDung", type="string", length=255, nullable=true)
     */
    private $CongDung;
    public function getCongDung()
    {
        return $this->CongDung;
    }
    /**
     * @var string|null
     *
     * @ORM\Column(name="PhanTacDung", type="string", length=255, nullable=true)
     */
    private $PhanTacDung;
    public function getPhanTacDung()
    {
        return $this->PhanTacDung;
    }
    /**
     * @var string|null
     *
     * @ORM\Column(name="CachDung", type="string", length=255, nullable=true)
     */
    private $CachDung;
    public function getCachDung()
    {
        return $this->CachDung;
    }
    /**
     * @var string|null
     *
     * @ORM\Column(name="ChuY", type="string", length=255, nullable=true)
     */
    private $ChuY;
    public function getChuY()
    {
        return $this->ChuY;
    }
    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="HanSuDung", type="datetime", nullable=true)
     */
    private $HanSuDung;
    public function getHanSuDung()
    {
        return $this->HanSuDung;
    }
    /**
     * @var string|null
     *
     * @ORM\Column(name="BaoQuan", type="string", length=255, nullable=true)
     */
    private $BaoQuan;
    public function getBaoQuan()
    {
        return $this->BaoQuan;
    }
    /**
     * @var string|null
     *
     * @ORM\Column(name="DangPhaChe", type="string", length=2555, nullable=true)
     */
    private $DangPhaChe;
    public function getDangPhaChe()
    {
        return $this->DangPhaChe;
    }

}
