<?php
session_start();
include 'db.php';
include 'admin_home.php';

// ✅ Redirect if admin not logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

// ✅ Handle bulk delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_selected'])) {
    if (!empty($_POST['selected_products'])) {
        $ids = implode(',', array_map('intval', $_POST['selected_products']));
        $conn->query("DELETE FROM products WHERE p_id IN ($ids)");
    }
    header("Location: manage_products.php");
    exit;
}

// ✅ Handle search and category filter
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';

$where = "WHERE 1";
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
    <title>Manage Products - FarmCart</title>
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
        .delete-btn {background:red; color:white; padding:6px 12px; border:none; border-radius:4px; cursor:pointer;}
        .bulk-delete-btn {background:#dc3545; color:white; padding:8px 16px; border:none; border-radius:6px; cursor:pointer; margin-top:10px;}
        .bulk-delete-btn:hover {background:#b02a37;}
        .cat-btn {background:#17a2b8; color:white; border:none; padding:6px 12px; border-radius:6px; margin-left:10px; cursor:pointer;}
        .cat-btn:hover {background:#138496;}
    </style>
</head>
<body>
    <div class="main">
        <div class="top-header">
            <form method="get" style="display:flex; gap:10px; align-items:center;">
                <input type="text" name="search" placeholder="Search by product name" class="search-box" value="<?= htmlspecialchars($search) ?>">
                <select name="category" class="category-dropdown">
                    <option value="all">-- All Categories --</option>
                    <option value="Fruits" <?= $category=="Fruits"?"selected":"" ?>>Fruits</option>
                    <option value="Vegetables" <?= $category=="Vegetables"?"selected":"" ?>>Vegetables</option>
                    <option value="Dry Fruits" <?= $category=="Dry Fruits"?"selected":"" ?>>Dry Fruits</option>
                </select>
                <button type="submit" class="search-btn">Search</button>
                <a href="manage_categories.php"><button type="button" class="cat-btn">+ Add Category</button></a>
            </form>
        </div>

        <form method="post" onsubmit="return confirm('Are you sure you want to delete selected products?');">
            <table>
                <button type="submit" name="delete_selected" class="bulk-delete-btn">Delete Selected</button>
                <tr>
                    <th><input type="checkbox" id="select-all"></th>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price (₹) / Kg</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><input type="checkbox" name="selected_products[]" value="<?= $row['p_id'] ?>"></td>
                    <td><?= $row['p_id'] ?></td>
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
                    <td><?= $row['p_name'] ?></td>
                    <td><?= $row['category'] ?></td>
                    <td><?= number_format($row['price'],2) ?></td>
                    <td><?= $row['quantity'] ?></td>
                    <td>
                        <a href="update_product.php?p_id=<?= $row['p_id'] ?>"><button type="button" class="edit-btn">Edit</button></a>
                        <!-- <a href="delete_product.php?p_id=<?= $row['p_id'] ?>" onclick="return confirm('Delete this product?')">
                            <button type="button" class="delete-btn">Delete</button>
                        </a> -->
                    </td>
                </tr>
                <?php endwhile; ?>
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
