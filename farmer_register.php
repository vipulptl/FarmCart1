<?php
require_once 'db.php';
session_start();

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['f_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $farm  = trim($_POST['farm_location']);
    $pass  = $_POST['password'];

    if (empty($name) || empty($email) || empty($phone) || empty($pass)) {
        $errors[] = "All fields are required.";
    }

    if (empty($errors)) {
        // Check if email or phone already exists
        $stmt = $conn->prepare("SELECT f_id FROM farmers WHERE email=? OR phone=?");
        $stmt->bind_param('ss', $email, $phone);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = "Email or Phone already registered.";
        } else {
            // Encrypt password
            $hash = password_hash($pass, PASSWORD_BCRYPT);

            // Insert into database
            $insert = $conn->prepare("INSERT INTO farmers (f_name, email, phone, password, farm_location, created_at) 
                                      VALUES (?,?,?,?,?, NOW())");
            $insert->bind_param('sssss', $name, $email, $phone, $pass, $farm);

            if ($insert->execute()) {
                $success = "Registration successful! Redirecting to login...";
                echo "<script>
                        setTimeout(function(){
                            window.location.href = 'farmer_login.php';
                        }, 3000);
                      </script>";
            } else {
                $errors[] = "Registration failed. Try again.";
            }
            $insert->close();
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Farmer Registration</title>
<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #fffdfd, #83bcf5ff);
    color: #333;
}
.container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}
.register-box {
    background: #fff;
    padding: 30px 40px;
    border-radius: 10px;
    width: 350px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}
.register-box h2 {
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
input[type="email"],
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
.alert.success {
    background: #c8e6c9;
    color: #2e7d32;
    padding: 10px;
    margin-bottom: 10px;
    border-radius: 5px;
    text-align: center;
}
</style>
</head>
<body>
<div class="container">
    <div class="register-box">
        <h2>Farmer Registration</h2>
        <?php if ($errors): ?>
        <div class="alert error"><?php echo implode("<br>", $errors); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
        <div class="alert success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="post">
            <label>Name</label>
            <input type="text" name="f_name" placeholder="Enter Full Name" required>

            <label>Email</label>
            <input type="email" name="email" placeholder="Enter Email" required>

            <label>Phone</label>
            <input type="text" name="phone" placeholder="Enter Phone" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Enter Password" required>

            <label>Farm Location</label>
            <input type="text" name="farm_location" placeholder="Enter Farm Location">

            <button type="submit" class="btn">Register</button>
        </form>

        <div class="links">
            <a href="farmer_login.php">Already have an account? Login</a>
        </div>
    </div>
</div>
</body>
</html>
