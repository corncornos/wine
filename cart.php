<?php
include("connectdb.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel_all'])) {
    unset($_SESSION['cart']);
    echo "success"; // Just return success status
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_quantity'])) {
    $product_id = $_POST['product_id'];
    $new_quantity = (int)$_POST['quantity'];

    if ($new_quantity > 0) {
        $_SESSION['cart'][$product_id] = $new_quantity;
    } else {
        unset($_SESSION['cart'][$product_id]);
    }

    echo "success";
    exit();
}

$cart_items = $_SESSION['cart'] ?? [];
$product_ids = array_keys($cart_items);
$result = !empty($product_ids) ? $conn->query("SELECT * FROM products WHERE id IN (" . implode(',', $product_ids) . ")") : false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
        }

        main {
            flex: 1;
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 32px;
            display: flex;
            flex-direction: column;
        }

        .cart h2 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }

        .cart form {
            display: flex;
            flex-direction: column;
            flex: 1 1 auto;
            height: 100%;
        }

        .button-group {
            margin-top: auto;
            display: flex;
            flex-direction: row; /* Changed from column to row */
            align-items: center;
            justify-content: center;
            gap: 15px; /* More space between buttons */
        }

        table {
            width: 100%;
            margin-bottom: 30px;
        }

        footer {
            background-color: rgb(2, 11, 15);
            color: white;
            text-align: center;
            padding: 1rem;
            margin-top: auto;
        }

        .btn {
            text-decoration: none;
        }

        .promo-text {
            color: #4CAF50;
            font-weight: bold;
            font-size: 0.9em;
            text-align: right;
            padding: 5px;
        }
        
        .discount-row {
            background-color: rgba(76, 175, 80, 0.1);
        }
    </style>
    <script>
        function updateQuantity(productId, quantity) {
            $.ajax({
                url: 'cart.php',
                type: 'POST',
                data: { update_quantity: 1, product_id: productId, quantity: quantity },
                success: function(response) {
                    if (response === 'success') {
                        location.reload();
                    }
                }
            });
        }
    </script>
</head>
<body>
    <header>
        <center>
            <h1 style="color: white;">Your Cart</h1>
            </center>   
    </header>
    <main class="cart">
        <h2>Cart Items</h2>
        <?php if ($result && $result->num_rows > 0): ?>
        <form method="POST" action="checkout.php">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $grand_total = 0;
                        $total_items = 0;
                        foreach($cart_items as $qty) {
                            $total_items += $qty;
                        }
                        $discount_applicable = $total_items >= 5;
                    ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <?php 
                        $quantity = $cart_items[$row['id']];
                        $total = $row['price'] * $quantity;
                        $grand_total += $total;
                    ?>
                    <tr>
                        <td><?php echo $row['name']; ?></td>
                        <td>₱<?php echo number_format($row['price'], 2); ?></td>
                        <td>
                            <input type="number" value="<?php echo $quantity; ?>" min="1" onchange="updateQuantity(<?php echo $row['id']; ?>, this.value)">
                        </td>
                        <td>₱<?php echo number_format($total, 2); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3">Subtotal</td>
                        <td>₱<?php echo number_format($grand_total, 2); ?></td>
                    </tr>
                    <?php if ($discount_applicable): ?>
                    <tr class="discount-row">
                        <td colspan="3">Discount (5% off for 5+ items)</td>
                        <td>-₱<?php echo number_format($grand_total * 0.05, 2); ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td colspan="3">VAT (12%)</td>
                        <td>₱<?php echo number_format(($discount_applicable ? $grand_total * 0.9 : $grand_total) * 0.12, 2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="3">Delivery Fee</td>
                        <td>₱36.00</td>
                    </tr>
                    <tr>
                        <td colspan="3"><strong>Total (Including VAT & Delivery)</strong></td>
                        <td><strong>₱<?php 
                            $final_total = $discount_applicable ? 
                                ($grand_total * 0.9 * 1.12) + 36 : 
                                ($grand_total * 1.12) + 36;
                            echo number_format($final_total, 2); 
                        ?></strong></td>
                    </tr>
                </tfoot>
            </table>
            <div class="payment-method">
                <center>
                <h3>Select Payment Method</h3>
                <label>
                    <input type="radio" name="payment_method" value="COD" checked>
                    Cash on Delivery (COD)
                </label>
                <label>
                    <input type="radio" name="payment_method" value="GCash">
                    GCash Payment
                </label>
                </center>
            </div>
            <div class="button-group">
                <button type="submit" name="checkout" class="btn checkout">Checkout</button>
                <button type="button" onclick="confirmCancel()" class="btn cancel">Cancel All Orders</button>
                <a href="index.php" class="btn">Back to Shop</a>
            </div>
        </form>
        <form id="cancelForm" method="POST" style="display: none;">
            <input type="hidden" name="cancel_all" value="1">
        </form>
        <script>
            function confirmCancel() {
                if (confirm("Are you sure you want to cancel all orders?")) {
                    $.ajax({
                        url: 'cart.php',
                        type: 'POST',
                        data: { cancel_all: 1 },
                        success: function(response) {
                            if (response === 'success') {
                                window.location.href = 'index.php';
                            }
                        }
                    });
                }
            }
        </script>
        <?php else: ?>
        <p>Your cart is empty.</p>
        <div class="button-group">
            <a href="index.php" class="btn ">Back to Shop</a>
        </div>
        <?php endif; ?>
    </main>
    <footer>
        <p>&copy; 2025 Beer Shop. All rights reserved.</p>
    </footer>
</body>
</html>
