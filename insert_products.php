<?php
session_start();
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password
$dbname = "tech_fanatic";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Array of products to insert
$products = [
    [
        'name' => 'MSI GeForce RTX™ 4060 VENTUS 2X OC 8GB GDDR6',
        'image' => 'PRODUCTS/RTX 4060.png',
        'price' => 19400.00,
        'description' => 'The MSI GeForce RTX™ 4060 VENTUS 2X OC is a powerful graphics card designed for gamers and content creators seeking high performance at an accessible price point. Featuring 8GB of GDDR6 memory and a 128-bit memory interface, it delivers smooth gameplay and stunning visuals in the latest titles.',
        'quantity' => 10
    ],
    [
        'name' => 'ASUS GeForce® GTX 1650 PH OC 4GB GDDR6',
        'image' => 'PRODUCTS/GTX 1650.png',
        'price' => 8160.00,
        'description' => 'The ASUS GeForce® GTX 1650 PH OC is a great choice for gamers looking for a balance between performance and price. With 4GB of GDDR6 memory, it provides excellent performance for 1080p gaming and is perfect for esports titles.',
        'quantity' => 10
    ],
    [
        'name' => 'MSI GeForce RTX™ 3050 VENTUS 2X 6GB GDDR6 OC',
        'image' => 'PRODUCTS/RTX 3050.png',
        'price' => 11050.00,
        'description' => 'The MSI GeForce RTX™ 3050 is designed for gamers who want to enjoy high-quality graphics at an affordable price. With its 6GB GDDR6 memory, it allows for smooth gameplay in modern titles.',
        'quantity' => 10
    ],
    [
        'name' => 'ASUS GeForce RTX™ 4060 Dual OC 8GB GDDR6 (White)',
        'image' => 'PRODUCTS/RTX 4060 White.png',
        'price' => 20187.00,
        'description' => 'The ASUS GeForce RTX™ 4060 Dual OC combines style and performance with its white design, making it a perfect fit for modern builds.',
        'quantity' => 10
    ],
    [
        'name' => 'ASUS GeForce RTX™ 4060 DUAL OC 8GB GDDR6 (Black)',
        'image' => 'PRODUCTS/RTX 4060 Black.png',
        'price' => 20187.00,
        'description' => 'The ASUS GeForce RTX™ 4060 DUAL OC delivers exceptional performance and thermal efficiency, perfect for gaming and creative tasks.',
        'quantity' => 10
    ],
    [
        'name' => 'ASUS GeForce RTX™ 3050 DUAL OC V2 8GB GDDR6',
        'image' => 'PRODUCTS/RTX 3050 V2.png',
        'price' => 13442.00,
        'description' => 'The ASUS GeForce RTX™ 3050 DUAL OC V2 offers gamers impressive performance with its advanced cooling technology and 8GB memory.',
        'quantity' => 10
    ],
    [
        'name' => 'MSI GeForce RTX™ 4070 GAMING X SLIM 12GB GDDR6X',
        'image' => 'PRODUCTS/RTX 4070.png',
        'price' => 43209.00,
        'description' => 'The MSI GeForce RTX™ 4070 GAMING X SLIM is designed for high-performance gaming, featuring advanced cooling and 12GB of GDDR6X memory.',
        'quantity' => 10
    ],
    [
        'name' => 'GIGABYTE GeForce RTX™ 4070 Ti SUPER EAGLE OC 16GB GDDR6X',
        'image' => 'PRODUCTS/RTX 4070Ti.png',
        'price' => 57569.00,
        'description' => 'The GIGABYTE GeForce RTX™ 4070 Ti SUPER EAGLE OC delivers exceptional performance for both gaming and content creation with 16GB of memory.',
        'quantity' => 10
    ],
    [
        'name' => 'ASUS ProArt GeForce RTX™ 4070 Ti OC 12GB GDDR6X',
        'image' => 'PRODUCTS/RTX ProArt.png',
        'price' => 61250.00,
        'description' => 'The ASUS ProArt GeForce RTX™ 4070 Ti OC is designed for professionals, offering 12GB of GDDR6X memory and optimized for creative applications.',
        'quantity' => 10
    ]
];


// Insert each product into the database
foreach ($products as $product) {
    $sql = "INSERT INTO products (name, image, price, description, quantity) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsi", $product['name'], $product['image'], $product['price'], $product['description'], $product['quantity']);
    
    if (!$stmt->execute()) {
        echo "Error inserting product: " . $stmt->error . "<br>";
    }
}

// Insert each new product into the database
foreach ($new_products as $product) {
    $sql = "INSERT INTO products (name, image, price, description, quantity) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsi", $product['name'], $product['image'], $product['price'], $product['description'], $product['quantity']);
    
    if (!$stmt->execute()) {
        echo "Error inserting product: " . $stmt->error . "<br>";
    }
}

$stmt->close();
$conn->close();
echo "Products added successfully!";
?>