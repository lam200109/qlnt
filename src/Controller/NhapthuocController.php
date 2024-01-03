<?php

namespace App\Controller;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class NhapthuocController extends AbstractController
{
     /**
     * @Route("/nhap-thuoc", name="nhapthuoc")
     */
    public function index(Connection $connection, Request $request, SessionInterface $session): Response
    {

        $distributorQuery = "SELECT * FROM Distributors";
        $userQuery = "SELECT * FROM Users";
        $medicineQuery = "SELECT Name, MAX(MedicineID) AS MedicineID FROM Medicines GROUP BY Name";

        $distributors = $connection->fetchAllAssociative($distributorQuery);
        $users = $connection->fetchAllAssociative($userQuery);
        $medicines = $connection->fetchAllAssociative($medicineQuery);

        if ($request->isMethod('POST')) {
            // Xử lý file Excel nếu có
            $excelFile = $request->files->get('excelFile');
            
            if ($excelFile instanceof UploadedFile && $excelFile->isValid()) {
                // Nếu có file Excel, xử lý dữ liệu từ file
                $this->processExcelData($excelFile, $connection, $session);
                return $this->redirectToRoute('nhap_thuoc');
            } else {
                // Nếu không có file Excel, xử lý dữ liệu từ form
                $this->processFormData($request, $connection, $session);
                return $this->redirectToRoute('nhap_thuoc');
            }
        }



        return $this->render('nhapthuoc/index.html.twig', [
            'distributors' => $distributors,
            'users' => $users,
            'medicines' => $medicines,


        ]);
    }


    #[Route('/get-gia-thuoc/{MedicineID}', name: 'get_gia_thuoc', methods: ['GET'])]
    public function getGiaThuoc(Connection $connection, $MedicineID): Response
    {
        $sql = "SELECT Price FROM Medicines WHERE MedicineID = :MedicineID";
        $result = $connection->executeQuery($sql, ['MedicineID' => $MedicineID]);

        $data = [];

        if ($result->rowCount() > 0) {
            $row = $result->fetchAssociative();
            $data['price'] = $row['Price'];
        } else {
            $data['error'] = 'Không tìm thấy thông tin thuốc';
        }

        return $this->json($data);
    }

      /**
     * Xử lý dữ liệu từ form.
     *
     * @param Request $request
     * @param Connection $connection
     * @param SessionInterface $session
     */
    private function processFormData(Request $request, Connection $connection, SessionInterface $session): void
    {
        $distributor = $request->request->get('DistributorID');
        $selectedMedicine = $request->request->get('MedicineID');  // Giả sử giá trị chọn từ dropdown có cả MedicineID và Name
        $quantity = $request->request->get('Quantity');
        $price = $request->request->get('Price');
        $amount = $request->request->get('Amount');
        $date = $request->request->get('Date');
        $lotnumber = $request->request->get('LotNumber');
        
        // Giải mã giá trị được chọn từ dropdown (chứa cả MedicineID và Name)
        list($medicineid, $name) = explode('|', $selectedMedicine);
        
        $expensetype = 'Nhập hàng';
        
        // Insert into PurchaseInvoices
        $sqlInsertPurchaseInvoices = "INSERT INTO PurchaseInvoices (DistributorID, ExpenseType, Amount, Date) VALUES (?, ?, ?, ?)";
        $paramsInsertPurchaseInvoices = [$distributor, $expensetype, $amount, $date];
        $connection->executeQuery($sqlInsertPurchaseInvoices, $paramsInsertPurchaseInvoices);
    
        // Get the last inserted PurchaseInvoiceID
        $lastInsertedPurchaseInvoiceId = $connection->lastInsertId();
    
        // Update PurchaseInvoiceID
        $sqlUpdatePurchaseinvoices = "UPDATE PurchaseInvoices SET PurchaseInvoiceID = ? WHERE PurchaseInvoiceID = ?";
        $connection->executeQuery($sqlUpdatePurchaseinvoices, [$lastInsertedPurchaseInvoiceId, $lastInsertedPurchaseInvoiceId]);
    
        // Check if the lot exists for the given MedicineID
        $sqlCheckLot = "SELECT COUNT(*) as count FROM Medicines WHERE MedicineID = ? AND LotNumber = ?";
        $paramsCheckLot = [$medicineid, $lotnumber];
        $resultCheckLot = $connection->fetchAssociative($sqlCheckLot, $paramsCheckLot);
    
        if ($resultCheckLot['count'] > 0) {
            // If the lot exists, update
            $sqlUpdateMedicines = "UPDATE Medicines SET InStock = InStock + ? WHERE MedicineID = ? AND LotNumber = ?";
            $paramsUpdateMedicines = [$quantity, $medicineid, $lotnumber];
    
            if ($connection->executeQuery($sqlUpdateMedicines, $paramsUpdateMedicines)) {
                $this->addFlash('success', 'Cập nhật số lượng thuốc thành công');
            } else {
                $this->addFlash('error', 'Cập nhật số lượng thuốc không thành công');
            }
        } else {
            // If the lot does not exist, insert
            $sqlInsertPurchaseInvoiceDetails = "INSERT INTO PurchaseInvoiceDetails (PurchaseInvoiceID, MedicineID, Quantity, Price) VALUES (?, ?, ?, ?)";
            $paramsInsertPurchaseInvoiceDetails = [$lastInsertedPurchaseInvoiceId, $medicineid, $quantity, $price];
    
            if ($connection->executeQuery($sqlInsertPurchaseInvoiceDetails, $paramsInsertPurchaseInvoiceDetails)) {
                $sqlInsertMedicines = "INSERT INTO Medicines (Name,  LotNumber, InStock) VALUES (?, ?, ?)";
                $paramsInsertMedicines = [$name, $lotnumber, $quantity];
    
                if ($connection->executeQuery($sqlInsertMedicines, $paramsInsertMedicines)) {
                    $this->addFlash('success', 'Nhập kho thành công');
                } else {
                    $this->addFlash('error', 'Thêm mới thuốc không thành công');
                }
            } else {
                $this->addFlash('error', 'Thêm chi tiết hoá đơn nhập không thành công');
            }
        }
    }
    


