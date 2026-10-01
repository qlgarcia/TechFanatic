<?php
session_start(); // Start the session
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password
$dbname = "tech_fanatic";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['logged_in'] = true; // Set logged in session variable

            // Check if a redirect URL is set
            if (isset($_POST['redirect']) && !empty($_POST['redirect'])) {
                $redirectUrl = $_POST['redirect'];
            } else {
                $redirectUrl = 'default_page.php'; // Fallback URL if not set
            }

            // Redirect to the specified URL
            header("Location: $redirectUrl");
            exit();
        } else {
            echo "<script>
                alert('Invalid password!');
                window.location.href = 'index.php?error=true'; // Redirect to the page with the login modal
</script>";
        }
    } else {
        // Show prompt and redirect back to the login form
        echo "<script>
                alert('No user found with that email! Please try again.');
                window.location.href = 'index.php?error=true'; // Redirect to the page with the login modal
              </script>";
        exit(); // Stop further execution
    }
}

// After successful login, retrieve cart items only if user is logged in
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $sql = "SELECT product_id, quantity FROM user_cart WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $product = getProductById($row['product_id']); // Implement this function to get product details
        $cart[] = [
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $row['quantity']
        ];
    }
} else {
    echo "<script>alert('User is not logged in.');</script>";
}

$conn->close();
?>