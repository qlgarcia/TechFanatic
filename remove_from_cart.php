<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tech_fanatic";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "User not logged in";
    exit;
}

// Handle POST request to remove item from cart
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $productId = intval($_POST['productId']);

    // Prepare SQL statement to delete item from cart
    $sql = "DELETE FROM user_cart WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ii", $userId, $productId);
        if ($stmt->execute()) {
            echo "success"; // Indicate success
        } else {
            echo "Error removing item: " . $stmt->error; // Handle execution error
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error; // Handle preparation error
    }
}

$conn->close();
?> 