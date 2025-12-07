<?php
session_start();
require_once 'db.php';
include 'farmer_home.php';


// ✅ Check if farmer is logged in
if (!isset($_SESSION['f_id'])) {
    header("Location: farmer_login.php");
    exit;
}

$farmer_id = $_SESSION['f_id'];
$message = "";

// ✅ Fetch product details
if (!isset($_GET['p_id']) || empty($_GET['p_id'])) {
    die("Invalid product ID.");
}

$p_id = intval($_GET['p_id']);
$sql = "SELECT * FROM products WHERE p_id = ? AND f_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $p_id, $farmer_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    die("Product not found or you don't have permission to edit it.");
}

// ✅ Update product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['p_name']);
    $category = trim($_POST['category']);
    $price_per_kg = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $image = $product['image']; // Keep old image by default

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $upload_dir = "../uploads/";
        $image = basename($_FILES['image']['name']);
        $target_file = $upload_dir . $image;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            // Optionally delete old image
            if (!empty($product['image']) && file_exists($upload_dir . $product['image'])) {
                unlink($upload_dir . $product['image']);
            }
        } else {
            $message = "❌ Failed to upload image.";
        }
    }

    if (empty($message)) {
        $update_sql = "UPDATE products SET p_name=?, category=?, price=?, quantity=?, image=? WHERE p_id=? AND f_id=?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssdisii", $name, $category, $price_per_kg, $quantity, $image, $p_id, $farmer_id);

        if ($stmt->execute()) {
            $message = "✅ Product updated successfully! Redirecting...";
            echo "<script>
                    setTimeout(function() {
                        window.location.href = 'farmer_products.php';
                    }, 3000);
                  </script>";
        } else {
            $message = "❌ Error updating product: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <style>
        
        .container { max-width: 500px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        label { font-weight: bold; display: block; margin-top: 10px; }
        input, select { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 15px; width: 100%; padding: 10px; background: #27ae60; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #219150; }
        .message { text-align: center; padding: 10px; margin-bottom: 10px; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
<div class="container">
    <h2>Edit Product</h2>
    <?php if (!empty($message)) : ?>
        <div class="message <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>"><?= $message ?></div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <label>Product Name</label>
        <input type="text" name="p_name" value="<?= htmlspecialchars($product['p_name']); ?>" required>

        <label>Category</label>
        <select name="category" required>
            <option value="Fruits" <?= $product['category']=='Fruits'?'selected':''; ?>>Fruits</option>
            <option value="Vegetables" <?= $product['category']=='Vegetables'?'selected':''; ?>>Vegetables</option>
            <option value="Dry Fruits" <?= $product['category']=='Dry Fruits'?'selected':''; ?>>Dry Fruits</option>
        </select>

        <label>Price per kg</label>
        <input type="number" step="0.01" name="price" value="<?= $product['price']; ?>" required>

        <label>Quantity (kg)</label>
        <input type="number" name="quantity" value="<?= $product['quantity']; ?>" required>

        <label>Product Image</label>
        <input type="file" name="image">
        <?php if (!empty($product['image']) && file_exists("../uploads/" . $product['image'])): ?>
            <img src="../uploads/<?= htmlspecialchars($product['image']); ?>" width="80" style="margin-top:10px;">
        <?php endif; ?>

        <button type="submit">Update Product</button>
    </form>
</div>
</body>
</html>
