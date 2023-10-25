// public/js/calculateTotal.js
function tinhTongTien() {
    var soLuongNhap = document.getElementById("so-luong-nhap").value;
    var giaNhap = document.getElementById("gia-nhap").value;
    var tongTien = soLuongNhap * giaNhap;
    document.getElementById("tong-tien-hoa-don").value = tongTien;
}

