document.addEventListener("change", function (event) {
    const quantityInput = event.target.closest(".cart-quantity");

    if (!quantityInput) return;

    const cartItemId = quantityInput.dataset.cartItemId;
    const unitPrice = parseFloat(quantityInput.dataset.unitPrice);
    let quantity = parseInt(quantityInput.value, 10);

    if (isNaN(quantity) || quantity < 1) {
        quantity = 1;
        quantityInput.value = 1;
    }

    const formData = new FormData();
    formData.append("cart_item_id", cartItemId);
    formData.append("quantity", quantity);

    fetch("update-cart-item.php", {
        method: "POST",
        body: formData
    })
        .then(function (response) {
            return response.json();
        })
        .then(function (data) {
            if (!data.success) {
                alert("Could not update the quantity.");
                return;
            }

            // Update this item's line total
            const lineTotalElement = document.querySelector(
                `.item-line-total[data-cart-item-id="${cartItemId}"]`
            );

            if (lineTotalElement) {
                const lineTotal = unitPrice * quantity;
                lineTotalElement.textContent = lineTotal.toFixed(2);
            }

            // Recalculate the full cart total
            let cartTotal = 0;

            document.querySelectorAll(".cart-quantity").forEach(function (input) {
                const price = parseFloat(input.dataset.unitPrice);
                const qty = parseInt(input.value, 10) || 0;

                cartTotal += price * qty;
            });

            const cartTotalElement = document.getElementById("cartTotal");

            if (cartTotalElement) {
                cartTotalElement.textContent = cartTotal.toFixed(2);
            }
        })
        .catch(function (error) {
            console.error(error);
            alert("Something went wrong while updating the cart.");
        });
});

document.addEventListener("click", function (event) {
    const deleteButton = event.target.closest(".remove-cart-item");

    if (!deleteButton) return;

    const cartItemId = deleteButton.dataset.cartItemId;

    if (!cartItemId) {
        alert("Could not identify the cart item.");
        return;
    }

    if (!confirm("Remove this item from your cart?")) {
        return;
    }

    const formData = new FormData();
    formData.append("cart_item_id", cartItemId);

    fetch("remove-cart-item.php", {
        method: "POST",
        body: formData
    })
        .then(function (response) {
            return response.json();
        })
        .then(function (data) {
            if (!data.success) {
                alert(data.message || "Could not remove the item.");
                return;
            }

            // Remove the item row from the page
            const itemRow = deleteButton.closest(".cart-item-row");

            if (itemRow) {
                itemRow.remove();
            }

            updateCartTotals();
            updateCartBadge(data.cart_count);
        })
        .catch(function (error) {
            console.error(error);
            alert("Something went wrong while removing the item.");
        });
});