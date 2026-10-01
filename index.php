<?php
session_start(); // Start the session
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tech Fanatic</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
</head>
<body>
<?php include 'navbar.html'; ?>



    <div class="carousel slide" data-bs-ride="carousel" id="carouselExampleIndicators">
        <div class="carousel-indicators">
            <button aria-label="Slide 1" class="active" data-bs-slide-to="0" data-bs-target="#carouselExampleIndicators" type="button"></button>
            <button aria-label="Slide 2" data-bs-slide-to="1" data-bs-target="#carouselExampleIndicators" type="button"></button>
            <button aria-label="Slide 3" data-bs-slide-to="2" data-bs-target="#carouselExampleIndicators" type="button"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img alt="HeroImage" class="d-block w-100" src="LandingPageImages/alex-diffor-jUaJTNwL_1c-unsplash.jpg">
                <div class="carousel-caption">
                    <h5>Build Your Dream PC</h5>
                    <p>Unleash Performance With Premium PC Parts</p>
                    <p><a class="btn btn-primary mt-3" href="#products">Shop now</a></p>
                </div>
            </div>
            <div class="carousel-item">
                <img alt="SecondaryHero" class="d-block w-100" src="LandingPageImages/complete-gaming-pc-setup-for-sale-or-swap-sa-laptop-37k-v0-hqcpn8ads0kb1.jpg">
                <div class="carousel-caption">
                    <h5>Expert-Approved Components</h5>
                    <p>Choose from our curated selection of reliable and performance-driven components, trusted by professionals and enthusiasts alike.</p>
                    <p><a class="btn btn-primary mt-3" href="#products">Shop now</a></p>
                </div>
            </div>
            <div class="carousel-item">
                <img alt="ThirdHero" class="d-block w-100" src="LandingPageImages/Custom-Build-PC-Part-List.webp">
                <div class="carousel-caption">
                    <h5>Cutting Edge Technology</h5>
                    <p>Experience next-level speed and efficiency with our latest products.</p>
                    <p><a class="btn btn-primary mt-3" href="#products">Shop now</a></p>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" data-bs-slide="prev" data-bs-target="#carouselExampleIndicators" type="button">
            <span aria-hidden="true" class="carousel-control-prev-icon"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" data-bs-slide="next" data-bs-target="#carouselExampleIndicators" type="button">
            <span aria-hidden="true" class="carousel-control-next-icon"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <section class="about section-padding" id="about">
        <div class="container">
            <div class="row">

                <div class="section-header text-center pb-5-about">
                    <div class="about-text text-white">
                        <h1> About <span class="text-info text-uppercase">TECH</span>FANATIC</h1>
                        <p>Welcome to TechFanatic, your ultimate destination for high-quality
                             PC parts and accessories! Whether you’re a hardcore gamer, a tech 
                             enthusiast, or a casual user looking to upgrade your system, we’re
                              here to provide you with the best products and expert advice to help
                               you achieve your computing goals.</p>
                        <h2 class="fw-bold"> Our Mission </h2>
                        <p> At TechFanatic, our mission is to empower our customers with the best tools 
                            and components to create their ideal computing experience. We believe
                             that everyone deserves access to top-notch technology, and we strive to deliver
                              products that combine quality, performance, and value.</p>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="products section-padding" id="products">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-header text-center pb-5">
                        <h2>Our Products</h2>
                        <p>Discover the finest selection of PC parts tailored for every build, from casual setups to high-performance gaming rigs.</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-12 col-lg-4">
                    <div class="card text-white text-center bg-dark pb-2">
                        <div class="card-body">
                            <div class="img-area mb-4"><img alt="Graphics Card" class="img-fluid" src="LandingPageImages/andrey-matveev-ipVMl4H6g6o-unsplash.jpg"></div>
                            <i class="bi bi-gpu-card"></i>
                            <h3 class="card-title fw-bold">Graphics Card</h3>
                            <a class="btn bg-primary text-white" href="GPU.php"> Show More </a>
                        </div>
                    </div>
                </div>
                <!-- PC Case -->
                <div class="col-12 col-md-12 col-lg-4">
                    <div class="card text-white text-center bg-dark pb-2">
                        <div class="card-body">
                            <div class="img-area mb-4"><img alt="PC Case" class="img-fluid" src="LandingPageImages/andrey-matveev-3IVnMjn0dh0-unsplash.jpg"></div>
                            <i class="bi bi-pc"></i>
                            <h3 class="card-title fw-bold">PC Case</h3>
                            <a class="btn bg-primary text-white" href="Case.html"> Show More </a>
                        </div>
                    </div>
                </div>
                <!-- Motherboard -->
                <div class="col-12 col-md-12 col-lg-4">
                    <div class="card text-white text-center bg-dark pb-2">
                        <div class="card-body">
                            <div class="img-area mb-4"><img alt="Motherboard" class="img-fluid" src="LandingPageImages/MotherBoard.jpg"></div>
                            <i class="bi bi-motherboard"></i>
                            <h3 class="card-title fw-bold">Motherboard</h3>
                            <a class="btn bg-primary text-white" href="Motherboards.html"> Show More </a>
                        </div>
                    </div>
                </div>
                <!-- Monitor -->
                <div class="col-12 col-md-12 col-lg-4 secondrow">
                    <div class="card text-white text-center bg-dark pb-2">
                        <div class="card-body">
                            <div class="img-area mb-4"><img alt="Monitor" class="img-fluid" src="LandingPageImages/Monitor.jpg"></div>
                            <i class="bi bi-tv"></i>
                            <h3 class="card-title fw-bold">Monitor</h3>
                            <a class="btn bg-primary text-white" href="monitor.html"> Show More </a>
                        </div>
                    </div>
                </div>
                <!-- Mouse -->
                <div class="col-12 col-md-12 col-lg-4 secondrow">
                    <div class="card text-white text-center bg-dark pb-2">
                        <div class="card-body">
                            <div class="img-area mb-4"><img alt="Mouse" class="img-fluid" src="LandingPageImages/Mouse.jpg"></div>
                            <i class="bi bi-mouse2"></i>
                            <h3 class="card-title fw-bold">Mouse</h3>
                            <a class="btn bg-primary text-white" href="mouse.html"> Show More </a>
                        </div>
                    </div>
                </div>
                <!-- Keyboard -->
                <div class="col-12 col-md-12 col-lg-4 secondrow">
                    <div class="card text-white text-center bg-dark pb-2">
                        <div class="card-body">
                            <div class="img-area mb-4"><img alt="Keyboard" class="img-fluid" src="LandingPageImages/keyboard.jpg"></div>
                            <i class="bi bi-keyboard"></i>
                            <h3 class="card-title fw-bold">Keyboard</h3>
                            <a class="btn bg-primary text-white" href="keyboard.html"> Show More </a>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="text-center faqs"> 
    <h1 class="fw-bold">FAQs</h1>
