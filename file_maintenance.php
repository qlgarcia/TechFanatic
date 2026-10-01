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

function validateFileUpload($file) {
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $maxFileSize = 5 * 1024 * 1024; // 5MB

    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExtension, $allowedExtensions)) {
        return "Invalid file type. Allowed types: " . implode(', ', $allowedExtensions);
    }

    if ($file['size'] > $maxFileSize) {
        return "File size exceeds 5MB limit.";
    }

    return null;
}

function getAllProducts($conn) {
    // Updated SQL query to include the quantity column
    $sql = "SELECT id, name, image, price, description, quantity, created_at, updated_at FROM products ORDER BY created_at DESC";
    $result = $conn->query($sql);
    $products = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }

    return $products;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        switch ($action) {
            case 'add':
                $name = $_POST['product_name'] ?? null;
                $price = (float)($_POST['product_price'] ?? 0);
                $description = $_POST['product_description'] ?? null;
                $image = $_FILES['product_image'] ?? null;
                $quantity = (int)($_POST['product_quantity'] ?? 0); // Get quantity

                if (!$name || !$price || $quantity < 0) {
                    die("Product name, price, and a valid quantity are required.");
                }

                if ($image && $image['error'] === UPLOAD_ERR_OK) {
                    $error = validateFileUpload($image);
                    if ($error) {
                        die($error);
                    }

                    $target_dir = "uploads/";
                    $target_file = $target_dir . basename($image['name']);
                    move_uploaded_file($image["tmp_name"], $target_file);
                    $image_path = basename($image['name']);
                } else {
                    $image_path = 'default.jpg'; // Default image if none provided
                }

                $sql = "INSERT INTO products (name, image, price, description, quantity) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssdsi", $name, $image_path, $price, $description, $quantity);
                $stmt->execute();
                $stmt->close();
                header("Location: file_maintenance.php");
                break;

                case 'edit':
                    $productId = (int)$_POST['product_id'];
                    $name = $_POST['product_name'] ?? null;
                    $price = (float)($_POST['product_price'] ?? null);
                    $description = $_POST['product_description'] ?? null;
                    $image = $_FILES['product_image'] ?? null;
                    $quantity = (int)($_POST['product_quantity'] ?? 0);
                
                    if (!$name || !$price || $quantity < 0) {
                        die("Product name, price, and a valid quantity are required.");
                    }
                
                    if ($image && $image['error'] === UPLOAD_ERR_OK) {
                        $error = validateFileUpload($image);
                        if ($error) {
                            die($error);
                        }
                
                        $target_dir = "uploads/";
                        $target_file = $target_dir . uniqid() . '_' . basename($image['name']);
                
                        if (move_uploaded_file($image["tmp_name"], $target_file)) {
                            $image_path = basename($target_file);
                
                            // Update with image
                            $sql = "UPDATE products SET name = ?, image = ?, price = ?, description = ?, quantity = ? WHERE id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("ssdssi", $name, $image_path, $price, $description, $quantity, $productId);
                            $stmt->execute();
                            $stmt->close(); // Closing after successful execution.
                        } else {
                            die("Failed to upload image.");
                        }
                    } else {
                        // Update without changing the image
                        $sql = "UPDATE products SET name = ?, price = ?, description = ?, quantity = ? WHERE id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("sdssi", $name, $price, $description, $quantity, $productId);
                        $stmt->execute();
                        $stmt->close(); // Closing after successful execution.
                    }
                
                    header("Location: file_maintenance.php");
                    break;
                                                            
            case 'delete':
                $productId = (int)$_POST['product_id'];

                // Delete related records in user_cart first
                $sql = "DELETE FROM user_cart WHERE product_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $productId);
                $stmt->execute();
                $stmt->close();

                // Get image filename before deleting record
                $sql = "SELECT image FROM products WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $productId);
                $stmt->execute();
                $result = $stmt->get_result();
                $product = $result->fetch_assoc();

                // Delete the image file if it exists
                if ($product && $product['image'] !== 'default.jpg') {
                    $image_path = "uploads/" . $product['image'];
                    if (file_exists($image_path)) {
                        unlink($image_path);
                    }
                }

                // Delete the database record
                $sql = "DELETE FROM products WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $productId);
                $stmt->execute();
                $stmt->close();
                header("Location: file_maintenance.php");
                break;

            case 'update':
                $userId = $_POST['user_id'];
                $productId = $_POST['product_id'];
                $quantity = $_POST['quantity'];

                // Update quantity in user_cart
                $sql = "UPDATE user_cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iii", $quantity, $userId, $productId);
                $stmt->execute();
                $stmt->close();
                break;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'add') {
        $userId = $_POST['user_id'];
        $productId = $_POST['product_id'];
        $quantity = $_POST['quantity'];
        $image = $_POST['image'];
 
        // Insert into user_cart
        $sql = "INSERT INTO user_cart (user_id, product_id, quantity, image) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiis", $userId, $productId, $quantity, $image);
        $stmt->execute();
        $stmt->close();
    }
    
    if ($action === 'update') {
        $userId = $_POST['user_id'];
        $productId = $_POST['product_id'];
        $quantity = $_POST['quantity'];

        // Update quantity in user_cart
        $sql = "UPDATE user_cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $quantity, $userId, $productId);
        $stmt->execute();
        $stmt->close();
    }
}

