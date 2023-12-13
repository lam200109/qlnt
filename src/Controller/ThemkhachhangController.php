<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


class ThemkhachhangController extends AbstractController
{
    #[Route('/them-khach-hang', name: 'themkhachhang')]
    public function index(Request $request, Connection $connection): Response
    {
        $sql = "SELECT * FROM Medicines";
        $rows = $connection->executeQuery($sql)->fetchAllAssociative();
    
        if ($request->isMethod('POST')) {
            $HoTen = $request->request->get('Name');
            $DienThoai = $request->request->get('Phone');
            $TongTienHD = $request->request->get('TongTienHD');
            $NgayLap = $request->request->get('Date');
            $GhiChu = $request->request->get('Description');
            $SoLuongXuat = $request->request->get('SoLuongXuat');
            $GiaBan = $request->request->get('GiaBan');
    
            try {
                // Bắt đầu giao dịch
                $connection->beginTransaction();
    
                // Thêm thông tin bệnh nhân
                $benhnhan = [
                    'HoTen' => $HoTen,
                    'DienThoai' => $DienThoai,
                ];
                $connection->insert('BenhNhan', $benhnhan);
    
                $IDBN = $connection->lastInsertId();
    
                // Thêm thông tin hóa đơn xuất
                $hoadon = [
                    'IDBN' => $IDBN,
                    'NgayLap' => $NgayLap,
                    'GhiChu' => $GhiChu,
                ];
                $connection->insert('HoaDonXuat', $hoadon);
    
                $MaHDX = $connection->lastInsertId();
    
                // Cập nhật tổng tiền
                $connection->update('HoaDonXuat', ['TongTienHD' => $TongTienHD], ['MaHDX' => $MaHDX]);
    
                // Lặp qua từng mục trong giỏ hàng và thêm vào bảng chi tiết hóa đơn xuất
                for ($i = 0; $i < count($idThuoc); $i++) {
                    $currentIdThuoc = $idThuoc[$i];
                    $currentSoLuongXuat = $SoLuongXuat[$i];
    
                    // Lấy số lượng tồn kho của sản phẩm
                    $SoLuongTonKho = $connection->fetchOne('SELECT SoLuong FROM Thuoc WHERE idThuoc = ?', [$currentIdThuoc]);
    
                    if ($currentSoLuongXuat > $SoLuongTonKho) {
                        // Hủy giao dịch nếu số lượng không đủ
                        $connection->rollBack();
                        return $this->redirectToRoute('your_error_route');
                    } else {
                        // Thêm vào bảng chi tiết hóa đơn xuất
                        $chitiethoadon = [
                            'MaHDX' => $MaHDX,
                            'idThuoc' => $currentIdThuoc,
                            'SoLuongXuat' => $currentSoLuongXuat,
                            'GiaBan' => $GiaBan[$i],
                        ];
                        $connection->insert('ChiTietHoaDonXuat', $chitiethoadon);
    
                        // Cập nhật số lượng tồn kho
                        $connection->update('Thuoc', ['SoLuong' => $SoLuongTonKho - $currentSoLuongXuat], ['idThuoc' => $currentIdThuoc]);
                    }
                }
    
                // Hoàn tất giao dịch
                $connection->commit();
                $this->addFlash('success', 'Thêm khách hàng và giao dịch thành công!');

                return $this->redirectToRoute('them_khach_hang');
            } catch (\Exception $e) {
                // Nếu có lỗi, hủy bỏ giao dịch và xử lý lỗi
                $connection->rollBack();
                $this->addFlash('error', 'Có lỗi xảy ra trong quá trình xử lý giao dịch.');

                return $this->redirectToRoute('them_khach_hang');
            }
        }
    
        return $this->render('themkhachhang/index.html.twig', [
            'controller_name' => 'ThemkhachhangController',
            'result' => $rows,
        ]);
    }
    
        
        #[Route('/search-medicine', name: 'search_medicine', methods: ['POST'])]
        public function searchMedicine(Request $request, Connection $connection): Response
        {
            $searchTerm = '%' . $request->request->get('searchTerm') . '%';

            $sql = "SELECT * FROM Medicines WHERE Name LIKE ?";
            $rows = $connection->executeQuery($sql, [$searchTerm])->fetchAllAssociative();

            return $this->json(['result' => $rows]);
        }






        /**
     * @Route("/them-khach-hang", name="them_khach_hang_momo")
     */
    public function momopayment(): Response
    {
        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
        $partnerCode = 'MOMOBKUN20180529';
        $accessKey = 'klm05TvNBzhg7h7j';
        $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
        $orderInfo = "Thanh toán qua MoMo";
        $amount = "25000";
        $orderId = time() . "";
        $redirectUrl = "http://localhost/cnpm/banhang.php?success=1";
        $ipnUrl = "https://webhook.site/b3088a6a-2d17-4f8d-a383-71389a6c600b";
        $extraData = "";

        $requestId = time() . "";
        $requestType = "captureWallet";
        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        $data = [
            'partnerCode' => $partnerCode,
            'partnerName' => "Test",
            "storeId" => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        ];

        $result = $this->execPostRequest($endpoint, json_encode($data));
        $jsonResult = json_decode($result, true);

        return $this->redirect($jsonResult['payUrl']);
    }

    private function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
