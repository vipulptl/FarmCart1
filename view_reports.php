<?php
session_start();
include '../db.php';
include 'admin_home.php';

// ✅ Redirect if admin not logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

// ✅ Fetch Farmer Report
$farmerReport = $conn->query("
    SELECT 
        f.f_id,
        f.f_name AS farmer_name,
        p.p_name,
        p.category,
        p.price,
        p.quantity,
        p.created_at
    FROM farmers f
    JOIN products p ON f.f_id = p.f_id
    ORDER BY p.created_at DESC
");

// ✅ Fetch Customer Report
$customerReport = $conn->query("
    SELECT 
        c.c_id,
        c.c_name AS customer_name,
        p.p_name,
        f.f_name AS farmer_name,
        o.quantity,
        o.total_amount,
        o.order_date
    FROM orders o
    JOIN customer c ON o.c_id = c.c_id
    JOIN products p ON o.p_id = p.p_id
    JOIN farmers f ON o.f_id = f.f_id
    ORDER BY o.order_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Reports | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: #f7f9fc;
            color: #333;
        }
        .container {
            margin: 60px auto;
            width: 90%;
        }
        h2 {
            text-align: center;
            color: #2d3748;
            margin-bottom: 20px;
        }
        .report-section {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-bottom: 40px;
            margin-left: 220px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #4CAF50;
            color: white;
        }
        tr:hover {
            background: #f1f1f1;
        }
        .download-btn {
            display: inline-block;
            margin-top: 15px;
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }
        .download-btn:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">

        <!-- Farmer Report -->
        <div class="report-section">
            <h2>👨‍🌾 Farmer Product Report</h2>
            <a href="download_farmer_report.php" class="download-btn">
                ⬇️ Download Farmer Report
            </a>
            <table>
                <thead>
                    <tr>
                        <th>Farmer ID</th>
                        <th>Farmer Name</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Price (₹)</th>
                        <th>Quantity</th>
                        <!-- <th>Added Date</th> -->
                    </tr>
                </thead>
                <tbody>
                    <?php if ($farmerReport->num_rows > 0): ?>
                        <?php while ($row = $farmerReport->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['f_id']; ?></td>
                                <td><?= $row['farmer_name']; ?></td>
                                <td><?= $row['p_name']; ?></td>
                                <td><?= $row['category']; ?></td>
                                <td><?= $row['price']; ?></td>
                                <td><?= $row['quantity']; ?></td>
                                <!-- <td><?= $row['created_at']; ?></td> -->
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7">No farmer products found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Customer Report -->
        <div class="report-section">
            <h2>🧑‍💼 Customer Purchase Report</h2>
            <a href="download_customer_report.php" class="download-btn">
                ⬇️ Download Customer Report
            </a>
            <table>
                <thead>
                    <tr>
                        <th>Customer ID</th>
                        <th>Customer Name</th>
                        <th>Product</th>
                        <th>Farmer</th>
                        <th>Quantity</th>
                        <th>Total Amount (₹)</th>
                        <th>Order Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($customerReport->num_rows > 0): ?>
                        <?php while ($row = $customerReport->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['c_id']; ?></td>
                                <td><?= $row['customer_name']; ?></td>
                                <td><?= $row['p_name']; ?></td>
                                <td><?= $row['farmer_name']; ?></td>
                                <td><?= $row['quantity']; ?></td>
                                <td><?= $row['total_amount']; ?></td>
                                <td><?= $row['order_date']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7">No orders found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
