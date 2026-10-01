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

    // Check if the product already exists in the user's cart
    // Note: You may want to handle the case where the user is not logged in
    $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;

    // Prepare statement to insert new product into the cart
    $stmt = $conn->prepare("INSERT INTO user_cart (user_id, product_id, quantity, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $user_id, $product_id, $quantity, $image);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Product added to cart successfully!"]);
    } else {
        echo json_encode(["error" => "Error: " . $stmt->error]);
    }
    $stmt->close();
    exit;
}

// Fetch products from the database for display
$sql = "SELECT * FROM products WHERE category = 'Case'"; // Filter by category
$result = $conn->query($sql);
$products = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PC Case</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styless.css">
    <link rel="stylesheet" href="modal.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
</head>
<body>
<?php include 'navbar.html'; ?>

<div class="container mt-5">
    <div class="row">
        <?php foreach ($products as $product): ?>
            <div class="col-md-4 mb-4">
                <div class="card" data-bs-toggle="modal" data-bs-target="#cartModal-<?php echo $product['id']; ?>">
                    <img src="uploads/<?php echo $product['image']; ?>" class="card-img-top" alt="<?php echo $product['name']; ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $product['name']; ?></h5>
                        <p class="price">Price: <span style="color: red;">₱<?php echo number_format($product['price'], 2); ?></span></p>
                        <button class="btn btn-primary add-to-cart" onclick="addToCart(<?php echo $product['id']; ?>, 1, '<?php echo $product['image']; ?>')">Add to Cart</button>
                    </div>
                </div>
            </div>

            <!-- Modal for each product -->
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
                                <p>Price: <span class="price" style="color: red;">₱<?php echo number_format($product['price'], 2); ?></span></p>
                                <p>Description: <?php echo htmlspecialchars($product['description'] ?? ''); ?></p>
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-primary me-2" onclick="addToCart(<?php echo $product['id']; ?>, 1, '<?php echo $product['image']; ?>')">Add to Cart</button>
                                    <button type="button" class="btn btn-success">Buy Now</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'cart_modal.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function addToCart(productId, quantity, image) {
    $.post('Case.php', {
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
</script>

</body>
<?php include 'footer.html'; ?>

</html>