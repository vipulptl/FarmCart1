<?php
session_start();
include '../db.php';

$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);

    $sql = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
    $res = $conn->query($sql);

    if ($res->num_rows == 1) {
        $_SESSION['admin'] = $username;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $msg = "Invalid Admin Username or Password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - FarmCart</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            height: 100vh;
            background: linear-gradient(135deg, #e9f5ec, #ffffff);
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            width: 380px;
            background: #fff;
            padding: 35px 30px;
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
            animation: slideIn 0.8s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        h1 {
            text-align: center;
            color: #2e7d32;
            margin-bottom: 25px;
            font-size: 24px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 14px;
            margin: 12px 0;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-size: 15px;
            transition: border 0.3s, box-shadow 0.3s;
        }

        input:focus {
            border-color: #4caf50;
            outline: none;
            box-shadow: 0 0 6px rgba(76, 175, 80, 0.4);
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #4caf50;
            color: white;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }

        button:hover {
            background-color: #388e3c;
            transform: scale(1.02);
        }

        p.error {
            text-align: center;
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }

        @media (max-width: 420px) {
            .login-card {
                width: 90%;
                padding: 25px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <h1>FarmCart Admin Login</h1>
        <form method="POST">
            <input type="text" name="username" placeholder="Admin Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
            <?php if ($msg): ?>
                <p class="error"><?= $msg ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
