<?php
// src/Controller/GiabanController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Connection;

class GetgiabanController extends AbstractController
{
    private $requestStack;
    
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }
    /**
     * @Route("/get-giaban", name="get_giaban")
     */
    public function getGiaBan(Connection $conn)
    {
        $request = $this->requestStack->getCurrentRequest();
        $idThuoc = $request->query->get('idThuoc');
        // Sử dụng Doctrine DBAL để truy vấn cơ sở dữ liệu
        $sql = "SELECT GiaBan FROM Thuoc WHERE idThuoc = :idThuoc";
        $result = $conn->executeQuery($sql, ['idThuoc' => $idThuoc]);

        if ($result->rowCount() > 0) {
            $row = $result->fetchAssociative();
            $giaBan = $row['GiaBan'];

            return new Response($giaBan);
        } else {
            return new Response("Không tìm thấy thông tin về giá bán.");
        }
    }
}