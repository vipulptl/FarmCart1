<?php
session_start();
include 'db.php';
include 'admin_home.php';

// ✅ Redirect if admin not logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

// ✅ Add New Category
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['c_name'])) {
    $c_name = trim($_POST['c_name']);
    if (!empty($c_name)) {
        $stmt = $conn->prepare("INSERT INTO categories (c_name) VALUES (?)");
        $stmt->bind_param("s", $c_name);
        $stmt->execute();
        $stmt->close();
        header("Location: manage_categories.php?success=1");
        exit;
    }
}

// ✅ Bulk Delete Categories
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_selected'])) {
    if (!empty($_POST['selected_ids'])) {
        $ids = implode(",", array_map('intval', $_POST['selected_ids']));
        $conn->query("DELETE FROM categories WHERE c_id IN ($ids)");
        header("Location: manage_categories.php?deleted=1");
        exit;
    }
}

// ✅ Fetch All Categories
$result = $conn->query("SELECT * FROM categories ORDER BY c_name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Categories - FarmCart</title>
    <style>
        body {font-family: Arial, sans-serif; background:#f4f6f9; margin:0;}
        .main {margin-left:250px; padding:20px;}
        h2 {margin-bottom:20px;}
        .form-box {
            background: #fff; padding: 15px;
            border-radius: 8px; margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        input[type="text"] {padding: 8px; width: 250px; border: 1px solid #ccc; border-radius: 6px;}
        .btn {padding: 8px 14px; border: none; border-radius: 6px; cursor: pointer;}
        .add-btn {background: #007bff; color: white;}
        .add-btn:hover {background:#0069d9;}
        .delete-btn {background: red; color: white;}
        .delete-btn:hover {background: darkred;}
        table {width:100%; border-collapse: collapse; background:#fff; border-radius:8px; overflow:hidden;}
        th, td {border: 1px solid #ddd; padding: 10px; text-align: center;}
        th {background: #f8f9fa;}
    </style>
</head>
<body>
    <div class="main">
        <h2>Manage Categories</h2>

        <!-- ✅ Add New Category -->
        <div class="form-box">
            <form method="POST">
                <input type="text" name="c_name" placeholder="Enter Category Name" required>
                <button type="submit" class="btn add-btn">➕ Add Category</button>
            </form>
        </div>

        <!-- ✅ Categories List -->
        <form method="POST">
            <table>
                <tr>
                    <th><input type="checkbox" id="select_all" onclick="toggleSelectAll(this)"></th>
                    <th>ID</th>
                    <th>Category Name</th>
                    <th>Actions</th>
                </tr>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><input type="checkbox" name="selected_ids[]" value="<?= $row['c_id'] ?>"></td>
                    <td><?= $row['c_id'] ?></td>
                    <td><?= $row['c_name'] ?></td>
                    <td>
                        <a href="delete_category.php?c_id=<?= $row['c_id'] ?>" onclick="return confirm('Delete this category?')">
                            <button type="button" class="btn delete-btn">Delete</button>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
            <br>
            <button type="submit" name="delete_selected" class="btn delete-btn" onclick="return confirm('Delete selected categories?')">Delete Selected</button>
        </form>
    </div>

    <script>
        function toggleSelectAll(source) {
            let checkboxes = document.querySelectorAll('input[name="selected_ids[]"]');
            checkboxes.forEach(cb => cb.checked = source.checked);
        }
    </script>
</body>
</html>
