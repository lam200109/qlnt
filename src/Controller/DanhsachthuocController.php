<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;

class DanhsachthuocController extends AbstractController
{
    #[Route('/danh-sach-thuoc', name: 'danhsachthuoc')]
    public function index(Connection $connection): Response
    {
        
        $sql = "SELECT * FROM Medicines
        ";
        
        $rows = $connection->executeQuery($sql)->fetchAllAssociative();

  

        // Truyền dữ liệu vào view
        return $this->render('danhsachthuoc/index.html.twig', [
            'controller_name' => 'DanhsachthuocController',
            'result' => $rows,
        ]);
    }


     /**
     * @Route("/danh-sach-thuoc/medicine/edit/{id}", name="edit_medicine", methods={"GET", "POST"})
     */
    public function editMedicine(Connection $connection, Request $request, $id): Response
    {
        // Xử lý chức năng sửa dữ liệu ở đây
        if ($request->isMethod('POST')) {
            $medicineName = $request->request->get('medicineName');
            $medicineGenericName = $request->request->get('medicineGenericName');

            $medicineConcentration = $request->request->get('medicineConcentration');
            $medicineCategory = $request->request->get('medicineCategory');
            $medicinePrice = $request->request->get('medicinePrice');
            $medicineManufacturerPrice = $request->request->get('medicineManufacturerPrice');
            $medicineInStock = $request->request->get('medicineInStock');
            $medicineExpirationDate = $request->request->get('medicineExpirationDate');

            // Thực hiện truy vấn SQL để cập nhật dữ liệu trong CSDL
            $sql = "UPDATE Medicines 
                    SET Name = :name,GenericName = :genericname, Concentration = :concentration, Category = :category, 
                        Price = :price, ManufacturerPrice = :manufacturerPrice, InStock = :inStock, 
                        ExpirationDate = :expirationDate
                    WHERE MedicineID = :id";

            $params = [
                'id' => $id,
                'name' => $medicineName,
                'genericname' => $medicineGenericName,

                'concentration' => $medicineConcentration,
                'category' => $medicineCategory,
                'price' => $medicinePrice,
                'manufacturerPrice' => $medicineManufacturerPrice,
                'inStock' => $medicineInStock,
                'expirationDate' => $medicineExpirationDate,
            ];

            $connection->executeStatement($sql, $params);
            $this->addFlash('update', 'Đã sửa thông tin thuốc thành công');

            // Điều hướng sau khi sửa thành công
            return $this->redirectToRoute('danh_sach_thuoc');
        }
    }

    /**
     * @Route("/danh-sach-thuoc/medicine/delete/{id}", name="delete_medicine", methods={"DELETE"})
     */
    public function deleteMedicine($id, Connection $connection): Response
    {

        $sql = "DELETE FROM Medicines WHERE MedicineID = :id";
        
        
        $rows = $connection->executeQuery($sql, ['id' => $id]);
        $this->addFlash('success', 'Đã xoá thành công.');

    
        return $this->redirectToRoute('danh_sach_thuoc'); // Điều hướng sau khi xoá thành công
    }
}
