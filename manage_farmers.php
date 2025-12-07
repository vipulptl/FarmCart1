<?php
session_start();
include 'db.php';
include 'admin_home.php';

// ✅ Redirect if admin not logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

// ✅ Handle Bulk Delete
if (isset($_POST['bulk_delete']) && !empty($_POST['selected_ids'])) {
    $ids = implode(",", array_map('intval', $_POST['selected_ids']));
    $conn->query("DELETE FROM farmers WHERE f_id IN ($ids)");
    header("Location: manage_farmers.php?deleted=1");
    exit;
}

// ✅ Handle search
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$where = "WHERE 1";
if ($search) {
    $where .= " AND (f_name LIKE '%$search%' OR email LIKE '%$search%' OR phone LIKE '%$search%')";
}

// ✅ Fetch farmers
$result = $conn->query("SELECT * FROM farmers $where ORDER BY f_id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Farmers - FarmCart</title>
    <style>
        * {margin:0; padding:0; box-sizing:border-box; font-family: Arial, sans-serif;}
        body {background:#f4f6f9; color:#333;}

        .main {margin-left: 250px; padding: 20px;}
        .top-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 20px; padding: 10px 20px;
            background: #fff; border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .search-box {
            padding: 6px 10px; border: 1px solid #ccc;
            border-radius: 6px;
        }
        .search-btn, .bulk-btn {
            background: #007bff; color: white; border: none;
            padding: 6px 14px; border-radius: 6px; cursor: pointer;
        }
        .bulk-btn {background: red;}
        .search-btn:hover {background: #0069d9;}
        .bulk-btn:hover {background: darkred;}
        table {
            width: 100%; border-collapse: collapse; margin-top: 20px;
            background: white; border-radius: 8px; overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        th, td {border: 1px solid #ddd; padding: 12px; text-align: center;}
        th {background: #f8f9fa;}
        .edit-btn {
            background: green; color: white; padding: 6px 12px;
            border: none; border-radius: 4px; cursor: pointer;
        }
        .delete-btn {
            background: red; color: white; padding: 6px 12px;
            border: none; border-radius: 4px; cursor: pointer;
        }
    </style>
    <script>
        function toggleSelectAll(source) {
            checkboxes = document.querySelectorAll('.row-check');
            for (var i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = source.checked;
            }
        }
    </script>
</head>
<body>
    <div class="main">
        <!-- Top Header -->
        <div class="top-header">
            <form method="get" style="display:flex; gap:10px; align-items:center;">
                <input type="text" name="search" placeholder="Search by name, email or phone" class="search-box" value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="search-btn">Search</button>
            </form>
        </div>

        <!-- Farmers Table with Multiple Choice -->
        <form method="post">
            <button type="submit" name="bulk_delete" class="bulk-btn" onclick="return confirm('Delete selected farmers?')">Delete Selected</button>
            <table>
                <tr>
                    <th><input type="checkbox" onclick="toggleSelectAll(this)"></th>
                    <th>ID</th>
                    <th>Farmer Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Farm Location</th>
                    <th>Actions</th>
                </tr>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><input type="checkbox" name="selected_ids[]" value="<?= $row['f_id'] ?>" class="row-check"></td>
                    <td><?= $row['f_id'] ?></td>
                    <td><?= $row['f_name'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td><?= $row['phone'] ?></td>
                    <td><?= $row['farm_location'] ?></td>
                    <td>
                        <a href="update_farmer.php?id=<?= $row['f_id'] ?>"><button type="button" class="edit-btn">Edit</button></a>
                        <!-- <a href="delete_farmer.php?id=<?= $row['f_id'] ?>" onclick="return confirm('Delete this farmer?')">
                            <button type="button" class="delete-btn">Delete</button> -->
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </form>
    </div>
</body>
</html>