$products = getAllProducts($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css
">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css
">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js
"></script>
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js
"></script>
</head>
<body style="background-color: rgb(32, 32, 32); color: white;">
    <nav class="navbar navbar-expand-lg" style="background-color: #343a40;">
        <div class="container">
            <a class="navbar-brand" href="#"><span class="text-info">PRODUCT</span><span class="text-white">ADMIN</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Customer LANDING PAGE</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#products">Customer's Product</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="user_maintenance.php">USER ADMIN</a>
                    </li>
                </ul>
                <form class="d-flex me-3" role="search">
                    <input class="me-2" type="search" id="searchInput" placeholder="Search" style="background-color: rgb(32, 32, 32); color: white;" aria-label="Search">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
            </div>
        </div>
    </nav>

    <body style="background-color: rgb(32, 32, 32); color: white;">
<div class="container mt-5">
    <h2>Product Management</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add">
        <div class="mb-3">
            <label class="form-label">Product Name</label>
            <input type="text" class="form-control" name="product_name" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Price</label>
            <input type="text" class="form-control" name="product_price" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="product_description" rows="3"></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Quantity</label>
            <input type="number" class="form-control" name="product_quantity" value="0" min="0" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Image</label>
            <input type="file" class="form-control" name="product_image" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Product</button>
    </form>

    <h3 class="mt-5">Existing Products</h3>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo $product['id']; ?></td>
                    <td><?php echo $product['name']; ?></td>
                    <td><?php echo $product['price']; ?></td>
                    <td><?php echo $product['quantity']; ?></td>
                    <td>
                        <img src="uploads/<?php echo $product['image']; ?>" alt="Product Image" style="max-width: 100px;">
                    </td>
                    <td>
                        <!-- Delete Button -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>

                        <!-- Edit Button (Triggers Modal) -->
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $product['id']; ?>">Edit</button>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal<?php echo $product['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $product['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel<?php echo $product['id']; ?>">Edit Product</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="action" value="edit">
                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

                                            <div class="mb-3">
                                                <label class="form-label">Product Name</label>
                                                <input type="text" class="form-control" name="product_name" value="<?php echo $product['name']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Price</label>
                                                <input type="text" class="form-control" name="product_price" value="<?php echo $product['price']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Description</label>
                                                <textarea class="form-control" name="product_description" rows="3"><?php echo $product['description']; ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Quantity</label>
                                                <input type="number" class="form-control" name="product_quantity" value="<?php echo $product['quantity']; ?>" min="0" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Image</label>
                                                <input type="file" class="form-control" name="product_image">
                                            </div>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
