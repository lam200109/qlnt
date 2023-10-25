<?php

namespace AppBundle;

use Doctrine\ORM\Mapping as ORM;

/**
 * Nhomthuoc
 *
 * @ORM\Table(name="NhomThuoc")
 * @ORM\Entity
 */
class Nhomthuoc
{
    /**
     * @var int
     *
     * @ORM\Column(name="MaNhom", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $manhom;
    public function getManhom()
    {
        return $this->manhom;
    }
    /**
     * @var string|null
     *
     * @ORM\Column(name="TenNhom", type="string", length=45, nullable=true)
     */
    private $tennhom;
    public function getTennhom()
    {
        return $this->tennhom;
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
