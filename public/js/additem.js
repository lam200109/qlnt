let cart = [];
let prices = []; // Mảng để lưu trữ giá của từng sản phẩm

function addToCart(productName, productPrice, inStock, productID) {
    if (inStock <= 0) {
        alert('Sản phẩm đã hết hàng!');
        return;
    }

    const existingProduct = cart.find(product => product.productID === productID);

    if (existingProduct) {
        if (existingProduct.quantity < inStock) {
            existingProduct.quantity++;
            existingProduct.total = parseFloat(productPrice) * existingProduct.quantity;
        } else {
            alert('Số lượng vượt quá số lượng còn trong kho!');
            return;
        }
    } else {
        const newProduct = {
            productID: productID,
            name: productName,
            price: productPrice,
            quantity: 1,
            total: parseFloat(productPrice)
        };

        cart.push(newProduct);
        prices.push(productPrice);

        // Cập nhật input hidden cho giá (Price[])
        updatePriceInputs();

        // Cập nhật input hidden cho số lượng (Quantity[])
        updateQuantityInputs();
    }

    // Hiển thị số lượng trong consol
    // Cập nhật giao diện giỏ hàng
    updateCartTable();
    updatePriceInputs();
    updateQuantityInputs();
    updateMedicineIDInputs();
}

// Hàm cập nhật input hidden cho MedicineID
function updateMedicineIDInputs() {
    const existingMedicineIDInputs = document.querySelectorAll('input[name="MedicineID[]"]');
    existingMedicineIDInputs.forEach(input => input.remove());

    cart.forEach((product, index) => {
        const medicineIDInput = document.createElement('input');
        medicineIDInput.type = 'hidden';
        medicineIDInput.name = 'MedicineID[]';

        // Giả sử 'productID' là thuộc tính chứa ID của loại thuốc
        const productID = product.productID; // Thay 'productID' bằng thuộc tính chứa ID thực tế

        medicineIDInput.value = productID;
        document.getElementById('themkhachhang').appendChild(medicineIDInput); // Thay 'themkhachhang' bằng id của container phù hợp
    });
}




// Hàm cập nhật input hidden cho giá
function updatePriceInputs() {
    const existingPriceInputs = document.querySelectorAll('input[name="Price[]"]');
    existingPriceInputs.forEach(input => input.remove());

    prices.forEach((price, index) => {
        const priceInput = document.createElement('input');
        priceInput.type = 'hidden';
        priceInput.name = 'Price[]';
        priceInput.value = price;
        document.getElementById('themkhachhang').appendChild(priceInput);
    });
}



// Hàm cập nhật input hidden cho số lượng
function updateQuantityInputs() {
    const existingQuantityInputs = document.querySelectorAll('input[name="Quantity[]"]');
    existingQuantityInputs.forEach(input => input.remove());

    cart.forEach((product, index) => {
        const quantityInput = document.createElement('input');
        quantityInput.type = 'hidden';
        quantityInput.name = 'Quantity[]';
        quantityInput.value = product.quantity;
        document.getElementById('themkhachhang').appendChild(quantityInput);
    });
}



function removeItem(index) {
    // Xóa sản phẩm khỏi giỏ hàng dựa vào chỉ số (index)
    cart.splice(index, 1);
    // Cập nhật bảng hiển thị giỏ hàng
    updateCartTable();
}

function updateCartTable() {
    const cartTable = document.getElementById('cartTable');
    const totalAmountCell = document.getElementById('totalAmount');
    const hiddenTotalAmountInput = document.querySelector('input[name="Amount"]');
  
    // Xóa toàn bộ dữ liệu cũ trong bảng
    while (cartTable.rows.length > 0) {
        cartTable.deleteRow(0);
    }

    // Hiển thị lại dữ liệu mới trong bảng
    let totalAmount = 0;
    cart.forEach((product, index) => {
        const newRow = cartTable.insertRow();
        const cell1 = newRow.insertCell(0);
        const cell2 = newRow.insertCell(1);
        const cell3 = newRow.insertCell(2);
        const cell4 = newRow.insertCell(3);
        const cell5 = newRow.insertCell(4);

        // Hiển thị hình ảnh (chưa có thông tin hình ảnh trong đoạn mã)
        cell1.innerHTML = `<img src="${product.image}" alt="${product.name}" style="width: 50px;">`;

        // Hiển thị các thông tin khác
        cell2.innerHTML = product.name;
        cell3.innerHTML = product.quantity;
        cell4.innerHTML = product.total.toFixed(2);
        cell5.innerHTML = `<button type="button" class="btn btn-danger" onclick="removeItem(${index})">Xóa</button>`;

        // Cập nhật tổng cộng
        totalAmount += product.total;
    });

    // Hiển thị tổng cộng
    totalAmountCell.textContent = totalAmount.toLocaleString('vi-VN', {
        style: 'currency',
        currency: 'VND'
    });
        hiddenTotalAmountInput.value = totalAmount.toFixed(2);
}










