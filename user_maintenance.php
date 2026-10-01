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


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $email = $_POST['email'];
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (email, password) VALUES ('$email', '$password')";
                $conn->query($sql);
                break;

            case 'edit':
                $id = $_POST['id'];
                $email = $_POST['email'];
                $password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
                $sql = "UPDATE users SET email='$email'" . ($password ? ", password='$password'" : "") . " WHERE id='$id'";
                $conn->query($sql);
                break;

            case 'delete':
                $id = $_POST['id'];
                $sql = "DELETE FROM users WHERE id='$id'";
                $conn->query($sql);
                break;
        }
    }
}


$sql = "SELECT * FROM users";
$result = $conn->query($sql);
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Maintenance</title>
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
            <a class="navbar-brand" href="#"><span class="text-info">USER</span><span class="text-white">ADMIN</span></a>
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
                        <a class="nav-link" href="file_maintenance.php">PRODUCT ADMIN</a>
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

<div class="container mt-5 text-white">
    <h2>User Maintenance</h2>
    <form method="POST">
        <input type="hidden" name="action" value="add">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" name="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Add User</button>
    </form>

    <h3 class="mt-5">Existing Users</h3>
    <table class="table text-white">
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo $user['email']; ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                        <button class="btn btn-warning btn-sm" onclick="editUser(<?php echo $user['id']; ?>, '<?php echo $user['email']; ?>')">Edit</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function editUser(id, email) {
    const newEmail = prompt("Edit Email:", email);
    const newPassword = prompt("Edit Password (leave blank if not changing):");
    if (newEmail || newPassword) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="${id}">
            <input type="hidden" name="email" value="${newEmail || email}">
            <input type="hidden" name="password" value="${newPassword || ''}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
</body>
</html>