</div>
<div id="accordion">
    <h3>What should I do if I receive a damaged or defective product?</h3>
    <div>
        <p>
            If you receive a damaged or defective item, please contact our customer
            support within [X days] with your order number and a description of
            the issue. We will assist you in resolving the problem, whether 
            it's a replacement or refund.
        </p>
    </div>
    <h3>Can I modify my order after it has been placed?</h3>
    <div>
        <p>
            If you need to modify your order, please contact us as soon as
            possible. If your order has not yet been processed for shipping,
            we may be able to accommodate your request.
        </p>
    </div>
    <h3>What types of PC parts do you sell?</h3>
    <div>
        <p>
            We offer a wide range of PC parts, including:
        </p>
        <ul>
            <li>Graphics Cards</li>
            <li>MotherBoards</li>
            <li>Peripherals</li>
            <li>PC Case</li>
        </ul>
    </div>
    <h3>Do you offer warranties on your products?</h3>
    <div>
        <p>
            Yes, most of our products come with a manufacturer warranty.
            The warranty period varies by product, so please check the individual
            product page for specific details.
        </p>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.14.1/jquery-ui.min.js"></script>
<script>
    $(function() {
        $("#accordion").accordion({
            heightStyle: "content" // Adjusts the height of the accordion panels
        });
    });
</script>

<?php include 'footer.html'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <?php include 'login_modal.php'; ?>


    <script>
        function showLoginModal() {
            // Hide the current modal
            $('.modal').modal('hide');
            // Show the login modal
            $('#loginModal').modal('show');
        }

        function validateSignUpForm() {
            const password = document.getElementById('signUpPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (password !== confirmPassword) {
                alert("Passwords do not match!");
                return false; // Prevent form submission
            }
            return true; // Allow form submission
        }
    </script>

    <!-- Include Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<?php include 'cart_modal.php'; ?>

    <script>
<script>
let cart = [];

function addToCart(name, price, productId) {
    const item = { name, price: parseFloat(price.replace(/₱|,/g, '')), quantity: 1 }; // Removed productId from here
    const existingItemIndex = cart.findIndex(cartItem => cartItem.name === name);
    
    if (existingItemIndex > -1) {
        cart[existingItemIndex].quantity += 1;
        // Update the quantity in the database
        $.post('file_maintenance.php', {
            action: 'update',
            user_id: <?php echo $_SESSION['user_id']; ?>,
            product_id: productId,
            quantity: cart[existingItemIndex].quantity
        });
    } else {
        cart.push(item);
        // Insert new item into the database
        $.post('file_maintenance.php', {
            action: 'add',
            user_id: <?php echo $_SESSION['user_id']; ?>,
            product_id: productId,
            quantity: 1
        });
    }
    
    updateCart();

        function updateCart() {
            const cartItems = document.getElementById('cartItems');
            cartItems.innerHTML = '';
            let total = 0;

            cart.forEach((item, index) => {
                total += item.price * item.quantity;
                cartItems.innerHTML += `
                    <div>
                        <span>${item.name} - ₱${item.price.toFixed(2)} x ${item.quantity}</span>
                        <button onclick="removeFromCart(${index})" class="btn btn-danger btn-sm">Remove</button>
                    </div>
                `;
            });

            document.getElementById('cartTotal').innerText = `Total: ₱${total.toFixed(1)}`;
            
            // Save cart to local storage
            localStorage.setItem('cart', JSON.stringify(cart));
        }

        function removeFromCart(index) {
            cart.splice(index, 1);
            updateCart();
        }

        function proceedToCheckout() {
            alert('Proceeding to checkout...');
        }
    </script>

    <script>
        function checkLogin() {
            <?php if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true): ?>
                // User is not logged in, show the login modal
                $('#loginModal').modal('show');
            <?php else: ?>
                // User is logged in, show the cart modal
                $('#cartModal').modal('show');
            <?php endif; ?>
        }
    </script>

    <script>
        window.onload = function() {
            const savedCart = localStorage.getItem('cart');
            if (savedCart) {
                cart = JSON.parse(savedCart);
                updateCart();
            }
        };
    </script>
</body>
<script src="main.js"></script> 
</html>