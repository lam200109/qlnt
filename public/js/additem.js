let cart = [];
let prices = []; // Mảng để lưu trữ giá của từng sản phẩm

function addToCart(productName, productPrice, inStock) {
    if (inStock <= 0) {
        alert('Sản phẩm đã hết hàng!');
        return;
    }
    
    const existingProduct = cart.find(product => product.name === productName);

    const quantity = existingProduct ? existingProduct.quantity + 1 : 1;
    const total = parseFloat(productPrice) * quantity;

    if (quantity <= inStock) {
        if (existingProduct) {
            existingProduct.quantity++;
            existingProduct.total = parseFloat(productPrice) * existingProduct.quantity;
        } else {
            const newProduct = {
                name: productName,
                price: productPrice,
                quantity: quantity,
                total: total
            };

            cart.push(newProduct);
            prices.push(productPrice); // Thêm giá của sản phẩm vào mảng giá

            // Xóa toàn bộ input hidden có tên "Price[]" để cập nhật lại

            // Thêm lại input hidden cho mỗi giá sản phẩm trong giỏ hàng
            prices.forEach((price, index) => {
                const priceInput = document.createElement('input');
                priceInput.type = 'hidden';
                priceInput.name = 'Price[]';
                priceInput.value = price;
                document.body.appendChild(priceInput);
            });

          
        }
    } else {
        alert('Số lượng vượt quá số lượng còn trong kho!');
    }

    updateCartTable();
    updatePriceInputs();

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
    const hiddenTotalAmountInput = document.querySelector('input[name="TongTienHD"]'); // Sử dụng querySelector để chọn element theo attribute
 const hiddenPriceInputs = document.querySelectorAll('input[name="Price[]"]');
    const hiddenQuantityInputs = document.querySelectorAll('input[name="SoLuongXuat[]"]');
    const tfootElement = cartTable.querySelector('tfoot');
    // Xóa toàn bộ dữ liệu cũ trong bảng
    cartTable.innerHTML = '';

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
    totalAmountCell.textContent = totalAmount.toFixed(2);
    hiddenTotalAmountInput.value = totalAmount.toFixed(2);
}
