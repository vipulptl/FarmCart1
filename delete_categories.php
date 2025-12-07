<?php
include 'db.php';

if (isset($_GET['c_id'])) {
    $id = intval($_GET['c_id']);
    $conn->query("DELETE FROM categories WHERE c_id=$id");
}

header("Location: manage_categories.php");
exit;