     /**
     * Xử lý dữ liệu từ file Excel.
     *
     * @param UploadedFile $excelFile
     * @param Connection $connection
     * @param SessionInterface $session
     */


private function processExcelData(UploadedFile $excelFile, Connection $connection, SessionInterface $session): void
{
    $excelReader = IOFactory::createReaderForFile($excelFile);
    $excelObject = $excelReader->load($excelFile->getRealPath());
    $excelData = $excelObject->getActiveSheet()->toArray(null, true, true, true);

    // Tìm dòng chứa tiêu đề (label)
    $headerRow = $this->findHeaderRow($excelData);

    // Kiểm tra xem dòng tiêu đề có đúng không
    if (!$this->isHeaderRow($headerRow)) {
        $this->addFlash('error', 'Dữ liệu từ file Excel không đúng định dạng.');
        return;
    }

    // Duyệt qua dữ liệu từ file Excel và thêm vào cơ sở dữ liệu
    foreach ($excelData as $row) {
        // Bỏ qua dòng tiêu đề
        if ($row === $headerRow) {
            continue;
        }

        $distributor = $row['A'] ?? null;
        $medicineid = $row['B'] ?? null;
        $quantity = $row['C'] ?? null;
        $price = $row['D'] ?? null;
        $amount = $row['E'] ?? null;
        $date = $row['F'] ?? null;

        // Thêm vào cơ sở dữ liệu
        $expensetype = 'Nhập hàng';

        $sql = "INSERT INTO PurchaseInvoices (DistributorID, ExpenseType, Amount, Date) 
                VALUES (?, ?, ?, ?)";

        $params = [$distributor, $expensetype, $amount, $date];
        $connection->executeQuery($sql, $params);

        // Lấy giá trị của PurchaseInvoiceID vừa được chèn
        $lastInsertedPurchaseInvoiceId = $connection->lastInsertId();

        // Cập nhật PurchaseInvoiceID
        $sqlUpdatePurchaseinvoices = "UPDATE PurchaseInvoices SET PurchaseInvoiceID = ? WHERE PurchaseInvoiceID = ?";
        $connection->executeQuery($sqlUpdatePurchaseinvoices, [$lastInsertedPurchaseInvoiceId, $lastInsertedPurchaseInvoiceId]);

        $sql4 = "SELECT Price FROM Medicines WHERE MedicineID = ?";
        $result = $connection->fetchAssociative($sql4, [$medicineid]);
        $pricemedicine = $result['Price'];

        if ($price <= $pricemedicine) {
            $sql2 = "INSERT INTO PurchaseInvoiceDetails (PurchaseInvoiceID, MedicineID, Quantity, Price)
                    VALUES (?, ?, ?, ?)";
            $params2 = [$lastInsertedPurchaseInvoiceId, $medicineid, $quantity, $price];

            if ($connection->executeQuery($sql2, $params2)) {
                $sql3 = "UPDATE Medicines SET InStock = InStock + ? WHERE MedicineID = ?";
                $params3 = [$quantity, $medicineid];

                if ($connection->executeQuery($sql3, $params3)) {
                    $this->addFlash('success', 'Nhập kho thành công');
                } else {
                    $this->addFlash('error', 'Cập nhật số lượng thuốc không thành công');
                }
            } else {
                $this->addFlash('error', 'Thêm chi tiết hoá đơn nhập không thành công');
            }
        } else {
            $this->addFlash('error', 'Giá nhập phải nhỏ hơn hoặc bằng giá bán!');
        }
    }

    // Thêm flash message sau khi xử lý toàn bộ file Excel
    $this->addFlash('success', 'Dữ liệu từ file Excel đã được thêm thành công!');
}

  


    /**
     * Tìm dòng chứa tiêu đề (label) trong dữ liệu từ file Excel.
     *
     * @param array $excelData
     * @return array|null
     */
    private function findHeaderRow(array $excelData): ?array
    {
        foreach ($excelData as $row) {
            if ($this->isHeaderRow($row)) {
                return $row;
            }
        }

        return null;
    }

    /**
     * Kiểm tra xem một dòng có phải là dòng chứa tiêu đề (label) hay không.
     *
     * @param array $row
     * @return bool
     */
    private function isHeaderRow(array $row): bool
    {
        return $row['A'] == 'Mã nhà phân phối'
            && $row['B'] == 'Tên thuốc'
            && $row['C'] == 'Số lượng nhập'
            && $row['D'] == 'Giá nhập'  
            && $row['E'] == 'Tổng hoá đơn'
            && $row['F'] == 'Ngày nhập';
    }

    /**
     * @Route("/download-excel-nhapthuoc", name="download_excel_nhapthuoc")
     */
    public function downloadExcelTemplate(): Response
    {
        // Tạo một đối tượng PhpSpreadsheet mới
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Đặt các dòng tiêu đề
        $labels = ['Mã nhà phân phối', 'Tên thuốc', 'Số lượng nhập', 'Giá nhập', 'Tổng hoá đơn', 'Ngày nhập'];
        $sheet->fromArray([$labels], null, 'A1');

        // Tạo một Response trả về file Excel
        $response = new Response();
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'Dulieumau.xlsx'
        );
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', $dispositionHeader);
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');

        return $response;
    }
}
