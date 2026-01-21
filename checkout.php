<?php
include("connectdb.php");
session_start();

// Ensure the cart is not empty
if (empty($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    echo "<script>alert('Your cart is empty. Please add items to your cart before checking out.'); window.location.href = 'cart.php';</script>";
    exit();
}

// Fetch product details for items in the cart
$product_ids = implode(',', array_keys($_SESSION['cart']));
$query = "SELECT * FROM products WHERE id IN ($product_ids)";
$products_result = $conn->query($query);

if (!$products_result || $products_result->num_rows === 0) {
    echo "<script>alert('Some items in your cart are no longer available. Please update your cart.'); window.location.href = 'cart.php';</script>";
    exit();
}

$products = $products_result->fetch_all(MYSQLI_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_checkout'])) {
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : "Guest";

    // Record transactions and decrease product quantities
    foreach ($_SESSION['cart'] as $product_id => $qty) {
        $product_sql = "SELECT price FROM products WHERE id = $product_id";
        $product_result = $conn->query($product_sql);
        $product = $product_result->fetch_assoc();

        $total_price = $product['price'] * $qty;

        // Insert transaction into the transactions table
        $transaction_sql = "INSERT INTO transactions (username, product_id, quantity, total_price) 
                            VALUES ('$username', $product_id, $qty, $total_price)";
        $conn->query($transaction_sql);

        // Decrease product quantity
        $conn->query("UPDATE products SET quantity = quantity - $qty WHERE id = $product_id AND quantity >= $qty");
    }

    // Clear the cart after checkout
    unset($_SESSION['cart']);
    echo "<script>alert('Checkout successful! Your transaction has been recorded.'); window.location.href = 'index.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header>
        <center>
            <h1 style="color: white;">Checkout</h1>
            </center>  
    </header>
    <main class="checkout">
        <center><h2>Order Summary</h2></center>
        
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th class="amount">Price</th>
                    <th class="amount">Quantity</th>
                    <th class="amount">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php $grand_total = 0; ?>
                <?php foreach ($products as $product): ?>
                <?php 
                    $quantity = $_SESSION['cart'][$product['id']];
                    $total = $product['price'] * $quantity;
                    $grand_total += $total;
                ?>
                <tr>
                    <td><?php echo $product['name']; ?></td>
                    <td class="amount">₱<?php echo number_format($product['price'], 2); ?></td>
                    <td class="amount"><?php echo $quantity; ?></td>
                    <td class="amount">₱<?php echo number_format($total, 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3">Subtotal</td>
                    <td class="amount">₱<?php echo number_format($grand_total, 2); ?></td>
                </tr>
                <tr>
                    <td colspan="3">VAT (12%)</td>
                    <td class="amount">₱<?php echo number_format($grand_total * 0.12, 2); ?></td>
                </tr>
                <tr>
                    <td colspan="3">Delivery Fee</td>
                    <td class="amount">₱36.00</td>
                </tr>
                <tr class="total-row">
                    <td colspan="3"><strong>Total (Including VAT & Delivery)</strong></td>
                    <td class="amount"><strong>₱<?php echo number_format(($grand_total * 1.12) + 36, 2); ?></strong></td>
                </tr>
            </tfoot>
        </table>
        <form method="POST" id="checkout-form">
            <div class="payment-section">
                <h3>Payment Method</h3>
                <div class="payment-display">
                    <?php 
                    $payment_method = $_POST['payment_method'] ?? 'COD';
                    if ($payment_method === 'GCash'): 
                    ?>
                        <p class="payment-chosen">GCash Payment</p>
                        <div class="gcash-details">
                            <p>GCash Number: 09122998086</p>
                            <p>Account Name: Benjie A.</p>
                        </div>
                    <?php else: ?>
                        <p class="payment-chosen">Cash on Delivery (COD)</p>
                    <?php endif; ?>
                    <input type="hidden" name="payment_method" value="<?php echo htmlspecialchars($payment_method); ?>">
                </div>
                <div class="delivery-notice">
                    <p style="color: #2e7d32; margin-top: 15px; font-weight: bold;">Your order will arrive within 24 hours!</p>
                </div>
            </div>
            <center>
            <p>Are you sure you want to confirm this order?</p>
            <button type="submit" name="confirm_checkout" class="btn">Yes, Confirm Checkout</button>
            <button type="button" onclick="window.location.href='cart.php';" class="btn">No, Go Back to Cart</button>
        </center>
        </form>
    </main>
    <style>
        .checkout {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .payment-section {
            margin: 30px 0;
            text-align: center;
        }
        .payment-display {
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
            margin: 20px auto;
            max-width: 400px;
        }
        .payment-chosen {
            font-size: 1.2em;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .delivery-info {
            margin-top: 20px;
            padding: 15px;
            background-color: #e8f5e9;
            border-radius: 5px;
            color: #2e7d32;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }

        tfoot tr {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        tfoot td {
            border-top: 2px solid #ddd;
        }

        .total-row {
            background-color: #fff8e1;
            font-size: 1.1em;
        }

        .amount {
            text-align: right;
            padding-right: 20px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin: 10px;
            transition: all 0.3s ease;
        }

        button[name="confirm_checkout"] {
            background-color: #4CAF50;
            color: white;
        }

        button[name="confirm_checkout"]:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }

        button[onclick*="cart.php"] {
            background-color: #dc3545;
            color: white;
        }

        button[onclick*="cart.php"]:hover {
            background-color: #c82333;
            transform: scale(1.05);
        }
    </style>
    <footer>
        <p>&copy; 2025 Boost in Class. All rights reserved.</p>
    </footer>
    <script>
        $(document).ready(function () {
            $('#checkout-form').on('submit', function (e) {
                e.preventDefault(); // Prevent form submission
                $.ajax({
                    url: 'process_checkout.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        const res = JSON.parse(response);
                        if (res.status === 'success') {
                            alert(res.message);
                            window.location.href = 'index.php'; // Redirect to index.php
                        } else {
                            alert('An error occurred: ' + res.message);
                        }
                    },
                    error: function () {
                        alert('An error occurred. Please try again.');
                    }
                });
            });
        });
    </script>
</body>
</html>
