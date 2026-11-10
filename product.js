document.addEventListener("click", function (event) {
    const sizeButton = event.target.closest(".size-btn");

    if (!sizeButton) return;

    document.querySelectorAll(".size-btn").forEach(function (button) {
        button.classList.remove("active");
    });

    sizeButton.classList.add("active");

    const price = sizeButton.dataset.price;
    const sku = sizeButton.dataset.sku;
    const quantity = sizeButton.dataset.quantity;

    const priceElement = document.getElementById("product-price");
    const skuElement = document.getElementById("product-sku");

    if (priceElement && price) {
        priceElement.textContent = "R" + parseFloat(price).toFixed(2);
    }

    if (skuElement && sku) {
        skuElement.textContent = "SKU: " + sku;
    }

    sizeButton.dataset.selectedQuantity = quantity;
});


document.addEventListener("click", function (event) {
    const addButton = event.target.closest("#addToCartButton");

    if (!addButton) return;

    const selectedSize = document.querySelector(".size-btn.active");

    if (!selectedSize) {
        alert("Please select a size first.");
        return;
    }

    const variantId = selectedSize.dataset.variantId;
    const quantityInput = document.getElementById("qty-input");
    const quantity = parseInt(quantityInput.value, 10);

    if (!variantId || quantity < 1) {
        alert("Please select a valid size and quantity.");
        return;
    }

    const originalText = addButton.textContent;

    addButton.disabled = true;
    addButton.innerHTML =
        '<span class="spinner-border spinner-border-sm me-2"></span>Adding...';

    const formData = new FormData();
    formData.append("product_variant_id", variantId);
    formData.append("quantity", quantity);

    fetch("add-to-cart.php", {
        method: "POST",
        body: formData
    })
        .then(function (response) {
            return response.json();
        })
        .then(function (data) {
                if (data.success) {
                    addButton.innerHTML = "Go to Cart";

                    addButton.addEventListener("click", function () {
                        window.location.href = "cart.php";
                    });

                    const badge = document.getElementById("cartBadge");

                    if (badge) {
                        badge.textContent = data.cart_count;
                        badge.style.display = "inline-block";
                    }
                } else if (data.login_required) {
                    addButton.disabled = false;
                    addButton.textContent = originalText;

                    const accountModal = new bootstrap.Modal(
                        document.getElementById("accountModal")
                    );

                    accountModal.show();

                    alert(data.message);
                } else {
                    alert(data.message);
                    addButton.disabled = false;
                    addButton.textContent = originalText;
                }
            })
        .catch(function () {
            alert("Something went wrong. Please try again.");
            addButton.disabled = false;
            addButton.textContent = originalText;
        });
});