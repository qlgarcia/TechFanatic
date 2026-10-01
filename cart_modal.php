<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tech_fanatic";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    exit;
}

$userId = $_SESSION['user_id'];
error_log("User ID: " . $userId); // Log the user ID for debugging

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Valid actions
    $validActions = ['fetch_cart', 'update', 'remove', 'add'];
    if (!in_array($action, $validActions)) {
        echo json_encode(["error" => "Invalid action provided."]);
        exit;
    }

    try {
        if ($action === 'fetch_cart') {
            // Fetch cart items for the logged-in user
            $sql = "SELECT p.id AS product_id, p.name, p.image, p.price, c.quantity, p.quantity AS available_quantity 
            FROM user_cart c 
            JOIN products p ON c.product_id = p.id 
            WHERE c.user_id = ?";
            
            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                throw new Exception("Failed to prepare fetch_cart statement: " . $conn->error);
            }

            $stmt->bind_param("i", $userId);
            if (!$stmt->execute()) {
                throw new Exception("Failed to execute fetch_cart statement: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $cartItems = $result->fetch_all(MYSQLI_ASSOC);

            echo json_encode($cartItems);
            exit;
        }

        if ($action === 'update') {
            $productId = filter_var($_POST['productId'], FILTER_VALIDATE_INT);
            $quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        
            if ($productId === false || $quantity === false) {
                echo json_encode(["error" => "Invalid product ID or quantity."]);
                exit;
            }
        
            $updateStmt = $conn->prepare("UPDATE user_cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
            if (!$updateStmt) {
                error_log("Prepare Error: " . $conn->error);
                echo json_encode(["error" => "Failed to prepare update statement."]);
                exit;
            }

            $updateStmt->bind_param("iii", $quantity, $userId, $productId);
            if (!$updateStmt->execute()) {
                error_log("Execute Error: " . $updateStmt->error);
                echo json_encode(["error" => "Failed to execute update statement."]);
            } else {
                echo json_encode(["success" => true]);
            }
            $updateStmt->close();
            exit;
        }

        if ($action === 'remove') {
            $productId = filter_var($_POST['productId'], FILTER_VALIDATE_INT);
            if ($productId === false) {
                echo json_encode(["error" => "Invalid product ID."]);
                exit;
            }

            // Remove the product from the cart
            $deleteStmt = $conn->prepare("DELETE FROM user_cart WHERE user_id = ? AND product_id = ?");
            $deleteStmt->bind_param("ii", $userId, $productId);
            if ($deleteStmt->execute()) {
                echo json_encode(["success" => true]);
            } else {
                echo json_encode(["error" => "Failed to remove item"]);
            }
            exit;
        }

        if ($action === 'add') {
            $productId = filter_var($_POST['productId'], FILTER_VALIDATE_INT);
            $quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            $image = $_POST['image'] ?? 'default.jpg'; // Default image if none provided

            if ($productId === false || $quantity === false) {
                echo json_encode(["error" => "Invalid product ID or quantity."]);
                exit;
            }

            // Check if the product already exists in the cart
            $checkStmt = $conn->prepare("SELECT quantity FROM user_cart WHERE user_id = ? AND product_id = ?");
            $checkStmt->bind_param("ii", $userId, $productId);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->num_rows > 0) {
                // Update quantity if product already exists
                $existingItem = $checkResult->fetch_assoc();
                $newQuantity = $existingItem['quantity'] + $quantity;

                $updateStmt = $conn->prepare("UPDATE user_cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
                $updateStmt->bind_param("iii", $newQuantity, $userId, $productId);
                $updateStmt->execute();
                $updateStmt->close();
            } else {
                // Insert new item into the cart
                $insertStmt = $conn->prepare("INSERT INTO user_cart (user_id, product_id, quantity, image) VALUES (?, ?, ?, ?)");
                $insertStmt->bind_param("iiis", $userId, $productId, $quantity, $image);
                $insertStmt->execute();
                $insertStmt->close();
            }

            echo json_encode(["success" => true]);
            exit;
        }

        echo json_encode(["error" => "Invalid action"]);
    } catch (Exception $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}

$conn->close();
?>

<!-- Modal Structure -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cartModalLabel">Shopping Cart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
    <div id="cartItems">
        <!-- Cart items will be dynamically populated here -->
    </div>
    <p id="cartTotal">Total: ₱0.00</p>
</div>>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="proceedToCheckout()">Proceed to Checkout</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('cartModal').addEventListener('show.bs.modal', fetchCartItems);

function fetchCartItems() {
    const formData = new FormData();
    formData.append('action', 'fetch_cart');

    fetch('cart_modal.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }
        return response.json();
    })
    .then(cartItems => {
        if (cartItems.error) {
            alert("Error: " + cartItems.error);
            return;
        }

        const cartItemsContainer = document.getElementById('cartItems');
        const cartTotalElement = document.getElementById('cartTotal');
        cartItemsContainer.innerHTML = '';
        let total = 0;

        // Group items by product ID
        const groupedItems = {};
        cartItems.forEach(item => {
            if (!groupedItems[item.product_id]) {
                groupedItems[item.product_id] = {
                    ...item,
                    quantity: 0
                };
            }
            groupedItems[item.product_id].quantity += item.quantity;
        });

        // Display grouped items with checkboxes
        for (const productId in groupedItems) {
    const item = groupedItems[productId];
    const price = parseFloat(item.price);
    const availableQuantity = item.available_quantity; // Get available quantity

    if (isNaN(price)) {
        console.error('Invalid price for item:', item);
        continue; // Skip this item if price is invalid
    }

    cartItemsContainer.innerHTML += `
        <div class="product-item border p-3 mb-3">
            <div class="d-flex align-items-center">
                <input type="checkbox" class="me-2" id="product-${item.product_id}" value="${item.product_id}" onchange="updateTotal()">
                <img src="uploads/${item.image}" class="img-fluid me-2" alt="${item.name}" style="max-width: 70px;">
                <span class="flex-grow-1">${item.name} - ₱${price.toFixed(2)} x ${item.quantity} (Available: ${availableQuantity})</span>
                <input type="number" value="${item.quantity}" min="1" max="${availableQuantity}" step="1" onchange="updateItemQuantity(${item.product_id}, this.value); updateTotal()" style="width: 50px;">
                <button onclick="removeFromCart(${item.product_id})" class="btn btn-danger btn-sm ms-2">Remove</button>
            </div>
        </div>
    `;
}


        updateTotal(); // Initial total calculation
    })
    .catch(error => {
        console.error('Error fetching cart:', error);
        alert('Failed to load cart. Please try again. Error: ' + error.message);
    });
}

function updateItemQuantity(productId, quantity) {
    const formData = new FormData();
    formData.append('action', 'update');
    formData.append('productId', productId);
    formData.append('quantity', quantity);

    fetch('cart_modal.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            fetchCartItems(); // Refresh the cart items
        } else {
            alert("Error: " + data.error);
        }
    })
    .catch(error => {
        console.error('Error updating quantity:', error);
        alert('Failed to update quantity. Please try again.');
    });
}

