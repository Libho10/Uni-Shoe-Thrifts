document.addEventListener("DOMContentLoaded", function () {

    const termsCheckbox = document.getElementById("termsCheckbox");
    const paypalFallbackButton = document.getElementById("paypalFallbackButton");
    const totalText = document.getElementById("totalChargedText");

    if (!termsCheckbox || !paypalFallbackButton) {
        console.error("Payment page elements were not found.");
        return;
    }

    termsCheckbox.addEventListener("change", function () {
        paypalFallbackButton.disabled = !termsCheckbox.checked;
    });

    paypalFallbackButton.addEventListener("click", function () {
        if (!termsCheckbox.checked) {
            alert("Please agree to the terms before paying.");
            return;
        }

        alert("PayPal Sandbox payment would start here.");
    });

    // PayPal Buttons will be added once you have your Sandbox client ID
    if (window.paypal) {
        paypal.Buttons({
            style: {
                layout: "vertical",
                color: "gold",
                shape: "rect",
                label: "paypal"
            },

            createOrder: function (data, actions) {
                return fetch("create-paypal-order.php", {
                    method: "POST"
                })
                    .then(function (response) {
                        return response.json();
                    })
                    .then(function (data) {
                        if (data.success) {
                            return data.order_id;
                        }

                        throw new Error(data.message || "Could not create PayPal order.");
                    });
            },

            onApprove: function (data, actions) {
                return fetch("capture-paypal-order.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        order_id: data.orderID
                    })
                })
                    .then(function (response) {
                        return response.json();
                    })
                    .then(function (data) {
                        if (data.success) {
                            window.location.href = "order-confirmation.php";
                        } else {
                            alert(data.message || "Payment could not be completed.");
                        }
                    });
            },

            onError: function (error) {
                console.error(error);
                alert("PayPal payment failed. Please try again.");
            }

        }).render("#paypal-button-container");
    }
});