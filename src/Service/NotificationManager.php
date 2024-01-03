<?php
// src/Service/NotificationManager.php

namespace App\Service;

class NotificationManager
{
    private $notifications = [];

    public function __construct()
    {
        // Hàm khởi tạo (constructor) trống
    }

    public function addLowStockNotification(array $products): void
    {
        foreach ($products as $product) {
            $message = sprintf('Sản phẩm %s sắp hết hàng. Số lượng còn: %d', $product['TenThuoc'], $product['TonKhoHienTai']);
            $this->notifications[] = ['type' => 'low_stock', 'message' => $message];
        }
    }

    public function addOutOfStockNotification(array $products): void
    {
        foreach ($products as $product) {
            $message = sprintf('Sản phẩm %s đã hết hàng.', $product['TenThuoc']);
            $this->notifications[] = ['type' => 'out_of_stock', 'message' => $message];
        }
    }

    public function getNotifications(): array
    {
        return $this->notifications;
    }
}

?>