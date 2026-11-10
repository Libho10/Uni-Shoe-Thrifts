document.addEventListener("DOMContentLoaded", function () {
    console.log("review-page.js loaded");

    const collectButton = document.getElementById("collectButton");
    const deliverButton = document.getElementById("deliverButton");
    const collectionBlock = document.getElementById("collectionBlock");
    const deliveryBlock = document.getElementById("deliveryBlock");
    const deliveryFeeText = document.getElementById("deliveryFeeText");
    const totalChargedText = document.getElementById("totalChargedText");
    const itemTotalHidden = document.getElementById("itemTotalHidden");

    if (!collectButton || !deliverButton) {
        console.error("Fulfilment buttons were not found.");
        return;
    }

    const platformFee = 1.50;
    const deliveryFee = 23.50;
    const itemTotal = parseFloat(itemTotalHidden.value);

    function setFulfillment(method) {
        if (method === "collect") {

            // Show collection, hide delivery
            collectionBlock.classList.remove("d-none");
            deliveryBlock.classList.add("d-none");

            // Highlight Collect
            collectButton.classList.remove("btn-outline-secondary");
            collectButton.classList.add("btn-success");

            deliverButton.classList.remove("btn-success");
            deliverButton.classList.add("btn-outline-secondary");

            // Update totals
            deliveryFeeText.textContent = "Free";
            totalChargedText.textContent =
                "R" + (itemTotal + platformFee).toFixed(2);

        } else {

            // Show delivery, hide collection
            deliveryBlock.classList.remove("d-none");
            collectionBlock.classList.add("d-none");

            // Highlight Deliver
            deliverButton.classList.remove("btn-outline-secondary");
            deliverButton.classList.add("btn-success");

            collectButton.classList.remove("btn-success");
            collectButton.classList.add("btn-outline-secondary");

            // Update totals
            deliveryFeeText.textContent =
                "R" + deliveryFee.toFixed(2);

            totalChargedText.textContent =
                "R" + (itemTotal + platformFee + deliveryFee).toFixed(2);
        }
    }

    collectButton.addEventListener("click", function () {
        setFulfillment("collect");
    });

    deliverButton.addEventListener("click", function () {
        setFulfillment("deliver");
    });

    // Set Collect as the default when the page loads
    setFulfillment("collect");
});

