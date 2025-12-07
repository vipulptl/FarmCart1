<?php
session_start();
require_once '../db.php'; // DB connection
include 'farmer_home.php';

// ✅ Check if farmer is logged in
if (!isset($_SESSION['f_id'])) {
    header("Location: farmer_login.php");
    exit;
}

$farmer_id = $_SESSION['f_id'];

// Fetch notifications for this farmer
$sql = "SELECT * FROM notifications WHERE farmer_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $farmer_id);
$stmt->execute();
$notifications = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications - FarmCart</title>
    <style>
        * {margin:0; padding:0; box-sizing:border-box; font-family: Arial, sans-serif;}
        body {background:#f4f6f9; color:#333;}
        .main {margin-left: 250px; padding: 20px;}
        .top-header {display:flex; justify-content:space-between; align-items:center; background:#fff;
            padding:10px 20px; border-radius:8px; box-shadow:0 2px 5px rgba(0,0,0,0.1);}
        table {width:100%; border-collapse:collapse; background:#fff; border-radius:8px; overflow:hidden; margin-top:20px;}
        th, td {border:1px solid #ddd; padding:12px; text-align:left;}
        th {background:#f8f9fa;}
        tr.unread {background:#fff7e6;}
        tr:hover {background:#f1f1f1;}
        @media (max-width:768px){
            .main {margin-left:0; padding:10px;}
            table, th, td {font-size:14px;}
        }
    </style>
</head>
<body>
<div class="main">
    <div class="top-header">
        <h2>Notifications</h2>
    </div>

    <?php if ($notifications->num_rows > 0): ?>
        <div style="overflow-x:auto;">
        <table>
            <tr>
                <th>ID</th>
                <th>Message</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
            <?php while ($row = $notifications->fetch_assoc()): ?>
            <tr class="<?= $row['status'] === 'unread' ? 'unread' : ''; ?>">
                <td><?= htmlspecialchars($row['notification_id']); ?></td>
                <td><?= htmlspecialchars($row['message']); ?></td>
                <td><?= ucfirst(htmlspecialchars($row['status'])); ?></td>
                <td><?= htmlspecialchars($row['created_at']); ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
        </div>
    <?php else: ?>
        <p>No notifications found.</p>
    <?php endif; ?>
</div>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
