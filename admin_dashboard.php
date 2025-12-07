<?php
session_start();
include 'db.php';
include 'admin_home.php';

// ✅ Redirect if admin not logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

// ✅ Fetch dashboard stats
$products   = $conn->query("SELECT COUNT(*) AS total FROM products")->fetch_assoc()['total'];
$farmers    = $conn->query("SELECT COUNT(*) AS total FROM farmers")->fetch_assoc()['total'];
$customers  = $conn->query("SELECT COUNT(*) AS total FROM customer")->fetch_assoc()['total'];
$categories  = $conn->query("SELECT COUNT(*) AS total FROM categories")->fetch_assoc()['total'];

$pending    = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE order_status='pending'")
                   ->fetch_assoc()['total'];
$processing = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE order_status='processing'")
                   ->fetch_assoc()['total'];
$cancelled  = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE order_status='cancelled'")
                   ->fetch_assoc()['total'];
$income     = $conn->query("SELECT IFNULL(SUM(total_amount),0) AS total FROM orders WHERE order_status='completed'")
                   ->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - FarmCart</title>
    <style>
        * {margin:0; padding:0; box-sizing:border-box; font-family: Arial, sans-serif;}
        body {background:#f4f6f9; color:#333;}
        .top-header {display:flex;justify-content:space-between;align-items:center;background:#fff;padding:12px 20px;border-radius:8px;box-shadow:0 2px 6px rgba(0,0,0,.1);margin-bottom:20px;}
        .top-header form {display:flex;gap:10px;align-items:center;}
        .search-box,.category-dropdown {padding:8px 12px;border:1px solid #ccc;border-radius:6px;font-size:14px;}
        .search-btn,.cat-btn {color:#fff;border:none;padding:8px 16px;border-radius:6px;font-size:14px;cursor:pointer;transition:.3s;}
        .search-btn {background:#007bff;}
        .search-btn:hover {background:#0056b3;}
        .cat-btn {background:#17a2b8;padding:8px 14px;}
        .cat-btn:hover {background:#138496;}
        .main {margin-left:250px;padding:20px;}
        .cards {display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;margin-top:20px;}
        .card {background:#fff;padding:20px;border-radius:10px;text-align:center;box-shadow:0 2px 8px rgba(0,0,0,.1);transition:.2s;}
        .card:hover {transform:translateY(-5px);}
        .card h3 {font-size:18px;margin-bottom:10px;}
        .card p {font-size:22px;font-weight:bold;color:#007bff;}
    </style>
</head>
<body>

    <!-- Main Content -->
    <div class="main">
        <h1>📊 Dashboard Overview</h1>


        <!-- Dashboard Cards -->
        <div class="cards">

            <!-- Row 1: Farmers, Customers, Products -->
            <div class="card">
                <h3>Total Farmers</h3>
                <p><?= $farmers ?></p>
            </div>
            <div class="card">
                <h3>Total Customers</h3>
                <p><?= $customers ?></p>
            </div>
            <div class="card">
                <h3>Total Products</h3>
                <p><?= $products ?></p>
            </div>

            <div class="card">
                <h3>Total Categories</h3>
                <p><?= $categories ?></p>
            </div>

        </div>

            <!-- Orders Section -->
            <h2 style="margin-top:30px;">📦 Order Management</h2>
            <div class="cards">
                <div class="card">
                    <h3>Pending Orders</h3>
                    <p><?= $pending ?></p>
                </div>
                <div class="card">
                    <h3>Processing Orders</h3>
                    <p><?= $processing ?></p>
                </div>
                <div class="card">
                    <h3>Cancelled Orders</h3>
                    <p><?= $cancelled ?></p>
                </div>
                <div class="card">
                    <h3>Total Income</h3>
                    <p>₹<?= number_format($income, 2) ?></p>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
