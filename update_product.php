<?php
session_start();
include '../db.php';


if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

$id = $_GET['p_id'];
$product = $conn->query("SELECT * FROM products WHERE p_id=$id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $p_name  = $_POST['p_name'];
    $price   = $_POST['price'];
    $cat     = $_POST['category'];

    // Handle image upload
    $imagePath = $product['image']; // Default to old image
    if (isset($_FILES['image']) && $_FILES['image']['name'] != '') {
        $img_name = basename($_FILES['image']['name']);
        $img_tmp  = $_FILES['image']['tmp_name'];

        // Create folder for category if not exists
        $folder = "../uploads/$cat/";
        if (!is_dir($folder)) mkdir($folder, 0777, true);

        $imagePath = $folder . $img_name;
        move_uploaded_file($img_tmp, $imagePath);
    }

    // Update product details
    $stmt = $conn->prepare("UPDATE products SET p_name=?, price=?, category=?, image=? WHERE p_id=?");
    $stmt->bind_param("sdssi", $p_name, $price, $cat, $imagePath, $id);
    $stmt->execute();

    header("Location: manage_products.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product - FarmCart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background: #fff;
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
            width: 400px;
            text-align: center;
        }

        .form-container h2 {
            margin-bottom: 20px;
            color: #2c3e50;
        }

        .form-container input, .form-container select {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }

        .form-container input:focus, .form-container select:focus {
            border-color: #27ae60;
            outline: none;
        }

        .form-container button {
            background: #27ae60;
            color: white;
            padding: 12px;
            width: 100%;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .form-container button:hover {
            background: #219150;
        }

        .back-link {
            display: inline-block;
            margin-top: 15px;
            font-size: 14px;
            text-decoration: none;
            color: #2980b9;
        }
        .back-link:hover {
            text-decoration: underline;
        }

        .current-img {
            margin: 10px 0;
            max-width: 150px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Edit Product</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="p_name" value="<?= htmlspecialchars($product['p_name']) ?>" required>
        <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required>
        <input type="text" name="category" value="<?= htmlspecialchars($product['category']) ?>" required>

        <label>Current Image:</label><br>
        <?php if(!empty($product['image']) && file_exists($product['image'])): ?>
            <img src="<?= $product['image'] ?>" alt="Product Image" class="current-img"><br>
        <?php else: ?>
            No Image<br>
        <?php endif; ?>

        <label>Upload New Image:</label>
        <input type="file" name="image" accept="image/*">

        <button type="submit">Update Product</button>
    </form>
    <a href="manage_products.php" class="back-link">⬅ Back to Products</a>
</div>

</body>
</html>
