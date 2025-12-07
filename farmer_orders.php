<?php
session_start();
require_once '../db.php';
include 'farmer_home.php';

// ✅ Redirect if farmer not logged in
if (!isset($_SESSION['f_id'])) {
    header("Location: login.php");
    exit;
}

$farmer_id = $_SESSION['f_id'];
$msg = "";

// ✅ Handle order status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['order_status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['order_status'];

    $update = $conn->prepare("UPDATE orders SET order_status=? WHERE order_id=? AND f_id=?");
    $update->bind_param('sii', $status, $order_id, $farmer_id);
    if ($update->execute()) {
        $msg = "Order status updated successfully!";
    } else {
        $msg = "Failed to update order status.";
    }
    $update->close();
}

// ✅ Fetch all orders for the logged-in farmer
$sql = "SELECT o.order_id, c.c_name AS c_name, p.p_name AS p_name, o.quantity, o.total_amount, 
        o.order_status, o.order_date 
        FROM orders o
        JOIN customer c ON o.c_id = c.c_id
        JOIN products p ON o.p_id = p.p_id
        WHERE o.f_id = ?
        ORDER BY o.order_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $farmer_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Farmer Orders - FarmCart</title>
    <style>
        * {margin:0; padding:0; box-sizing:border-box; font-family: Arial, sans-serif;}
        body {background:#f4f6f9; color:#333;}
        .main {margin-left: 250px; padding: 20px;}
        .top-header {display:flex; justify-content:space-between; align-items:center; background:#fff;
            padding:10px 20px; border-radius:8px; box-shadow:0 2px 5px rgba(0,0,0,0.1);}
        .search-box, .category-dropdown {padding:6px 10px; border:1px solid #ccc; border-radius:6px;}
        .search-btn {background:#007bff; color:#fff; border:none; padding:6px 14px; border-radius:6px; cursor:pointer;}
        table {width:100%; border-collapse:collapse; margin-top:20px; background:#fff; border-radius:8px; overflow:hidden;}
        th,td {border:1px solid #ddd; padding:12px; text-align:center;}
        th {background:#f8f9fa;}
        .edit-btn {background:green; color:white; padding:6px 12px; border:none; border-radius:4px; cursor:pointer;}
        .edit-btn:hover {background:#218838;}
        .category-dropdown {cursor:pointer;}
        .msg {text-align:center; font-weight:bold;}

        /* ✅ Popup Message Styling */
        .popup-msg {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 20px;
            border-radius: 6px;
            color: #fff;
            font-weight: bold;
            box-shadow: 0 3px 8px rgba(0,0,0,0.2);
            animation: fadeOut 3s forwards;
            z-index: 9999;
        }
        .popup-msg.success { background-color: #28a745; }
        .popup-msg.error { background-color: #dc3545; }

        @keyframes fadeOut {
            0% {opacity: 1;}
            80% {opacity: 1;}
            100% {opacity: 0; display: none;}
        }
    </style>
</head>
<body>
<div class="main">
    <div class="top-header">
        <h2>Order Management</h2>
    </div>

    <!-- ✅ Popup message -->
    <?php if (!empty($msg)): ?>
        <div class="popup-msg <?= strpos($msg, 'successfully') !== false ? 'success' : 'error'; ?>">
            <?= htmlspecialchars($msg); ?>
        </div>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['order_id']; ?></td>
                <td><?= htmlspecialchars($row['c_name']); ?></td>
                <td><?= htmlspecialchars($row['p_name']); ?></td>
                <td><?= $row['quantity']; ?></td>
                <td>₹<?= $row['total_amount']; ?></td>
                <td><?= ucfirst($row['order_status']); ?></td>
                <td><?= $row['order_date']; ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="order_id" value="<?= $row['order_id']; ?>">
                        <select name="order_status" class="category-dropdown">
                            <option value="pending" <?= $row['order_status']=='pending'?'selected':''; ?>>Pending</option>
                            <option value="processing" <?= $row['order_status']=='processing'?'selected':''; ?>>Processing</option>
                            <option value="completed" <?= $row['order_status']=='completed'?'selected':''; ?>>Completed</option>
                            <option value="cancelled" <?= $row['order_status']=='cancelled'?'selected':''; ?>>Cancelled</option>
                        </select>
                        <button type="submit" class="edit-btn">Update</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No orders found.</p>
    <?php endif; ?>
</div>

<!-- ✅ Script for hiding popup after 3 seconds -->
<script>
    setTimeout(() => {
        const msg = document.querySelector('.popup-msg');
        if (msg) msg.remove();
    }, 3000);
</script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
