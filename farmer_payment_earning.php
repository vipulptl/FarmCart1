<?php
session_start();
require_once '../db.php'; // Database connection
include 'farmer_home.php'; // Sidebar / Navbar

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ Check login
if (!isset($_SESSION['f_id'])) {
    header("Location: farmer_login.php");
    exit;
}

$farmer_id = $_SESSION['f_id'];

// ✅ Update Payment Status (Pending → Paid)
if (isset($_GET['mark_paid'])) {
    $payment_id = intval($_GET['mark_paid']);
    $update_sql = "UPDATE payments SET payment_status = 'Paid' WHERE payment_id = ?";
    $stmt_update = $conn->prepare($update_sql);
    $stmt_update->bind_param("i", $payment_id);
    if ($stmt_update->execute()) {
        echo "<script>alert('✅ Payment marked as Paid successfully!'); window.location='farmer_payment_earning.php';</script>";
        exit;
    } else {
        echo "<script>alert('❌ Error updating payment status!');</script>";
    }
    $stmt_update->close();
}

// ✅ Fetch total earnings (for completed orders only)
$total_sql = "SELECT SUM(total_amount) AS total_earning 
              FROM orders 
              WHERE f_id = ? AND order_status = 'completed'";
$stmt = $conn->prepare($total_sql);
$stmt->bind_param("i", $farmer_id);
$stmt->execute();
$total_result = $stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_earning = $total_row['total_earning'] ?? 0;

// ✅ Fetch full payment history with product & customer info
$sql = "SELECT 
            p.payment_id, 
            p.amount, 
            p.payment_method, 
            p.payment_status, 
            p.payment_date,
            o.order_id, 
            o.total_amount, 
            o.order_status,
            pr.p_name AS product_name,
            c.c_name AS customer_name
        FROM payments p
        JOIN orders o ON p.order_id = o.order_id
        JOIN products pr ON o.p_id = pr.p_id
        JOIN customer c ON o.c_id = c.c_id
        WHERE o.f_id = ?
        ORDER BY p.payment_date DESC";

$stmt2 = $conn->prepare($sql);
$stmt2->bind_param("i", $farmer_id);
$stmt2->execute();
$payments = $stmt2->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Farmer Payment & Earnings | FarmCart</title>
    <style>
        * {margin:0; padding:0; box-sizing:border-box; font-family: Arial, sans-serif;}
        body {background:#f4f6f9; color:#333;}
        .main {margin-left: 250px; padding: 20px;}
        .top-header {
            display:flex; justify-content:space-between; align-items:center;
            background:#fff; padding:10px 20px; border-radius:8px;
            box-shadow:0 2px 5px rgba(0,0,0,0.1);
        }
        .earning-box {
            background:#28a745; color:#fff; padding:15px 20px;
            border-radius:8px; margin:20px 0;
            font-size:18px; font-weight:bold;
            display:inline-block;
        }
        table {width:100%; border-collapse:collapse; background:#fff; border-radius:8px; overflow:hidden;}
        th, td {border:1px solid #ddd; padding:12px; text-align:center;}
        th {background:#f8f9fa;}
        tr:hover {background:#f1f1f1;}
        .paid {color: green; font-weight: bold;}
        .pending {color: orange; font-weight: bold;}
        .failed {color: red; font-weight: bold;}
        .btn-update {
            background:#007bff; color:white; padding:6px 10px;
            border:none; border-radius:5px; text-decoration:none; cursor:pointer;
        }
        .btn-update:hover {background:#0056b3;}
        .no-data {
            text-align:center; padding:40px; font-size:18px;
            color:#777; background:#fff; border-radius:8px;
            box-shadow:0 2px 5px rgba(0,0,0,0.1);
        }
        @media (max-width:768px){
            .main {margin-left:0; padding:10px;}
            table, th, td {font-size:14px;}
        }
    </style>
</head>
<body>
<div class="main">
    <div class="top-header">
        <h2>💰 Payment & Earnings</h2>
    </div>

    <div class="earning-box">
        Total Earnings: ₹<?= number_format($total_earning, 2); ?>
    </div>

    <h3>📜 Payment History</h3>
    <?php if ($payments->num_rows > 0): ?>
        <div style="overflow-x:auto;">
            <table>
                <tr>
                    <th>Payment ID</th>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Amount (₹)</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Order Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = $payments->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['payment_id']; ?></td>
                    <td><?= $row['order_id']; ?></td>
                    <td><?= htmlspecialchars($row['customer_name']); ?></td>
                    <td><?= htmlspecialchars($row['product_name']); ?></td>
                    <td><?= number_format($row['amount'], 2); ?></td>
                    <td><?= htmlspecialchars($row['payment_method']); ?></td>
                    <td class="<?= strtolower($row['payment_status']); ?>">
                        <?= ucfirst($row['payment_status']); ?>
                    </td>
                    <td><?= ucfirst($row['order_status']); ?></td>
                    <td><?= $row['payment_date']; ?></td>
                    <td>
                        <?php if (strtolower($row['payment_status']) == 'pending'): ?>
                            <a href="?mark_paid=<?= $row['payment_id']; ?>" 
                               class="btn-update"
                               onclick="return confirm('Mark this payment as Paid?');">Mark as Paid</a>
                        <?php else: ?>
                            ✅
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    <?php else: ?>
        <div class="no-data">
            ⚠️ No payment records found for this farmer.
        </div>
    <?php endif; ?>
</div>
</body>
</html>
<?php
$stmt->close();
$stmt2->close();
$conn->close();
?>
