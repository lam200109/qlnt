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
    #[Route('/them-khach-hang', name: 'themkhachhang', methods: ['GET', 'POST'])]
    public function index(Request $request, Connection $connection): Response
    {
        $sql = "SELECT * FROM Medicines";
        $rows = $connection->executeQuery($sql)->fetchAllAssociative();

        // Xử lý dữ liệu từ form nếu form được submit
        if ($request->isMethod('POST')) {
            $allData = $request->request->all();
            $prices = $allData['Price'] ?? [];
            $quantities = $allData['Quantity'] ?? [];
            $incometype = 'Mua thuốc';

            // Lặp qua mảng giá trị Price để chuyển đổi thành số decimal
            foreach ($prices as &$price) {
                $price = is_numeric($price) ? (float) $price : 0;
                if (!is_scalar($price)) {
                    var_dump($price);
                }
            }

            // Tạo và lưu đối tượng Customer
            $sqlInsertCustomer = "INSERT INTO Customers (Name, Phone, Address) VALUES (:name, :phone, :address)";
            $connection->executeStatement($sqlInsertCustomer, [
                'name' => $allData['Name'] ?? '',
                'phone' => $allData['Phone'] ?? '',
                'address' => $allData['Address'] ?? '',
            ]);

            // Lấy ID của Customer vừa được thêm
            $customerID = $connection->lastInsertId();

            // Tạo và lưu đối tượng SalesInvoice
            $sqlInsertSalesInvoice = "INSERT INTO SalesInvoices (CustomerID, Date, Amount, IncomeType) VALUES (:customerID, :date, :amount, :incometype)";
            $connection->executeStatement($sqlInsertSalesInvoice, [
                'customerID' => $customerID,
                'date' => $allData['Date'] ?? '',
                'amount' => $allData['Amount'] ?? 0,
                'incometype' => $incometype,
            ]);

            // Lấy ID của SalesInvoice vừa được thêm
            $salesInvoiceID = $connection->lastInsertId();

            // Tạo và lưu đối tượng SalesInvoiceDetail
            foreach ($prices as $index => $price) {
                $quantity = $quantities[$index] ?? 0;
                $total = $price * $quantity;
                $medicineID = $_POST['MedicineID'][$index] ?? 0;

                $sqlInsertSalesInvoiceDetail = "INSERT INTO SalesInvoiceDetails (SalesInvoiceID, MedicineID, Price, Quantity, Total) VALUES (:salesInvoiceID, :medicineID, :price, :quantity, :total)";
                $connection->executeStatement($sqlInsertSalesInvoiceDetail, [
                    'salesInvoiceID' => $salesInvoiceID,
                    'medicineID' => $medicineID,
                    'price' => $price,
                    'quantity' => $quantity,
                    'total' => $total,
                ]);
            }
            // Trừ InStock từ bảng Medicines
         // Trừ InStock từ bảng Medicines
foreach ($prices as $index => $price) {
    $quantity = $quantities[$index] ?? 0;
    
    // Lấy MedicineID của sản phẩm từ cart (nếu có)
    $medicineID = $_POST['MedicineID'][$index] ?? 0;

    $sqlUpdateStock = "UPDATE Medicines SET InStock = InStock - :quantity WHERE MedicineID = :medicineID";
    $connection->executeStatement($sqlUpdateStock, [
        'quantity' => $quantity,
        'medicineID' => $medicineID,
    ]);

}

            $this->addFlash('success', 'Tạo hoá đơn thành công!');


            // Hiển thị trang sau khi đã xử lý hết tất cả các sản phẩm
            return $this->render('themkhachhang/index.html.twig', [
                'controller_name' => 'ThemkhachhangController',
                'result' => $rows,
            ]);
        }

        // Hiển thị trang với dữ liệu ban đầu
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





          /**
     * @Route("/them-khach-hang", name="them_khach_hang_vpay")
     */
    public function vnpay(): Response
    {
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = "http://127.0.0.1:8000/thanks";
        $vnp_TmnCode = "4MBZ5WF5";//Mã website tại VNPAY 
        $vnp_HashSecret = "JBSGPKLPWNTFZXZCYEERXASJGFEPZXDW"; //Chuỗi bí mật
        
        $vnp_TxnRef = rand(00,9999); //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
        $vnp_OrderInfo = 'Nội dung thanh toán';
        $vnp_OrderType = 'Bill payment';
        $vnp_Amount = 10000 * 100;
        $vnp_Locale = 'vn';
        $vnp_BankCode = 'TCB';
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
        //Add Params of 2.0.1 Version
        // $vnp_ExpireDate = $_POST['txtexpire'];
        //Billing
        // $vnp_Bill_Mobile = $_POST['txt_billing_mobile'];
        // $vnp_Bill_Email = $_POST['txt_billing_email'];
        // $fullName = trim($_POST['txt_billing_fullname']);
        // if (isset($fullName) && trim($fullName) != '') {
        //     $name = explode(' ', $fullName);
        //     $vnp_Bill_FirstName = array_shift($name);
        //     $vnp_Bill_LastName = array_pop($name);
        // }
        // $vnp_Bill_Address=$_POST['txt_inv_addr1'];
        // $vnp_Bill_City=$_POST['txt_bill_city'];
        // $vnp_Bill_Country=$_POST['txt_bill_country'];
        // $vnp_Bill_State=$_POST['txt_bill_state'];
        // // Invoice
        // $vnp_Inv_Phone=$_POST['txt_inv_mobile'];
        // $vnp_Inv_Email=$_POST['txt_inv_email'];
        // $vnp_Inv_Customer=$_POST['txt_inv_customer'];
        // $vnp_Inv_Address=$_POST['txt_inv_addr1'];
        // $vnp_Inv_Company=$_POST['txt_inv_company'];
        // $vnp_Inv_Taxcode=$_POST['txt_inv_taxcode'];
        // $vnp_Inv_Type=$_POST['cbo_inv_type'];
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            // "vnp_ExpireDate"=>$vnp_ExpireDate,
            // "vnp_Bill_Mobile"=>$vnp_Bill_Mobile,
            // "vnp_Bill_Email"=>$vnp_Bill_Email,
            // "vnp_Bill_FirstName"=>$vnp_Bill_FirstName,
            // "vnp_Bill_LastName"=>$vnp_Bill_LastName,
            // "vnp_Bill_Address"=>$vnp_Bill_Address,
            // "vnp_Bill_City"=>$vnp_Bill_City,
            // "vnp_Bill_Country"=>$vnp_Bill_Country,
            // "vnp_Inv_Phone"=>$vnp_Inv_Phone,
            // "vnp_Inv_Email"=>$vnp_Inv_Email,
            // "vnp_Inv_Customer"=>$vnp_Inv_Customer,
            // "vnp_Inv_Address"=>$vnp_Inv_Address,
            // "vnp_Inv_Company"=>$vnp_Inv_Company,
            // "vnp_Inv_Taxcode"=>$vnp_Inv_Taxcode,
            // "vnp_Inv_Type"=>$vnp_Inv_Type
        );
        
        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        // if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
        //     $inputData['vnp_Bill_State'] = $vnp_Bill_State;
        // }
        
        //var_dump($inputData);
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        
        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);//  
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        $returnData = array('code' => '00'
            , 'message' => 'success'
            , 'data' => $vnp_Url);
            if (isset($_POST['redirect'])) {
                header('Location: ' . $vnp_Url);
                die();
            } else {
                echo json_encode($returnData);
            }
            // vui lòng tham khảo thêm tại code demo
            return new Response('Thanh toán thành công');

    }

}
