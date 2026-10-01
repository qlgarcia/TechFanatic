<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// After successful login
$userId = $_SESSION['user_id']; // This is already correct
$sql = "SELECT product_id, quantity FROM user_cart WHERE user_id = ?"; // Ensure user_id is used
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$cart = []; // Initialize the cart array

while ($row = $result->fetch_assoc()) {
    // Fetch product details
    $product = getProductById($row['product_id']); // Implement this function
    $cart[] = [
        'name' => $product['name'],
        'price' => $product['price'],
        'quantity' => $row['quantity'],
        'id' => $row['product_id'] // Include product ID for cart updates
    ];
}

// ... rest of your shopping cart code ...
?>