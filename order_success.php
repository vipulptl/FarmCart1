<?php
session_start();
if (!isset($_SESSION['c_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Success | FarmCart</title>
  <style>
    body {
      font-family: "Poppins", sans-serif;
      background-color: #f5f7fa;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .box {
      background: #fff;
      padding: 40px;
      border-radius: 12px;
      text-align: center;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    h2 {
      color: #2e7d32;
      margin-bottom: 15px;
    }
    p {
      color: #333;
      font-size: 16px;
      margin-bottom: 20px;
    }
    a {
      background: #2e7d32;
      color: white;
      padding: 10px 20px;
      border-radius: 6px;
      text-decoration: none;
      font-size: 15px;
    }
    a:hover {
      background: #1b5e20;
    }
  </style>
</head>
<body>
  <div class="box">
    <h2>🎉 Order Placed Successfully!</h2>
    <p>Thank you for purchasing <strong><?php echo htmlspecialchars($_GET['p_name']); ?></strong>.</p>
    <p>Your order will be delivered soon.</p>
    <a href="customer_dashboard.php">Back to Dashboard</a>
  </div>
</body>
</html>