function removeFromCart(productId) {
    if (!confirm("Are you sure you want to remove this item?")) return;

    const formData = new FormData();
    formData.append('action', 'remove');
    formData.append('productId', productId);

    fetch('cart_modal.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            fetchCartItems(); // Refresh the cart items
        } else {
            alert("Error: " + data.error);
        }
    })
    .catch(error => {
        console.error('Error removing item:', error);
        alert('Failed to remove item. Please try again.');
    });
}

function updateTotal() {
    const checkboxes = document.querySelectorAll('#cartItems input[type="checkbox"]');
    const cartTotalElement = document.getElementById('cartTotal');
    let total = 0;

    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            const productId = checkbox.value;
            const quantityInput = document.querySelector(`#product-${productId} + img + span + input`);
            const priceText = checkbox.parentElement.querySelector('span').innerText;
            const price = parseFloat(priceText.split(' - ₱')[1].split(' x')[0]);

            const quantity = parseInt(quantityInput.value);
            total += price * quantity;
        }
    });

    cartTotalElement.innerText = `Total: ₱${total.toFixed(2)}`;
}

function proceedToCheckout() {
    const selectedProducts = [];
    const checkboxes = document.querySelectorAll('#cartItems input[type="checkbox"]:checked');

    checkboxes.forEach(checkbox => {
        const productId = checkbox.value;
        const quantityInput = document.querySelector(`#product-${productId} + img + span + input`);
        const quantity = parseInt(quantityInput.value);
        selectedProducts.push({ productId, quantity });
    });

    if (selectedProducts.length === 0) {
        alert("Please select at least one product to proceed to checkout.");
        return;
    }

    // Send the selected products to the server for processing
    fetch('checkout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(selectedProducts)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Thank you for using our services! Your order has been placed.");
            location.reload(); // Refresh the page to update the showcase
        } else {
            alert("Error: " + data.error);
        }
    })
    .catch(error => {
        console.error('Error during checkout:', error);
        alert('Failed to proceed to checkout. Please try again.');
    });
}

function checkLogin() {
    <?php if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true): ?>
        $('#loginModal').modal('show');
    <?php else: ?>
        // User is logged in, show the cart modal
        const cartModal = new bootstrap.Modal(document.getElementById('cartModal'));
        cartModal.show();
    <?php endif; ?>
}

</script>
