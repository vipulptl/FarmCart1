<?php
session_start();
include 'db.php';

// ✅ Check admin login
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

// ✅ Validate and sanitize customer ID
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $c_id = intval($_GET['id']);

    // Prepare delete query
    $stmt = $conn->prepare("DELETE FROM users WHERE c_id = ?");
    $stmt->bind_param("i", $c_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Customer deleted successfully!";
    } else {
        $_SESSION['message'] = "Error deleting customer!";
    }

    $stmt->close();
} else {
    $_SESSION['message'] = "Invalid request!";
}

// Redirect back to Manage Customers page
header("Location: manage_customers.php");
exit;
?>
