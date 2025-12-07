<?php
session_start();
include('db.php');

// Redirect if not logged in
if (!isset($_SESSION['c_id'])) {
    header("Location: login.php");
    exit();
}

$c_id = $_SESSION['c_id'];

// Fetch orders joined with product details
$sql = "SELECT o.*, p.p_name, p.image, p.price 
        FROM orders o
        JOIN products p ON o.p_id = p.p_id
        WHERE o.c_id = '$c_id'
        ORDER BY o.order_date DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Orders | FarmCart</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      font-family: "Poppins", sans-serif;
      background-color: #f9f9f9;
      margin: 0;
      padding: 0;
    }

    header {
      background-color: #4CAF50;
      color: white;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    header h2 {
      margin: 0;
    }

    .orders-container {
      width: 90%;
      margin: 30px auto;
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    th, td {
      text-align: center;
      padding: 12px;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: #4CAF50;
      color: white;
    }

    tr:hover {
      background-color: #f1f1f1;
    }

    img.product-img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 8px;
    }

    .status {
      padding: 5px 10px;
      border-radius: 5px;
      color: white;
      font-weight: 500;
    }

    .pending { background-color: orange; }
    .processing { background-color: blue; }
    .completed { background-color: green; }
    .cancelled { background-color: red; }

    .back-btn {
      display: inline-block;
      margin-top: 20px;
      background-color: #4CAF50;
      color: white;
      text-decoration: none;
      padding: 10px 15px;
      border-radius: 5px;
      font-weight: 500;
    }

    .back-btn:hover {
      background-color: #45a049;
    }
  </style>
</head>
<body>

<header>
  <h2>My Orders</h2>
  <a href="customer_dashboard.php" style="color:white; text-decoration:none;">🏠 Dashboard</a>
</header>

<div class="orders-container">
  <?php if ($result && $result->num_rows > 0): ?>
  <table>
    <tr>
      <th>Product</th>
      <th>Name</th>
      <th>Quantity</th>
      <th>Total Amount</th>
      <th>Status</th>
      <th>Order Date</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
      <td><img src="../uploads/<?php echo $row['image']; ?>" class="product-img" alt=""></td>
      <td><?php echo $row['p_name']; ?></td>
      <td><?php echo $row['quantity']; ?> KG</td>
      <td>₹<?php echo number_format($row['total_amount'], 2); ?></td>
      <td><span class="status <?php echo strtolower($row['order_status']); ?>">
        <?php echo ucfirst($row['order_status']); ?></span>
      </td>
      <td><?php echo date('d M Y, h:i A', strtotime($row['order_date'])); ?></td>
    </tr>
    <?php endwhile; ?>
  </table>
  <?php else: ?>
    <p style="text-align:center; font-size:18px;">No orders found yet.</p>
  <?php endif; ?>

  <div style="text-align:center;">
    <a href="customer_dashboard.php" class="back-btn">← Back to Dashboard</a>
  </div>
</div>

</body>
</html>
