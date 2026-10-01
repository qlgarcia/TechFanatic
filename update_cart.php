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

// Handle POST request to update cart quantity
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $productId = intval($_POST['productId']);
    $quantity = intval($_POST['quantity']);

    // Validate quantity
    if ($quantity <= 0) {
        echo "Invalid quantity";
        exit;
    }

    // Prepare SQL statement to update quantity
    $sql = "UPDATE user_cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("iii", $quantity, $userId, $productId);
        if ($stmt->execute()) {
            echo "success"; // Indicate success
        } else {
            echo "Error updating quantity: " . $stmt->error; // Handle execution error
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error; // Handle preparation error
    }
}

$conn->close();
?> 