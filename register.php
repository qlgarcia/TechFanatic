<?php
session_start(); // Start the session
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password
$dbname = "tech_fanatic";

// Create connection
mysqli_report(MYSQLI_REPORT_OFF);
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $newUsername = strstr($email, '@', true); // derive username from email prefix

    $sql = "INSERT INTO users (username, email, password) VALUES ('$newUsername', '$email', '$password')";
    
    if ($conn->query($sql) === TRUE) {
        // Set session variables after successful registration
        $_SESSION['user_id'] = $conn->insert_id; // Get the ID of the newly registered user
        $_SESSION['logged_in'] = true; // Set logged in session variable
        header("Location: index.php"); // Redirect to homepage
        exit(); // Ensure no further code is executed
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>