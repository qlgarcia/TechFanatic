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
    $resetEmail = $_POST['resetEmail'];

    // Check if the email exists in the database
    $sql = "SELECT * FROM users WHERE email='$resetEmail'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Generate a unique token for password reset
        $token = bin2hex(random_bytes(50)); // Generate a random token
        $expires = date("U") + 3600; // Token expires in 1 hour

        // Attempt to store the token in the database
        $sql = "INSERT INTO password_resets (email, token, expires) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        // Check if the table exists before executing
        if ($stmt) {
            $stmt->bind_param("ssi", $resetEmail, $token, $expires);
            $stmt->execute();
        }

        // Send the email with the reset link
        $resetLink = "http://yourwebsite.com/reset.php?token=" . $token; // Adjust the URL as needed
        $subject = "Password Reset Request";
        $message = "Click the link below to reset your password:\n" . $resetLink;
        $headers = "From: no-reply@yourwebsite.com";

        // Send the email regardless of the database operation
        mail($resetEmail, $subject, $message, $headers);

        // Show a prompt indicating that an email has been sent
        echo "<script>alert('A password reset link has been sent to your email.'); window.location.href = 'index.php';</script>";
    } else {
        // Show prompt if no user found
        echo "<script>alert('No user found with that email! Please try again.'); window.location.href = 'index.php';</script>";
    }
}

$conn->close();
?>