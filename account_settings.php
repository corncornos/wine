<?php
include("connectdb.php");
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = $_POST['new_username'];
    $new_password = $_POST['new_password'];

    $sql = "UPDATE users SET username = '$new_username', password = '$new_password' WHERE username = '$username'";
    if ($conn->query($sql) === TRUE) {
        $_SESSION['username'] = $new_username;
        $message = "Account updated successfully!";
    } else {
        $message = "Error updating account: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings - Boost in Class</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #333;
            color: #333;
        }
        .settings-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            padding: 20px;
        }
        .settings-container form {
            background: rgba(0, 0, 0, 0.8);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .settings-container h2 {
            color: #FF8C00;
            font-size: 2rem;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 1rem;
            margin-bottom: 5px;
            color: #fff;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
            color: #333;
        }
        .form-group input:focus {
            outline: none;
            border-color: #E67E00;
            box-shadow: 0 0 5px rgba(230, 126, 0, 0.5);
        }
        .button-group {
            margin-top: 20px;
        }
        .button-group .btn {
            background-color: #FF8C00;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }
        .button-group .btn:hover {
            background-color: #E67E00;
        }
        .message {
            margin-top: 10px;
            font-size: 1rem;
            color: #28a745;
        }
        .error {
            color: #dc3545;
            font-size: 1rem;
        }
        .toggle-link {
            color: #007BFF;
            text-decoration: underline;
            cursor: pointer;
            font-size: 1rem;
            margin-bottom: 20px;
        }
        .toggle-link:hover {
            color: #0056b3;
        }
        .back-btn {
            background-color: #FF8C00;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
            margin-top: 20px;
            text-decoration: none;
            display: inline-block;
        }
        .back-btn:hover {
            background-color: #E67E00;
        }
        .form-container {
            display: none; /* Initially hidden */
        }
    </style>
    <script>
        function toggleForm() {
            const formContainer = document.querySelector('.form-container');
            formContainer.style.display = formContainer.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</head>
<body>
    <div class="settings-container">
        <a class="toggle-link" onclick="toggleForm()">Configure Username and Password</a>
        <div class="form-container">
            <form method="POST">
                <h2>Account Settings</h2>
                <?php if (!empty($message)): ?>
                    <p class="<?php echo strpos($message, 'successfully') !== false ? 'message' : 'error'; ?>">
                        <?php echo $message; ?>
                    </p>
                <?php endif; ?>
                <div class="form-group">
                    <label for="new_username">New Username</label>
                    <input type="text" id="new_username" name="new_username" placeholder="Enter new username" required>
                </div>
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
                </div>
                <div class="button-group">
                    <button type="submit" class="btn">Update</button>
                </div>
            </form>
        </div>
        <a href="index.php" class="back-btn">Back</a>
    </div>
</body>
</html>
