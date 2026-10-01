<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tech_fanatic";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Add to Cart requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $image = $conn->real_escape_string($_POST['image']);

    if (isset($_SESSION['user_id'])) {
        // Logged-in user: store cart in the database
        $user_id = intval($_SESSION['user_id']);

        $checkStmt = $conn->prepare("SELECT quantity FROM user_cart WHERE user_id = ? AND product_id = ?");
        $checkStmt->bind_param("ii", $user_id, $product_id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            echo json_encode(["error" => "Product has already been registered in the cart."]);
        } else {
            $stmt = $conn->prepare("INSERT INTO user_cart (user_id, product_id, quantity, image) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiis", $user_id, $product_id, $quantity, $image);

            if ($stmt->execute()) {
                echo json_encode(["success" => "Product added to cart successfully!"]);
            } else {
                echo json_encode(["error" => "Error: " . $stmt->error]);
            }
            $stmt->close();
        }
        $checkStmt->close();
    } else {
        // Guest user: store cart in the session
        if (!isset($_SESSION['guest_cart'])) {
            $_SESSION['guest_cart'] = [];
        }

        foreach ($_SESSION['guest_cart'] as &$item) {
            if ($item['product_id'] === $product_id) {
                echo json_encode(["error" => "Product has already been registered in the cart."]);
                exit;
            }
        }

        $_SESSION['guest_cart'][] = [
            'product_id' => $product_id,
            'quantity' => $quantity,
            'image' => $image
        ];

        echo json_encode(["success" => "Product added to cart successfully!"]);
    }
    exit;
}

// Fetch products from the database
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
$products = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graphics Card</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styless.css">
    <link rel="stylesheet" href="modal.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
</head>
<body>
<?php include 'navbar.html'; ?>
<!-- Shopping Cart Modal -->
<?php include 'cart_modal.php'; ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">


<div class="container mt-5">
    <div class="row">
    <?php foreach ($products as $product): ?>
            <div class="col-md-4 mb-4">
                <div class="card" data-bs-toggle="modal" data-bs-target="#cartModal-<?php echo $product['id']; ?>">
                    <img src="uploads/<?php echo $product['image']; ?>" class="card-img-top" alt="<?php echo $product['name']; ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $product['name']; ?></h5>
                        <p class="price">Price: <span style="color: red;">₱<?php echo number_format($product['price'], 2); ?></span></p>
                        <p>Quantity: <span><?php echo $product['quantity']; ?></span></p>
                        <!-- Updated button to launch the modal -->
                        <button class="btn btn-primary add-to-cart" data-bs-toggle="modal" data-bs-target="#cartModal-<?php echo $product['id']; ?>">
                            Add to Cart
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="cartModal-<?php echo $product['id']; ?>" tabindex="-1" aria-labelledby="cartModal<?php echo $product['id']; ?>Label" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="cartModal<?php echo $product['id']; ?>Label"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body d-flex align-items-center">
                            <img src="uploads/<?php echo $product['image']; ?>" class="img-fluid me-4" alt="Product Image" style="max-width: 150px;">
                            <div>
                                <p>Price: <span class="price" style="color: red;">₱<?php echo number_format($product['price'], 1); ?></span></p>
                                <p>Description: <?php echo htmlspecialchars($product['description'] ?? ''); ?></p>
                                <p>Quantity: <span><?php echo $product['quantity']; ?></span></p>
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-primary me-2" 
                                    <<button type="button" class="btn btn-primary" 
onclick="addToCart(<?php echo $product['id']; ?>, 1, '<?php echo $product['image']; ?>')">
    Add to Cart
</button> <!-- Closing tag added here -->
<button type="button" class="btn btn-success" 
    onclick="addToCartAndOpenModal(<?php echo $product['id']; ?>, 1, '<?php echo $product['image']; ?>')">
    Buy Now
</button>                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
let cart = [];

function addToCart(productId, quantity, image) {
    $.post('GPU.php', {
        action: 'add_to_cart',
        product_id: productId,
        quantity: quantity,
        image: image
    })
    .done(response => {
        const data = JSON.parse(response);
        if (data.error) {
            alert(data.error); // Show error message
        } else {
            alert(data.success); // Show success message
        }
    })
    .fail(error => {
        console.error('Error:', error);
        alert('Failed to add product to cart.');
    });
}

function addToCartAndOpenModal(productId, quantity, image) {
    const formData = new FormData();
    formData.append('action', 'add_to_cart');
    formData.append('product_id', productId);
    formData.append('quantity', quantity);
    formData.append('image', image);

    fetch('GPU.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert("Error: " + data.error);
        } else {
            alert(data.success);
            // Close the product modal if open
            const modalId = `#cartModal-${productId}`;
            const productModal = bootstrap.Modal.getInstance(document.querySelector(modalId));
            if (productModal) {
                productModal.hide(); // Hide the modal
            }
            // Optionally open the main cart modal
            const cartModal = new bootstrap.Modal(document.getElementById('cartModal'));
            cartModal.show();
        }
    })
    .catch(error => {
        console.error('Error adding to cart:', error);
        alert('Failed to add product to cart. Please try again.');
    });
}

function updateCart() {
    const cartItems = document.getElementById('cartItems');
    cartItems.innerHTML = '';
    let total = 0;

    cart.forEach((item, index) => {
        total += item.price * item.quantity;
        cartItems.innerHTML += `
            <div>
                <span>${item.name} - ₱${item.price.toFixed(2)} x </span>
                <input type="number" value="${item.quantity}" min="1" onchange="updateItemQuantity(${index}, this.value)" style="width: 50px;">
                <button onclick="removeFromCart(${index})" class="btn btn-danger btn-sm">Remove</button>
            </div>
        `;
    });

    document.getElementById('cartTotal').innerText = `Total: ₱${total.toFixed(1)}`;
    
    // Save cart to local storage
    localStorage.setItem('cart', JSON.stringify(cart));
}



function checkLogin() {
    <?php if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true): ?>
        // User is not logged in, do not show the cart modal
        // Optionally, you can show a message or redirect to login
        alert('You are currently in guest mode. Please log in to access your cart.');
    <?php else: ?>
        // User is logged in, show the cart modal
        const cartModal = new bootstrap.Modal(document.getElementById('cartModal'));
        cartModal.show();
    <?php endif; ?>
}

window.onload = function() {
    // Load cart from the database instead of local storage
    $.post('file_maintenance.php', {
        action: 'load',
        user_id: <?php echo $_SESSION['user_id']; ?>
    }, function(data) {
        cart = JSON.parse(data);
        updateCart();
    });
};
</script>

</body>
<?php include 'footer.html'; ?>

</html>