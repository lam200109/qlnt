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

class ThemthuocmoiController extends AbstractController
{
    /**
     * @Route("/themthuocmoi", name="themthuocmoi")
     */
    public function index(Connection $connection, Request $request, SessionInterface $session): Response
{
    $categoryQuery = "SELECT DISTINCT Category FROM Medicines";
    $categories = $connection->fetchAllAssociative($categoryQuery);

    // Kiểm tra nếu form hoặc file Excel đã được submit
    if ($request->isMethod('POST')) {
        // Xử lý file Excel nếu có
        $excelFile = $request->files->get('excelFile');
        
        if ($excelFile instanceof UploadedFile && $excelFile->isValid()) {
            // Nếu có file Excel, xử lý dữ liệu từ file
            $this->processExcelData($excelFile, $connection, $session);
            return $this->redirectToRoute('them_thuoc_moi');
        } else {
            // Nếu không có file Excel, xử lý dữ liệu từ form
            $this->processFormData($request, $connection, $session);
            return $this->redirectToRoute('them_thuoc_moi');
        }
    }

    // Nếu không có file Excel và không phải là form, render form nếu là method GET
    return $this->render('themthuocmoi/index.html.twig', [
        'categories' => $categories,
    ]);
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
        // Lấy dữ liệu từ form
        $name = $request->request->get('MedicineName');
        $generic = $request->request->get('GenericName');
        $SKU = $request->request->get('SKU');
        $concentration = $request->request->get('Concentration');
        $category = $request->request->get('Category');
        $manufacturer_price = $request->request->get('ManufacturerPrice');
        $expiration_date = $request->request->get('ExpirationDate');
        $status = $request->request->get('Status');
        $description = $request->request->get('Description');
        $price = 0;
        $instock = 0;
        $manufacturer_price = floatval($manufacturer_price);
        $formatted_date = date('Y-m-d', strtotime($expiration_date));
    
        // Kiểm tra xem ít nhất một trường dữ liệu đã được nhập
        if ($name || $generic || $SKU || $concentration || $category || $manufacturer_price || $expiration_date || $status || $description) {
            // Thực hiện xử lý dữ liệu, ví dụ: lưu vào cơ sở dữ liệu
            $sql = "
                INSERT INTO Medicines (Name, GenericName, SKU, Concentration, Category, Price, ManufacturerPrice, InStock, ExpirationDate, Description)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ";
    
            $connection->executeQuery($sql, [
                $name,
                $generic,
                $SKU,
                $concentration,
                $category,
                $price,
                $manufacturer_price,
                $instock,
                $formatted_date,
                $description,
            ]);
    
            // Lấy giá trị của MedicineID vừa được chèn
            $lastInsertedId = $connection->lastInsertId();
    
            // Xử lý tải lên hình ảnh
            $imageFile = $request->files->get('imageFile');
            $yourImageUploadDirectory = '/Users/nguyenlam/Documents/Symfony/qlnt/public/images';

            if ($imageFile instanceof UploadedFile) {
                // Xử lý file ảnh ở đây, ví dụ lưu vào thư mục và lưu đường dẫn vào cơ sở dữ liệu
                $imageFileName = md5(uniqid()) . '.' . $imageFile->guessExtension();
                $imageFile->move($yourImageUploadDirectory, $imageFileName);
                $imagePath = '/images/' . $imageFileName;

                // Lưu đường dẫn vào cơ sở dữ liệu
                $sqlUpdateImage = "UPDATE Medicines SET Image = ? WHERE MedicineID = ?";
                $connection->executeQuery($sqlUpdateImage, [$imagePath, $lastInsertedId]);
            }
    
            // Thêm flash message
            $this->addFlash('success', 'Thuốc từ form đã được thêm thành công!');
        } else {
            // Thông báo rằng ít nhất một trường dữ liệu cần được nhập
            $this->addFlash('warning', 'Ít nhất một trường dữ liệu cần được nhập từ form hoặc file Excel.');
        }
    }
    
    

    /**
     * Xử lý dữ liệu từ file Excel.
     *
     * @param UploadedFile $excelFile
     * @param Connection $connection
     * @param SessionInterface $session
     */
  // Xử lý dữ liệu từ file Excel
private function processExcelData(UploadedFile $excelFile, Connection $connection, SessionInterface $session): void
{
    $excelReader = IOFactory::createReaderForFile($excelFile);
    $excelObject = $excelReader->load($excelFile->getRealPath());
    $excelData = $excelObject->getActiveSheet()->toArray(null, true, true, true);

    // Tìm dòng chứa tiêu đề (label)
    $headerRow = $this->findHeaderRow($excelData);

    // Duyệt qua dữ liệu từ file Excel và thêm vào cơ sở dữ liệu
    foreach ($excelData as $row) {
        // Bỏ qua dòng tiêu đề
        if ($row === $headerRow) {
            continue;
        }

        $name = $row['A'] ?? null;
        $generic = $row['B'] ?? null;
        $SKU = $row['C'] ?? null;
        $concentration = $row['D'] ?? null;
        $category = $row['E'] ?? null;
        $manufacturer_price = $row['F'] ?? null;
        $expiration_date = $row['G'] ?? null;
        $status = $row['H'] ?? null;
        $description = $row['I'] ?? null;
        $price = 0;

        $instock = 0;

        // Thêm vào cơ sở dữ liệu
        $sql = "
            INSERT INTO Medicines (Name, GenericName, SKU, Concentration, Category, Price, ManufacturerPrice, InStock, ExpirationDate, Status, Description)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";
        $connection->executeQuery($sql, [
            $name,
            $generic,
            $SKU,
            $concentration,
            $category,
            $price,
            $manufacturer_price,
            $instock,
            $expiration_date,
            $status,
            $description,
        ]);
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
        return $row['A'] == 'Tên thuốc'
            && $row['B'] == 'Tên thường gọi'
            && $row['C'] == 'Mã thuốc'
            && $row['D'] == 'Hàm lượng'
            && $row['E'] == 'Danh mục'
            && $row['F'] == 'Giá của nhà sản xuất'  // Sửa đây
            && $row['G'] == 'Ngày hết hạn'
            && $row['H'] == 'Trạng thái'
            && $row['I'] == 'Mô tả thuốc';

    }

    /**
     * @Route("/download-excel-template", name="download_excel_template")
     */
    public function downloadExcelTemplate(): Response
    {
        // Tạo một đối tượng PhpSpreadsheet mới
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Đặt các dòng tiêu đề
        $labels = ['Tên thuốc', 'Tên thường gọi', 'Mã thuốc', 'Hàm lượng', 'Danh mục', 'Giá của nhà sản xuất', 'Ngày hết hạn', 'Trạng thái', 'Mô tả thuốc'];
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
