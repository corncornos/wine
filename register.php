<?php
include("connectdb.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', 'user')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('Your account is now registered!');
                window.location.href = 'login.php';
              </script>";
        exit();
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Boost in Class</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #201f2d;
            color: #333;
        }
        .register-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            padding: 20px;
        }
        .register-container form {
            background: rgba(0, 0, 0, 0.8);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .register-container h2 {
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
        .login-dialog {
            margin-top: 20px;
        }
        .login-dialog p {
            font-size: 1rem;
            color: #fff;
        }
        .login-dialog .btn {
            background-color: #007BFF;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }
        .login-dialog .btn:hover {
            background-color: #0056b3;
        }
        .error {
            color: #dc3545;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <form method="POST">
            <h2>Boost in Class</h2>
            <?php if (isset($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Choose a username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Choose a password" required>
            </div>
            <div class="button-group">
                <button type="submit" class="btn">Register</button>
            </div>
            <div class="login-dialog">
                <p>Already have an account?</p>
                <a href="login.php" class="btn">Login</a>
            </div>
        </form>
    </div>
</body>
</html>