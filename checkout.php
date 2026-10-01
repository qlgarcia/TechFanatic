<?php
session_start();
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
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$userId = $_SESSION['user_id'];

// Get the JSON input
$data = json_decode(file_get_contents('php://input'), true);

if (!is_array($data)) {
    echo json_encode(["error" => "Invalid input"]);
    exit;
}

foreach ($data as $item) {
    $productId = intval($item['productId']);
    $quantity = intval($item['quantity']);

    // Deduct quantity from products table
    $updateProductStmt = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ? AND quantity >= ?");
    $updateProductStmt->bind_param("iii", $quantity, $productId, $quantity);
    
    if (!$updateProductStmt->execute() || $updateProductStmt->affected_rows === 0) {
        echo json_encode(["error" => "Failed to update product quantity or insufficient stock for product ID: $productId"]);
        exit;
    }

    // Remove the product from the user's cart after successful deduction
    $deleteCartStmt = $conn->prepare("DELETE FROM user_cart WHERE user_id = ? AND product_id = ?");
    $deleteCartStmt->bind_param("ii", $userId, $productId);
    $deleteCartStmt->execute();
}

echo json_encode(["success" => true]);
$conn->close();
?>