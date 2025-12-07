<?php
session_start();
require_once 'db.php';

// Check login
if (!isset($_SESSION['f_id'])) {
    header("Location: farmer_login.php");
    exit;
}

$farmer_id = $_SESSION['f_id'];

// Validate product id
if (!isset($_GET['p_id']) || empty($_GET['p_id'])) {
    die("Invalid product ID.");
}
$p_id = intval($_GET['p_id']);

// Verify ownership
$sql = "SELECT image FROM products WHERE p_id=? AND f_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $p_id, $farmer_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    die("Product not found or no permission.");
}

// Delete product image
$upload_dir = __DIR__ . "/../uploads/"; // safer absolute path
if (!empty($product['image'])) {
    $file_path = $upload_dir . $product['image'];
    if (file_exists($file_path)) {
        unlink($file_path);
    }
}

// Delete product record
$delete_sql = "DELETE FROM products WHERE p_id=? AND f_id=?";
$stmt = $conn->prepare($delete_sql);
$stmt->bind_param("ii", $p_id, $farmer_id);

if ($stmt->execute()) {
    $stmt->close();
    header("Location: farmer_products.php?deleted=success");
    exit;
} else {
    die("Error deleting product: " . $conn->error);
}
?>
