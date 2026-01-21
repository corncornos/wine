<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    
    if (isset($_POST['remove'])) {
        unset($_SESSION['cart'][$product_id]);
    } else {
        $change = $_POST['quantity_change'];
        if (!isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] = 1;
        } else {
            $_SESSION['cart'][$product_id] += $change;
            if ($_SESSION['cart'][$product_id] < 1) {
                unset($_SESSION['cart'][$product_id]);
            }
        }
    }
}

echo 'success';
?>
