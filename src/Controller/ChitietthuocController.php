<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;

class ChitietthuocController extends AbstractController
{
    #[Route('/chitietthuoc', name: 'chitietthuoc')]
    public function index($id, Connection $connection): Response
    {
           // Thực hiện truy vấn SQL để lấy thông tin của người dùng với ID tương ứng
           $sql = "SELECT * FROM Medicines
           WHERE Medicines.MedicineID = :id;
           ";
           $medicine = $connection->executeQuery($sql, ['id' => $id])->fetchAssociative();
   
           // Kiểm tra nếu không có người dùng với ID tương ứng
           if (!$medicine) {
               throw $this->createNotFoundException('Không tìm thấy thuốc với ID ' . $id);
           }
   
           // Truyền dữ liệu vào view
           return $this->render('chitietthuoc/index.html.twig', [
               'result' => $medicine,
           ]);
    }
}
