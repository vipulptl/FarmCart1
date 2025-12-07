<?php
session_start();
include('db.php');
$msg = "";

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM customer WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['c_id'] = $row['c_id'];
        $_SESSION['c_name'] = $row['c_name'];

        if (isset($_GET['redirect'])) {
            header("Location: " . $_GET['redirect']);
        } else {
            header("Location: customer_dashboard.php");
        }
        exit();
    } else {
        $msg = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Login - FarmCart</title>
<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #e8f5e9 40%, #ffffff 100%);
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .login-container {
        background: #ffffff;
        width: 380px;
        padding: 40px 35px;
        border-radius: 20px;
        box-shadow: 0 8px 25px rgba(0, 128, 0, 0.15);
        text-align: center;
        transition: 0.3s;
    }

    .login-container:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(46, 125, 50, 0.25);
    }

    h2 {
        color: #2E7D32;
        margin-bottom: 25px;
        letter-spacing: 1px;
    }

    input {
        width: 100%;
        padding: 12px;
        margin: 10px 0;
        border: 1.5px solid #a5d6a7;
        border-radius: 8px;
        outline: none;
        transition: 0.2s;
    }

    input:focus {
        border-color: #2E7D32;
        box-shadow: 0 0 5px rgba(46, 125, 50, 0.3);
    }

    button {
        width: 100%;
        padding: 12px;
        margin-top: 15px;
        background: #66bb6a;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }

    button:hover {
        background: #43a047;
        transform: scale(1.02);
    }

    .msg {
        color: red;
        margin-bottom: 10px;
        font-size: 14px;
    }

    a {
        display: block;
        margin-top: 15px;
        color: #2E7D32;
        font-weight: 500;
        text-decoration: none;
        transition: 0.3s;
    }

    a:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>
    <div class="login-container">
        <h2>Customer Login</h2>
        <?php if($msg != "") echo "<p class='msg'>$msg</p>"; ?>
        <form method="post">
            <input type="email" name="email" placeholder="Enter Email" required>
            <input type="password" name="password" placeholder="Enter Password" required>
            <button type="submit" name="login">Login</button>
        </form>
        <a href="register.php">Don't have an account? Register here</a>
    </div>
</body>
</html>
