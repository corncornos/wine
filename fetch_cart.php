<?php
include("connectdb.php");
session_start();

$cart_items = $_SESSION['cart'] ?? [];
if (empty($cart_items)) {
    echo '<div style="padding: 15px; text-align: center; color: #fff;">Your cart is empty</div>';
    exit();
}

$product_ids = implode(',', array_keys($cart_items));
$query = "SELECT * FROM products WHERE id IN ($product_ids)";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $total = 0;
    echo '<div style="max-height: 300px; overflow-y: auto;">';
    
    while ($row = $result->fetch_assoc()) {
        $quantity = $cart_items[$row['id']];
        $subtotal = $row['price'] * $quantity;
        $total += $subtotal;
        
        echo '<div style="padding: 10px; border-bottom: 1px solid rgba(255,255,255,0.1);">';
        echo '<div style="color: #FF8C00; font-weight: bold;">' . htmlspecialchars($row['name']) . '</div>';
        echo '<div style="display: flex; justify-content: space-between; align-items: center;">';
        echo '<div style="color: #fff; font-size: 12px;">₱' . number_format($row['price'], 2) . '</div>';
        echo '<div style="display: flex; align-items: center; gap: 5px;">';
        echo '<button onclick="updateQuantity(' . $row['id'] . ', -1)" style="background: #333; color: #fff; border: none; padding: 2px 6px; cursor: pointer;">-</button>';
        echo '<span style="color: #fff; padding: 0 8px;">' . $quantity . '</span>';
        echo '<button onclick="updateQuantity(' . $row['id'] . ', 1)" style="background: #333; color: #fff; border: none; padding: 2px 6px; cursor: pointer;">+</button>';
        echo '<button onclick="removeItem(' . $row['id'] . ')" style="background: #dc3545; color: #fff; border: none; padding: 2px 6px; margin-left: 5px; cursor: pointer;">×</button>';
        echo '</div></div>';
        echo '<div style="color: #fff; font-size: 12px; text-align: right;">Total: ₱' . number_format($subtotal, 2) . '</div>';
        echo '</div>';
    }
    
    echo '</div>';
    echo '<div style="padding: 15px; border-top: 2px solid #FF8C00;">';
    echo '<div style="color: #FF8C00; font-weight: bold; margin-bottom: 10px;">Total: ₱' . number_format($total, 2) . '</div>';
    echo '<a href="cart.php" style="color: #fff; text-decoration: none; background: #FF8C00; padding: 5px 10px; border-radius: 4px; font-size: 12px;">View Cart</a>';
    echo '</div>';
    
    // Add JavaScript functions for quantity updates
    echo '<script>
    function updateQuantity(productId, change) {
        $.post("update_cart.php", {
            product_id: productId,
            quantity_change: change
        }, function() {
            location.reload();
        });
    }
    
    function removeItem(productId) {
        if(confirm("Remove this item from cart?")) {
            $.post("update_cart.php", {
                product_id: productId,
                remove: true
            }, function() {
                location.reload();
            });
        }
    }
    </script>';
} else {
    echo '<div style="padding: 15px; text-align: center; color: #fff;">Your cart is empty</div>';
}
?>
