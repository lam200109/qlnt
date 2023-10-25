<?php

namespace AppBundle;

use Doctrine\ORM\Mapping as ORM;

/**
 * Donvitinh
 *
 * @ORM\Table(name="DonViTinh")
 * @ORM\Entity
 */
class Donvitinh
{
    /**
     * @var int
     *
     * @ORM\Column(name="MaDVT", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $madvt;
    public function getMadvt()
    {
        return $this->madvt;
    }
    /**
     * @var string|null
     *
     * @ORM\Column(name="TenDVT", type="string", length=45, nullable=true)
     */
    private $tendvt;
    public function getTendvt()
    {
        return $this->tendvt;
    }

}
