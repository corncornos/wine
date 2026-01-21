<?php
include("connectdb.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : "Guest";
    $payment_method = $_POST['payment_method'] ?? 'COD';

    foreach ($_SESSION['cart'] as $product_id => $qty) {
        $product_sql = "SELECT price FROM products WHERE id = $product_id";
        $product_result = $conn->query($product_sql);
        $product = $product_result->fetch_assoc();

        if ($product) {
            $total_price = $product['price'] * $qty;

            // Insert transaction into the transactions table
            $transaction_sql = "INSERT INTO transactions (username, product_id, quantity, total_price, payment_method) 
                                VALUES ('$username', $product_id, $qty, $total_price, '$payment_method')";
            $conn->query($transaction_sql);

            // Decrease product quantity
            $conn->query("UPDATE products SET quantity = quantity - $qty WHERE id = $product_id AND quantity >= $qty");
        }
    }

    // Clear the cart after checkout
    unset($_SESSION['cart']);
    echo json_encode(['status' => 'success', 'message' => 'Checkout successful! Your transaction has been recorded.']);
    exit();
}

echo json_encode(['status' => 'error', 'message' => 'Your cart is empty or an error occurred.']);
exit();
