<?php
require_once '../db.php';
session_start();

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = trim($_POST['password']);

    if (empty($login) || empty($password)) {
        $error = "Please enter email/phone and password.";
    } else {
        $stmt = $conn->prepare("SELECT f_id, f_name, password FROM farmers WHERE email=? OR phone=?");
        $stmt->bind_param('ss', $login, $login);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $name, $db_password);
            $stmt->fetch();

            // For plain text password
            if ($password === $db_password) {
                $_SESSION['f_id'] = $id;
                $_SESSION['f_name'] = $name;
                header("Location: farmer_dashboard.php");
                exit;
            } else {
                $error = "Username or password is wrong.";
            }
        } else {
            $error = "No account found with this email or phone.";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Farmer Login</title>
<style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #f4f7f7ff, #77f9e8ff);
            color: #333;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-box {
            background: #fff;
            padding: 30px 40px;
            border-radius: 10px;
            width: 350px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .login-box h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #2E7D32;
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            color: #333;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button.btn {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            background: #4CAF50;
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }
        button.btn:hover {
            background: #388E3C;
        }
        .links {
            text-align: center;
            margin-top: 15px;
        }
        .links a {
            color: #2E7D32;
            text-decoration: none;
            font-size: 14px;
        }
        .links a:hover {
            text-decoration: underline;
        }
        .alert.error {
            background: #ffcdd2;
            color: #c62828;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            text-align: center;
        }
</style>
</head>
<body>
<div class="container">
    <div class="login-box">
        <h2>Farmer Login</h2>
        <?php if ($error): ?>
        <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="post">
            <label>Email or Phone</label>
            <input type="text" name="login" placeholder="Enter Email or Phone" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Enter Password" required>

            <button type="submit" class="btn">Login</button>
        </form>

        <div class="links">
            <a href="farmer_register.php">Register as Farmer</a> |
            <a href="forgot_password.php">Forgot Password?</a>
        </div>
    </div>
</div>
</body>
</html>
