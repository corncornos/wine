<?php
include("connectdb.php");
session_start();

$username = isset($_SESSION['username']) ? $_SESSION['username'] : "Guest";

$sql = "SELECT t.id, p.name AS product_name, t.quantity, t.total_price, t.transaction_date, t.payment_method 
        FROM transactions t
        JOIN products p ON t.product_id = p.id
        WHERE t.username = '$username'
        ORDER BY t.transaction_date DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
            background-color: #d4b894;  /* Light brown background */
            border-radius: 15px;
        }
        
        h1 {
            text-align: center;
            color: #ffffff;  /* White text */
            margin-bottom: 30px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        table {
            width: 100%;
            background-color: #fff;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            overflow: hidden;
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
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #FF8C00;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        
        .back-btn:hover {
            background-color: #E67E00;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Transaction History</h1>
        <table>
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Payment Method</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['product_name']; ?></td>
                            <td><?php echo $row['quantity']; ?></td>
                            <td>₱<?php echo number_format($row['total_price'], 2); ?></td>
                            <td><?php echo $row['payment_method']; ?></td>
                            <td><?php echo $row['transaction_date']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No transactions found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <center><a href="index.php" class="back-btn">Back to Home</a></center>
    </div>
</body>
</html>
