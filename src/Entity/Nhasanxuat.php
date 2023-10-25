<?php

namespace AppBundle;

use Doctrine\ORM\Mapping as ORM;

/**
 * Nhasanxuat
 *
 * @ORM\Table(name="NhaSanXuat")
 * @ORM\Entity
 */
class Nhasanxuat
{
    /**
     * @var int
     *
     * @ORM\Column(name="MaNSX", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $mansx;
    public function getMansx()
    {
        return $this->mansx;
    }
    /**
     * @var string|null
     *
     * @ORM\Column(name="TenNSX", type="string", length=45, nullable=true)
     */
    private $tennsx;
    public function getTennsx()
    {
        return $this->tennsx;
    }
    /**
     * @var string|null
     *
     * @ORM\Column(name="DiaChi", type="string", length=255, nullable=true)
     */
    private $diachi;
    public function getDiachi()
    {
        return $this->diachi;
    }
    /**
     * @var int|null
     *
     * @ORM\Column(name="SoDienThoai", type="integer", nullable=true)
     */
    private $sodienthoai;
    public function getSodienthoai()
    {
        return $this->sodienthoai;
    }
    /**
     * @var int|null
     *
     * @ORM\Column(name="Fax", type="integer", nullable=true)
     */
    private $fax;
    public function getFax()
    {
        return $this->fax;
    }
    /**
     * @var string|null
     *
     * @ORM\Column(name="Email", type="string", length=45, nullable=true)
     */
    private $email;
    public function getEmail()
    {
        return $this->email;
    }
    /**
     * @var string|null
     *
     * @ORM\Column(name="GhiChu", type="string", length=255, nullable=true)
     */
    private $ghichu;
    public function getGhichu()
    {
        return $this->ghichu;
    }

}
