<?php
include("connectdb.php");
session_start();

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Handle CRUD for Users
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update'])) {
        $sql = "UPDATE users SET username = '{$_POST['username']}', role = '{$_POST['role']}' WHERE id = {$_POST['id']}";
        $conn->query($sql);
    } elseif (isset($_POST['delete'])) {
        $sql = "DELETE FROM users WHERE id = {$_POST['id']}";
        $conn->query($sql);
    }
}

// Handle CRUD for Products
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_product'])) {
        $target_file = handleImageUpload($_FILES['image']);
        if ($target_file) {
            $sql = "INSERT INTO products (name, price, description, quantity, image) 
                    VALUES ('{$_POST['name']}', '{$_POST['price']}', '{$_POST['description']}', '{$_POST['quantity']}', '$target_file')";
            $conn->query($sql);
        }
    } elseif (isset($_POST['update_product'])) {
        $sql = "UPDATE products SET name = '{$_POST['name']}', price = '{$_POST['price']}', 
                description = '{$_POST['description']}', quantity = '{$_POST['quantity']}' WHERE id = {$_POST['id']}";
        $conn->query($sql);
    } elseif (isset($_POST['delete_product'])) {
        $id = intval($_POST['id']); // Ensure the ID is an integer

        // Delete related rows in the transactions table
        $conn->query("DELETE FROM transactions WHERE product_id = $id");

        // Delete the product
        $sql = "DELETE FROM products WHERE id = $id";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Product and related transactions deleted successfully.');</script>";
        } else {
            echo "<script>alert('Error deleting product: " . $conn->error . "');</script>";
        }
    } elseif (isset($_POST['truncate_products'])) {
        $conn->query("SET FOREIGN_KEY_CHECKS = 0"); // Disable foreign key checks
        if ($conn->query("TRUNCATE TABLE products") === TRUE) {
            echo "<script>alert('Products table truncated successfully.');</script>";
        } else {
            echo "<script>alert('Error truncating products table: " . $conn->error . "');</script>";
        }
        $conn->query("SET FOREIGN_KEY_CHECKS = 1"); // Re-enable foreign key checks
    }
}

// Function to handle image upload
function handleImageUpload($image) {
    if ($image['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($image['name']);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $allowed_types) && move_uploaded_file($image['tmp_name'], $target_file)) {
            return $target_file;
        }
    }
    return false;
}

// Fetch all users and products
$users = $conn->query("SELECT id, username, role FROM users");
$products = $conn->query("SELECT * FROM products");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Boost in Class</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .sidenav {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 0; /* Initially hidden */
            background-color: #111;
            color: #fff;
            overflow-x: hidden;
            padding-top: 20px;
            z-index: 2000;
            transition: width 0.3s ease; /* Smooth transition for opening/closing */
        }
        .sidenav a {
            display: block;
            padding: 10px 20px;
            text-decoration: none;
            color: #fff;
            font-size: 2rem;
            transition: background-color 0.3s ease;
        }
        .sidenav a:hover {
            background-color:rgb(17, 14, 14);
        }
        .sidenav .closebtn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 1.5rem;
            cursor: pointer;
            color: #fff;
        }
        .menu-btn {
            font-size: 1.5rem;
            cursor: pointer;
            color: #fff;
            background: none;
            border: none;
            padding: 10px;
        }
        header {
            text-align: center; /* Center the header content */
            padding: 20px;
            background-color: black;
            color: #fff;
        }
        header h1 {
            margin: 0;
            font-size: 2rem;
        }
        header p {
            margin: 5px 0 0;
            font-size: 1rem;
        }
    </style>
    <script>
        function confirmAction(action) {
            return confirm(`Do you really want to ${action}?`);
        }

        function openNav() {
            document.getElementById("mySidenav").style.width = "250px"; // Show sidenav
        }

        function closeNav() {
            document.getElementById("mySidenav").style.width = "0"; // Hide sidenav
        }
    </script>
</head>
<body>
    <div id="mySidenav" class="sidenav">
        <span class="closebtn" onclick="closeNav()">&times;</span>
        <a href="#manage-users">Manage Users</a>
        <a href="#manage-products">Manage Products</a>
        <a href="?logout=true">Logout</a>
    </div>
    <header>
        <button class="menu-btn" onclick="openNav()">&#9776; Menu</button>
        <h1>Admin Dashboard</h1>
    </header>
    <main>
        <!-- Manage Users -->
        <section id="manage-users" class="admin-section">
            <h2>Manage Users</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['role']; ?></td>
                        <td>
                            <form method="POST" class="inline-form" onsubmit="return confirmAction('update this user');">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <input type="text" name="username" value="<?php echo $row['username']; ?>" required>
                                <select name="role">
                                    <option value="customer" <?php echo $row['role'] == 'customer' ? 'selected' : ''; ?>>Customer</option>
                                    <option value="admin" <?php echo $row['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                </select>
                                <button type="submit" name="update" class="btn">Update</button>
                            </form>
                            <form method="POST" class="inline-form" onsubmit="return confirmAction('delete this user');">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="delete" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>

        <!-- Manage Products -->
        <section id="manage-products" class="admin-section">
            <h2>Manage Products</h2>
            <form method="POST" enctype="multipart/form-data" class="admin-form">
                <h3>Add Product</h3>
                <input type="text" name="name" placeholder="Product Name" required>
                <input type="number" name="price" step="0.01" placeholder="Price" required>
                <textarea name="description" placeholder="Description" required></textarea>
                <input type="number" name="quantity" placeholder="Quantity" required>
                <input type="file" name="image" accept="image/*" required>
                <button type="submit" name="add_product" class="btn">Add Product</button>
            </form>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($product = $products->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td><?php echo $product['name']; ?></td>
                        <td>₱<?php echo number_format($product['price'], 2); ?></td>
                        <td><?php echo $product['description']; ?></td>
                        <td><?php echo $product['quantity']; ?></td>
                        <td><img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" style="width: 50px; height: auto;"></td>
                        <td>
                            <form method="POST" class="inline-form" onsubmit="return confirmAction('update this product');">
                                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                <input type="text" name="name" value="<?php echo $product['name']; ?>" required>
                                <input type="number" name="price" step="0.01" value="<?php echo $product['price']; ?>" required>
                                <textarea name="description" required><?php echo $product['description']; ?></textarea>
                                <input type="number" name="quantity" value="<?php echo $product['quantity']; ?>" required>
                                <button type="submit" name="update_product" class="btn">Update</button>
                            </form>
                            <form method="POST" class="inline-form" onsubmit="return confirmAction('delete this product');">
                                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                <button type="submit" name="delete_product" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </main>
    <footer>
        <p>&copy; 2025 Boost in Class. All rights reserved.</p>
    </footer>
</body>
</html>
