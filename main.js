$(function() {
  $("#accordion").accordion();
  
  // Check if there is an error in the URL and show the login modal
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.has('error')) {
    $('#loginModal').modal('show');
  }
});

let cart = [];

// Function to add an item to the cart
function addToCart(name, price) {
  const item = { name, price: parseFloat(price.replace(/₱|,/g, '')), quantity: 1 }; // Initialize quantity to 1
  const existingItemIndex = cart.findIndex(cartItem => cartItem.name === name);
  
  if (existingItemIndex > -1) {
    // If the item already exists in the cart, increase the quantity
    cart[existingItemIndex].quantity += 1;
  } else {
    // Otherwise, add the new item to the cart
    cart.push(item);
  }
  updateCart();
}

// Function to remove an item from the cart
function removeFromCart(index) {
  cart.splice(index, 1);
  updateCart();
}

// Function to update the cart display
function updateCart() {
  const cartItems = document.getElementById('cartItems');
  cartItems.innerHTML = '';
  let total = 0;

  // Retrieve cart from session if available
  if (typeof cart === 'undefined' || cart.length === 0) {
    cart = <?php echo json_encode($_SESSION['cart'] ?? []); ?>; // Fetch from PHP session
  }

  cart.forEach((item, index) => {
    total += item.price * item.quantity; // Calculate total based on quantity
    cartItems.innerHTML += `
      <div>
        <span>${item.name} - ₱${item.price.toFixed(2)} x </span>
        <input type="number" value="${item.quantity}" min="1" onchange="updateQuantity(${index}, this.value)" style="width: 50px;">
        <button onclick="removeFromCart(${index})" class="btn btn-danger btn-sm">Remove</button>
      </div>
    `;
  });

  document.getElementById('cartTotal').innerText = `Total: ₱${total.toFixed(2)}`;
}

// Function to proceed to checkout
function proceedToCheckout() {
  alert('Proceeding to checkout...');
}

// Function to check login status and open the appropriate modal
function checkLogin() {
  <?php if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true): ?>
    $('#loginModal').modal('show');
  <?php else: ?>
    // User is logged in, show the cart modal
    const cartModal = new bootstrap.Modal(document.getElementById('cartModal'));
    cartModal.show();
  <?php endif; ?>
}

// Function to update the quantity of an item in the cart
function updateQuantity(index, quantity) {
  if (quantity < 1) {
    quantity = 1; // Ensure quantity is at least 1
  }
  cart[index].quantity = parseInt(quantity);
  updateCart();
}

