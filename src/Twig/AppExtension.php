<?php

// src/Twig/AppExtension.php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('format_vnd', [$this, 'formatVND']),
        ];
    }

    public function formatVND($value)
    {
        // Kiểm tra xem giá trị có phải là số không
        if (!is_numeric($value)) {
            return $value; // Trả về giá trị nguyên thủy nếu không phải là số
        }
    
        $suffix = '';
        if ($value >= 1000000) {
            $value /= 1000000;
            $suffix = 'M';
        } elseif ($value >= 1000) {
            $value /= 1000;
            $suffix = 'K';
        }
    
        // Đảm bảo rằng giá trị sau khi chia là một số
        if (!is_numeric($value)) {
            return $value; // Trả về giá trị nguyên thủy nếu không phải là số
        }
    
        // Loại bỏ chữ số thập phân và ký tự "." nếu chúng là "00"
        $formattedValue = number_format($value, 2);
        $formattedValue = rtrim($formattedValue, '0');
        $formattedValue = rtrim($formattedValue, '.');
    
        // Nếu giá trị là số nguyên, không hiển thị thập phân
        if (strpos($formattedValue, '.') === false) {
            $formattedValue = rtrim($formattedValue, '.');
        }
    
        return '$' . $formattedValue . $suffix;
    }
}
