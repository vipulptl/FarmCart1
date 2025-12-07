<?php
session_start();
require_once 'db.php';
include 'farmer_home.php';

if (!isset($_SESSION['f_id'])) {
    header("Location: farmer_login.php");
    exit;
}

$farmer_id = $_SESSION['f_id'];

// ✅ Farmer Name
$sql = "SELECT f_name FROM farmers WHERE f_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $farmer_id);
$stmt->execute();
$result = $stmt->get_result();
$farmer = $result->fetch_assoc();
$stmt->close();

// ✅ Total Products
$total_products = $conn->query("SELECT COUNT(*) as total FROM products WHERE f_id=$farmer_id")->fetch_assoc()['total'] ?? 0;

// ✅ Total Orders
$total_orders = $conn->query("SELECT COUNT(*) as total FROM orders WHERE f_id=$farmer_id")->fetch_assoc()['total'] ?? 0;

// ✅ Total Earnings (only from completed orders)
$total_earning_result = $conn->query("SELECT SUM(total_amount) AS total_earning FROM orders WHERE f_id = $farmer_id AND order_status = 'completed'");
$total_earning = $total_earning_result->fetch_assoc()['total_earning'] ?? 0;

// ✅ Orders by Status
$orderStatusCounts = [
    'pending' => 0,
    'processing' => 0,
    'completed' => 0,
    'cancelled' => 0
];

$statusQuery = $conn->query("
    SELECT order_status, COUNT(*) AS count 
    FROM orders 
    WHERE f_id = $farmer_id 
    GROUP BY order_status
");

while ($row = $statusQuery->fetch_assoc()) {
    $status = strtolower($row['order_status']);
    if (isset($orderStatusCounts[$status])) {
        $orderStatusCounts[$status] = $row['count'];
    }
}

// ✅ Recent Orders (last 5)
$recent_orders = $conn->query("SELECT * FROM orders WHERE f_id=$farmer_id ORDER BY order_date DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Farmer Dashboard - FarmCart</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f6f9; margin: 0; display: flex; }
        .main { margin-left: 250px; padding: 30px; width: 100%; }

        h1 { margin-bottom: 5px; color: #2c3e50; }
        p { color: #555; margin-bottom: 30px; }

        .cards { display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 30px; }
        .card { flex: 1; min-width: 220px; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); text-align: center; transition: 0.3s; }
        .card:hover { transform: translateY(-5px); }

        .card h3 { font-size: 22px; margin-bottom: 5px; color: #2980b9; }
        .card p { color: #555; }

        .status-cards { display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 30px; }
        .status-card {
            flex: 1; min-width: 180px; background: #fff; padding: 20px;
            border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); text-align: center;
            font-weight: bold; color: #fff;
        }
        .pending { background: #f1c40f; }
        .processing { background: #3498db; }
        .completed { background: #2ecc71; }
        .cancelled { background: #e74c3c; }

        .notifications { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        .notifications h2 { margin-bottom: 15px; color: #2c3e50; }
        .notifications ul { list-style: none; padding: 0; }
        .notifications ul li { padding: 10px; border-bottom: 1px solid #ddd; font-size: 15px; }
        .notifications ul li:last-child { border-bottom: none; }
        .view-all { display: inline-block; margin-top: 10px; color: #2980b9; text-decoration: none; font-size: 14px; }
    </style>
</head>

<body>
    <div class="main">
        <h1>👨‍🌾 Welcome, <?= htmlspecialchars($farmer['f_name']); ?>!</h1>
        <p>Here’s your farm overview on FarmCart.</p>

        <!-- ✅ Stats Cards -->
        <div class="cards">
            <div class="card">
                <h3><?= $total_products; ?></h3>
                <p>Total Products</p>
            </div>
            <div class="card">
                <h3><?= $total_orders; ?></h3>
                <p>Total Orders</p>
            </div>
            <div class="card">
                <h3>₹<?= number_format($total_earning, 2); ?></h3>
                <p>Total Earnings</p>
            </div>
        </div>

        <!-- ✅ Order Status Cards -->
        <div class="status-cards">
            <div class="status-card pending">
                🕒 Pending<br><?= $orderStatusCounts['pending']; ?>
            </div>
            <div class="status-card processing">
                ⚙️ Processing<br><?= $orderStatusCounts['processing']; ?>
            </div>
            <div class="status-card completed">
                ✅ Completed<br><?= $orderStatusCounts['completed']; ?>
            </div>
            <div class="status-card cancelled">
                ❌ Cancelled<br><?= $orderStatusCounts['cancelled']; ?>
            </div>
        </div>

        <!-- ✅ Notifications Section -->
        <div class="notifications">
            <h2>🔔 Recent Order Notifications</h2>
            <ul>
                <?php if ($recent_orders->num_rows > 0): ?>
                    <?php while ($row = $recent_orders->fetch_assoc()): ?>
                        <li>
                            Order #<?= $row['order_id']; ?> - 
                            <?= ucfirst($row['order_status']); ?> 
                            (<?= date('d M Y, h:i A', strtotime($row['order_date'])); ?>)
                        </li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li>No recent orders yet.</li>
                <?php endif; ?>
            </ul>
            <a class="view-all" href="farmer_orders.php">View All Orders →</a>
        </div>
    </div>
</body>
</html>
