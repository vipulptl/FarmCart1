<?php
session_start();
include '../db.php';
include 'farmer_home.php';

// Redirect if farmer not logged in
if (!isset($_SESSION['f_id'])) {
    header("Location: farmer_login.php");
    exit;
}

$farmer_id = $_SESSION['f_id'];

// Handle bulk delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_selected'])) {
    if (!empty($_POST['selected_products'])) {
        $ids = implode(',', array_map('intval', $_POST['selected_products']));
        $conn->query("DELETE FROM products WHERE p_id IN ($ids) AND f_id = $farmer_id");
    }
    header("Location: farmers_products.php");
    exit;
}

// Search and filter
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';

$where = "WHERE f_id = $farmer_id";
if ($search) {
    $where .= " AND p_name LIKE '%$search%'";
}
if ($category && $category != 'all') {
    $where .= " AND category='$category'";
}

$result = $conn->query("SELECT * FROM products $where ORDER BY p_id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Products - Farmer Panel</title>
<style>
* {margin:0; padding:0; box-sizing:border-box; font-family: Arial, sans-serif;}
body {background:#f4f6f9; color:#333;}
.main {margin-left:250px; padding:20px;}
.top-header {display:flex; justify-content:space-between; align-items:center; background:#fff;
    padding:10px 20px; border-radius:8px; box-shadow:0 2px 5px rgba(0,0,0,0.1); margin-bottom:20px;}
.search-box, .category-dropdown {padding:6px 10px; border:1px solid #ccc; border-radius:6px;}
.search-btn {background:#007bff; color:#fff; border:none; padding:6px 14px; border-radius:6px; cursor:pointer;}
table {width:100%; border-collapse:collapse; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,.1);}
th,td {border:1px solid #ddd; padding:12px; text-align:center; vertical-align:middle;}
th {background:#f8f9fa;}
tr:hover {background:#f1f1f1;}
.edit-btn {background:green; color:white; padding:6px 12px; border:none; border-radius:4px; cursor:pointer;}
.delete-btn {background:red; color:white; padding:6px 12px; border:none; border-radius:4px; cursor:pointer;}
.bulk-delete-btn {background:#dc3545; color:white; padding:8px 16px; border:none; border-radius:6px; cursor:pointer; margin-bottom:10px;}
.bulk-delete-btn:hover {background:#b02a37;}
.add-btn {background:#28a745; color:white; border:none; padding:6px 12px; border-radius:6px; margin-left:10px; cursor:pointer;}
.add-btn:hover {background:#218838;}
img {border-radius:4px; object-fit:cover;}
@media(max-width:768px){.main{margin-left:0;padding:15px;} table, th, td, button{font-size:14px;}}
</style>
</head>
<body>
<div class="main">
    <div class="top-header">
        <form method="get" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
            <input type="text" name="search" placeholder="Search by product name" class="search-box" value="<?= htmlspecialchars($search) ?>">
            <select name="category" class="category-dropdown">
                <option value="all">-- All Categories --</option>
                <option value="Fruits" <?= $category=="Fruits"?"selected":"" ?>>Fruits</option>
                <option value="Vegetables" <?= $category=="Vegetables"?"selected":"" ?>>Vegetables</option>
                <option value="Dry Fruits" <?= $category=="Dry Fruits"?"selected":"" ?>>Dry Fruits</option>
            </select>
            <button type="submit" class="search-btn">Search</button>
            <a href="add_product.php"><button type="button" class="add-btn">+ Add Product</button></a>
        </form>
    </div>

    <form method="post" onsubmit="return confirm('Are you sure you want to delete selected products?');">
        <button type="submit" name="delete_selected" class="bulk-delete-btn">Delete Selected</button>
        <table>
            <tr>
                <th><input type="checkbox" id="select-all"></th>
                <!-- <th>ID</th> -->
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price (₹/kg)</th>
                <th>Available Quantity (kg)</th>
                <th>Actions</th>
            </tr>

            <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><input type="checkbox" name="selected_products[]" value="<?= $row['p_id'] ?>"></td>
                <!-- <td><?= $row['p_id'] ?></td> -->

                <!-- ✅ Image Column -->
                <td>
                <?php
                        // Get category and image filename from database
                        $category = strtolower(trim($row['category']));
                        $imageName = trim($row['image']);

                        // ✅ First, look inside the category folder (e.g., uploads/fruits)
                        $folderName = str_replace(' ', '_', $category);
                        $imagePathCategory = "../uploads/$folderName/$imageName";

                        // ✅ Then, also check in main uploads folder
                        $imagePathRoot = "../uploads/$imageName";

                        // ✅ Display image if found
                        if (!empty($imageName)) {
                            if (file_exists($imagePathCategory)) {
                                echo "<img src='$imagePathCategory' width='60' height='60' style='object-fit:cover; border-radius:8px;'>";
                            } elseif (file_exists($imagePathRoot)) {
                                echo "<img src='$imagePathRoot' width='60' height='60' style='object-fit:cover; border-radius:8px;'>";
                            } else {
                                echo "<span style='color:#888;'>No Image Found</span>";
                            }
                        } else {
                            echo "<span style='color:#888;'>No Image</span>";
                        }
                ?>
                </td>

                <td><?= htmlspecialchars($row['p_name']) ?></td>
                <td><?= htmlspecialchars($row['category']) ?></td>
                <td><?= number_format($row['price'], 2) ?></td>
                <td><?= $row['quantity'] ?></td>
                <td>
                    <a href="edit_product.php?p_id=<?= $row['p_id'] ?>"><button type="button" class="edit-btn">Edit</button></a>
                </td>
            </tr>
            <?php endwhile; ?>
            <?php else: ?>
            <tr><td colspan="8">No products found.</td></tr>
            <?php endif; ?>
        </table>
    </form>
</div>

<script>
document.getElementById('select-all').addEventListener('click', function() {
    let checkboxes = document.querySelectorAll('input[name="selected_products[]"]');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>
</body>
</html>
