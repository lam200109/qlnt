    // Mảng để lưu trữ sản phẩm trong giỏ hàng
    let cart = [];

    function addToCart(productName, productPrice) {
        // Kiểm tra xem sản phẩm đã tồn tại trong giỏ hàng chưa
        const existingProduct = cart.find(product => product.name === productName);

        if (existingProduct) {
            // Nếu sản phẩm đã tồn tại, tăng số lượng
            existingProduct.quantity++;
            existingProduct.total = parseFloat(productPrice) * existingProduct.quantity;
        } else {
            // Nếu sản phẩm chưa tồn tại, thêm mới vào giỏ hàng
            const quantity = 1;
            const total = parseFloat(productPrice) * quantity;
            cart.push({
                name: productName,
                price: productPrice,
                quantity: quantity,
                total: total
            });
        }

        // Cập nhật bảng hiển thị giỏ hàng
        updateCartTable();
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

            cell1.innerHTML = product.name;
            cell2.innerHTML = product.quantity;
            cell3.innerHTML = product.total.toFixed(2);
            cell4.innerHTML = `<button type="button" class="btn btn-danger" onclick="removeItem(${index})">Xóa</button>`;

            // Cập nhật tổng cộng
            totalAmount += product.total;
        });

        // Hiển thị tổng cộng
        totalAmountCell.textContent = totalAmount.toFixed(2);
    }


  