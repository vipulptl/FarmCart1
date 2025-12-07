<?php
require_once 'db.php';
$message = "";
$messageClass = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    $stmt = $conn->prepare("SELECT id FROM farmers WHERE email=?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // In production: Send a reset link via email
        $message = "A password reset link has been sent to your email.";
        $messageClass = "success";
    } else {
        $message = "No account found with that email.";
        $messageClass = "error";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Forgot Password</title>
<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #e0f7fa, #80deea);
    color: #333;
}
.container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}
.card {
    background: #fff;
    padding: 30px 40px;
    border-radius: 10px;
    width: 350px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}
.card h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #00796B;
}
label {
    display: block;
    margin-top: 15px;
    font-weight: bold;
}
input[type="email"] {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
}
button {
    width: 100%;
    padding: 12px;
    margin-top: 20px;
    background: #009688;
    color: #fff;
    font-size: 16px;
    font-weight: bold;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s;
}
button:hover {
    background: #00796B;
}
.message.success {
    background: #c8e6c9;
    color: #256029;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 15px;
    text-align: center;
}
.message.error {
    background: #ffcdd2;
    color: #c62828;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 15px;
    text-align: center;
}
a {
    display: block;
    text-align: center;
    margin-top: 15px;
    color: #00796B;
    text-decoration: none;
}
a:hover {
    text-decoration: underline;
}
</style>
</head>
<body>
<div class="container">
    <div class="card">
        <h2>Forgot Password</h2>
        <?php if ($message): ?>
        <div class="message <?php echo $messageClass; ?>"><?php echo $message; ?></div>
        <?php endif; ?>
        <form method="post">
            <label>Email:</label>
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit">Send Reset Link</button>
        </form>
        <a href="farmer_login.php">Back to Login</a>
    </div>
</div>
</body>
</html>
