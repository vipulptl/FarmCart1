<?php
session_start();
include '../db.php';

// ✅ Search & Filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category_filter = isset($_GET['category']) ? $_GET['category'] : 'all';

// ✅ Fetch Categories
$categories = [];
$result = $conn->query("SELECT * FROM categories");
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}

// ✅ Fetch Products
$sql = "SELECT products.*, categories.name AS category 
        FROM products 
        JOIN categories ON products.category_id = categories.id
        WHERE 1";

if ($search != '') {
    $sql .= " AND products.name LIKE '%$search%'";
}
if ($category_filter != 'all') {
    $sql .= " AND categories.id = '$category_filter'";
}

$products = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Product Status - FarmCart</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fc; margin: 0; padding: 0; }
        .container { width: 90%; margin: auto; padding: 20px; }
        h1 { text-align: center; margin-bottom: 20px; }
        form { text-align: center; margin-bottom: 20px; }
        input, select { padding: 8px; margin: 5px; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        table th, table td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        table th { background: #4e73df; color: #fff; }
        .status { font-weight: bold; padding: 5px 10px; border-radius: 4px; }
        .available { color: green; }
        .out { color: red; }
        img { border-radius: 6px; object-fit: cover; }
    </style>
</head>
<body>

<div class="container">
    <h1>📦 Product Status</h1>

    <!-- 🔍 Search & Filter -->
    <form method="GET">
        <input type="text" name="search" placeholder="Search by product name" value="<?= $search ?>">
        <select name="category">
            <option value="all">-- All Categories --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $category_filter == $cat['id'] ? 'selected' : '' ?>>
                    <?= $cat['name'] ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Search</button>
    </form>

    <!-- 📊 Product Status Table -->
    <table>
        <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Product</th>
            <th>Category</th>
            <th>Price (₹)</th>
            <th>Quantity</th>
            <th>Status</th>
        </tr>
        <?php while ($row = $products->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td>
                    <?php 
                    $folder = "uploads/" . $row['category'];
                    $imagePath = $folder . "/" . $row['image']; 
                    if (!empty($row['image']) && file_exists("../" . $imagePath)): ?>
                        <img src="../<?= $imagePath ?>" width="60" height="60">
                    <?php else: ?>
                        No Image
                    <?php endif; ?>
                </td>
                <td><?= $row['name'] ?></td>
                <td><?= $row['category'] ?></td>
                <td><?= number_format($row['price'], 2) ?></td>
                <td><?= $row['quantity'] ?></td>
                <td>
                    <?php if ($row['quantity'] > 0): ?>
                        <span class="status available">Available</span>
                    <?php else: ?>
                        <span class="status out">Out of Stock</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>
