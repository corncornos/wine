<?php
include("connectdb.php");
session_start();

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy(); 
    header("Location: login.php"); 
    exit();
}

// Handle Add to Cart
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (!isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] = 1;
    } else {
        $_SESSION['cart'][$product_id]++;
    }

    $_SESSION['cart_message'] = 'Product added to cart!';
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

$sql = "SELECT * FROM products";
$result = $conn->query($sql);

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$product_ids = array_keys($cart_items);

$cart_products = [];
if (!empty($product_ids)) {
    $ids = implode(',', $product_ids);
    $cart_sql = "SELECT * FROM products WHERE id IN ($ids)";
    $cart_result = $conn->query($cart_sql);
    while ($row = $cart_result->fetch_assoc()) {
        $cart_products[] = $row;
    }
}

// Fetch the username of the logged-in user
$username = isset($_SESSION['username']) ? $_SESSION['username'] : "Guest";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Boost in Class</title>
    <link rel="icon" type="image/png" href="images/iconlogo.png">
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function openNav() {
            document.getElementById("mySidenav").classList.add("open");
        }

        function closeNav() {
            document.getElementById("mySidenav").classList.remove("open");
        }

        function openAccountSidenav() {
            document.getElementById("accountSidenav").classList.add("open");
        }

        function closeAccountSidenav() {
            document.getElementById("accountSidenav").classList.remove("open");
        }
    </script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #201f2d; /* Updated background color to match splash.php */
            background-size: cover;
            color: rgb(243, 244, 255);
        }
        .top-nav {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px 30px;
            height: 50px;
            background: linear-gradient(to left, #0b0a10, #0b0a10, #0b0a10, #222131, #0b0a10, #0b0a10, #0b0a10); /* Updated gradient background to match splash.php */
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .top-nav a {
            text-decoration: none;
            color: #fff; /* Adjusted text color for contrast */
            margin: 0 10px;
            font-size: 14px;
        }
        .menu-icon {
            position: absolute;
            left: 20px;
            font-size: 32px;
            color: #fff; /* Adjusted icon color for contrast */
            cursor: pointer;
            z-index: 1100;
            transition: opacity 0.2s;
        }
        .menu-icon.hide {
            opacity: 0;
            pointer-events: none;
        }
        .top-nav .login-icon {
            position: absolute;
            right: 15px;
            display: flex;
            align-items: center;
        
            text-decoration: none;
            color: #333;
            cursor: pointer;
        }
        .top-nav .login-icon img {
            width: 30px;
            height: 30px;
        }
        .top-nav .login-icon:hover {
            color: #007BFF;
        }
        .top-nav .cart-icon {
            position: relative;
            right:35px;
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #333;
            height: 10px;
            width: 10px;
        }
        .top-nav .cart-icon img {
            width: 35px; /* Match the width of the login icon */
            height: 35px; /* Match the height of the login icon */
        }

        .cart-preview {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            min-width: 300px;
            background-color: #111;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            z-index: 1000;
            margin-top: 10px;
        }

        .top-nav .cart-icon:hover .cart-preview {
            display: block;
        }
        .sidenav {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 250px;
            background-color: rgba(17, 17, 17, 0.3); /* Changed from solid color to rgba */
            overflow-x: hidden;
            padding-top: 20px;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            z-index: 1000;
        }
        .sidenav.open {
            transform: translateX(0);
        }
        .sidenav a {
            margin-top: 20px;
            padding: 20px 15px;
            text-decoration: none;
            font-size: 18px;
            color: #fff;
            display: block;
            transition: 0.3s;
        }
        .sidenav a:hover {
            background-color: #575757;
        }
        .sidenav .closebtn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 20px;
            cursor: pointer;
            color: #fff;
        }
        .account-sidenav {
            position: fixed;
            top: 0;
            right: 0;
            height: 50%;
            width: 300px;
            background-color: #111;
            color: #fff;
            overflow-x: hidden;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            z-index: 1000;
            padding: 20px;
        }
        .account-sidenav.open {
            transform: translateX(0);
        }
        .account-sidenav .closebtn {
            position: absolute;
            top: 10px;
            left: 15px;
            font-size: 20px;
            cursor: pointer;
            color: #fff;
        }
        .account-sidenav h3 {
            margin-top: 50px;
            font-size: 20px; /* Increased font size */
            color: #e2a55f;
        }
        .account-sidenav p {
            font-size: 16px; /* Increased font size */
            margin: 10px 0;
        }
        .account-sidenav a {
            display: block;
            color: #007BFF;
            text-decoration: none;
            margin: 10px 0;
            font-size: 16px; /* Increased font size */
        }
        .account-sidenav a:hover {
            text-decoration: underline;
        }
        .payment-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 30px auto;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            max-width: 400px;
            text-align: center;
        }
        .payment-options {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 15px;
        }
        .payment-option {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .payment-option:hover {
            background-color: #f5f5f5;
        }
        .payment-label {
            margin-left: 10px;
        }
        .slideshow-container {
         position: relative;
            max-width: 100%;  /* Reduced from 100% to create margins */
            margin: 0px auto;  /* Changed margin to auto for centering */
            overflow: hidden;
            z-index: -1;
            margin-top: 20px;
            
        }
        .slideshow-container img {
            width: 100%;
            display: none;
    
        }
        .slideshow-container img.active {
            display: block;
        }
        .slideshow-dots {
            text-align: center;
            margin-top: 10px;
        }
        .slideshow-dots span {
            cursor: pointer;
            height: 15px;
            width: 15px;
            margin: 0 5px;
            background-color: #bbb;
            border-radius: 50%;
            display: inline-block;
            transition: background-color 0.3s;
        }
        .slideshow-dots span.active {
            background-color: #717171;
        }
        .floating-home-tab {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #FF8C00; /* Orange background */
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 14px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.3s ease, transform 0.3s ease;
            z-index: 1000;
        }
        .floating-home-tab img {
            width: 16px; /* Icon size */
            height: 16px;
        }
        .floating-home-tab:hover {
            background-color: #E67E00; /* Darker orange on hover */
            transform: scale(1.1);
        }
        
        .header-banner {
                 height: 100px;
            width: 100%;
            background: url('/webdev/images/.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 1px 0;  /* Reduced from 60px to 30px */
            gap: 15px;
            top: -20px;
        }

        .header-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .header-banner h1 {
            color: #FF8C00;
            font-size: 3.5rem;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 5px;
            position: relative;
            z-index: 2;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
        }

        .header-banner p {
             font-family:'Times New Roman', Times, serif;
            font-style: bold;
            color: #fff;
            font-size: 4.5rem; /* Increased font size */
            margin-top: 5; /* Remove top margin since items are side by side */
            position: relative;
            z-index: 2;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
            letter-spacing: 5px;
            left: 115px;
        }

        .top-nav.nav-hidden {
            transform: translateY(-100%);
        }

        .top-nav .brand {
            display: flex;
            align-items: center;
            gap: 20px;
            width: 100%;
            justify-content: center;
            position: relative;
        }

        .top-nav .brand img {
            position: absolute;
            left: 80px;
            width: 70px;
            height: auto;
        }

        .top-nav .brand p {
            font-family: 'Times New Roman', Times, serif;
            color: white;
            font-size: 2.2rem;
            margin: 0;
            margin-left: 60px; /* Added left margin to move text right */
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
            letter-spacing: 5px;
            text-align: center;
        }

        .products {
        position: relative;
    }
    
    .promo-banner {
        position: absolute;
        top: 0;
        right: 40px;
        width: 200px;
        transform: rotate(3deg);
        background: #fff5d7;
        padding: 15px;
        border-radius: 2px;
        box-shadow: 3px 3px 8px rgba(0, 0, 0, 0.3);
        z-index: 10;
    }

    .promo-banner img {
        width: 100%;
        height: auto;
    }
    </style>
</head>
<body>
    <nav class="top-nav">
        <span class="menu-icon" onclick="openNav()">&#9776;</span>
        <div class="brand">
            <img src="images/iconlogo.png" alt="Boost in Class Logo">
            <p>LIQUOR THAT STAYS IN CLASS</p>
        </div>
        <div class="login-icon" onclick="openAccountSidenav()">
            <img src="images/logcon.png" alt="Login">
        </div>
        <!-- Replace existing cart icon HTML with this -->
        <a href="cart.php" class="cart-icon">
            <img src="images/cart-icon.png" alt="View Cart">
            <div class="cart-preview" id="cart-preview">
            </div>
        </a>
    </nav>
    <div id="accountSidenav" class="account-sidenav">
        <span class="closebtn" onclick="closeAccountSidenav()">&times;</span>
        <h3>Account</h3>
        <p>Welcome to Boost in Class, <?php echo htmlspecialchars($username); ?></p>
        <a href="transaction_history.php" style="color: #FF8C00; text-decoration: none;">Transaction History</a>
        <a href="account_settings.php" style="color: #FF8C00; text-decoration: none;">Account Settings</a> <!-- Added Account Settings -->
        <div style="position: absolute; bottom: 50px; left: 20px;">
            <a href="?logout=true" class="logout-btn" style="color: #FF8C00; text-decoration: none;">Logout</a>
        </div>
    </div>
    <div id="mySidenav" class="sidenav">
        <span class="closebtn" onclick="closeNav()">&times;</span>
        <a href="#products">Shop Now!</a>
        <a href="#about">About</a>
        <a href="#contact">Contact</a>
        <a href="cart.php">Cart</a>
    </div>

    <div class="slideshow-container">
        <img src="images/slide_1.png" class="active" alt="Slide 1">
        <img src="images/slide_2.png" alt="Slide 2">
        <img src="images/slide_3.png" alt="Slide 3">
        <img src="images/slide_4.png" alt="Slide 4">
        <img src="images/slide_5.png" alt="Slide 5">
    </div>
    <div class="slideshow-dots">
        <span class="dot active" onclick="showSlide(0)"></span>
        <span class="dot" onclick="showSlide(1)"></span>
        <span class="dot" onclick="showSlide(2)"></span>
        <span class="dot" onclick="showSlide(3)"></span>
        <span class="dot" onclick="showSlide(4)"></span>
    </div>
    <main>
        <section class="products" section id="products">
            
            <h2 style="color:white;">Our Products</h2>
            <div class="promo-banner">
        <img src="images/5p.png" alt="5% Discount Promo">
    </div>
            <div class="product-list">
                <?php while ($row = $result->fetch_assoc()): ?>
                <div class="product">
                    <img src="<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" style="width:100%; height:auto;">
                    <h3><?php echo $row['name']; ?></h3>
                    <p>₱<?php echo number_format($row['price'], 2); ?></p>
                    <?php if ((int)$row['quantity'] === 0): ?>
                        <div class="out-of-stock">Out of Stock</div>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            <div class="add-to-cart-btn-wrapper">
                                <button type="submit" name="add_to_cart" disabled style="opacity:0.6;cursor:not-allowed;">Add to Cart</button>
                            </div>
                        </form>
                    <?php else: ?>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            <div class="add-to-cart-btn-wrapper">
                                <button type="submit" name="add_to_cart">Add to Cart</button>
                                <span class="add-to-cart-description"><?php echo htmlspecialchars($row['description']); ?></span>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
                <?php endwhile; ?>
            </div>
        </section>
    </main>
    <section id="about" style="padding: 40px 20px; background-color: #161520; text-align: center;">
        <h2>About Us</h2>
        <p>Boost In Class
       
        Established in 2025, Boost In Class is your go-to destination for premium liquors, offering a carefully curated selection of spirits, wines, and beers to elevate any occasion. We take pride in providing top-quality products, excellent customer service, and a refined shopping experience. Whether you're celebrating a special moment or simply enjoying a drink, we’re here to bring you the best—always with class.</p>
    
        <h2>Mission</h2>
        <p>Mission
        To provide a diverse selection of high-quality liquors at competitive prices, ensuring a convenient and enjoyable shopping experience for all customers while promoting responsible drinking.</p>
    
        <h2>Vision</h2>
        <p>To be the go-to liquor store in the community, recognized for our excellent service, curated selection, and commitment to customer satisfaction.

</p>
    
    </section>

    <section id="contact" style="padding: 40px 20px; background-color: #161520; text-align: center;">
        <h2>Contact Us</h2>
        <p>If you have any questions or feedback, feel free to reach out to us:</p>
        <p>Email: support@boostinclass.com</p>
        <p>Phone: +639122998086</p>
        <p>Address: Laklak St. Cainta, Rizal</p>
    </section>
    <footer>
        <p>&copy; 2025 Boost in Class. All rights reserved.</p>
    </footer>

    
    <a href="index.php" class="floating-home-tab">
        <img src="images/home-icon.png" alt="Home"> <!-- Home icon -->
        
    </a>
    <script>
        // Add this before your existing script
        let lastScroll = 0;
        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;
            const nav = document.querySelector('.top-nav');
            
            if (currentScroll <= 0) {
                nav.classList.remove('nav-hidden');
                return;
            }
            
            if (currentScroll > lastScroll && !nav.classList.contains('nav-hidden')) {
                // Scrolling down
                nav.classList.add('nav-hidden');
            } else if (currentScroll < lastScroll && nav.classList.contains('nav-hidden')) {
                // Scrolling up
                nav.classList.remove('nav-hidden');
            }
            
            lastScroll = currentScroll;
        });
        
        // Show/hide description tooltip on hover, only if the description span exists
        document.querySelectorAll('.add-to-cart-btn-wrapper').forEach(function(wrapper) {
            var btn = wrapper.querySelector('button');
            var desc = wrapper.querySelector('.add-to-cart-description');
            if (!desc) return;
            // Hide by default
            desc.style.display = 'none';
            btn.addEventListener('mouseenter', function() {
                desc.style.display = 'block';
            });
            btn.addEventListener('mouseleave', function() {
                desc.style.display = 'none';
            });
            btn.addEventListener('focus', function() {
                desc.style.display = 'block';
            });
            btn.addEventListener('blur', function() {
                desc.style.display = 'none';
            });
        });

        $(document).ready(function () {
            // Fetch cart contents when hovering over the cart icon
            $('.cart-icon').hover(function () {
                $.ajax({
                    url: 'fetch_cart.php',
                    type: 'GET',
                    success: function (response) {
                        $('#cart-dropdown-content').html(response);
                    },
                    error: function () {
                        $('#cart-dropdown-content').html('<p>An error occurred. Please try again.</p>');
                    }
                });
            });

            // Handle Add to Cart functionality
            $('.add-to-cart-form').on('submit', function (e) {
                e.preventDefault(); // Prevent form submission
                const form = $(this);
                $.ajax({
                    url: 'add_to_cart.php',
                    type: 'POST',
                    data: form.serialize(),
                    success: function (response) {
                        alert('Product added to cart!');
                    },
                    error: function () {
                        alert('An error occurred. Please try again.');
                    }
                });
            });

            // Add this to your jQuery document ready function
            $('.cart-icon').hover(function() {
                $.get('fetch_cart.php', function(data) {
                    $('#cart-preview').html(data);
                });
            });
        });

        let currentSlide = 0;
        const slides = document.querySelectorAll('.slideshow-container img');
        const dots = document.querySelectorAll('.slideshow-dots .dot');

        function showSlide(index) {
            slides[currentSlide].classList.remove('active');
            dots[currentSlide].classList.remove('active');
            currentSlide = index;
            slides[currentSlide].classList.add('active');
            dots[currentSlide].classList.add('active');
        }

        function nextSlide() {
            showSlide((currentSlide + 1) % slides.length);
        }

        setInterval(nextSlide, 3000); // Change slide every 3 seconds
    </script>
</body>
</html>