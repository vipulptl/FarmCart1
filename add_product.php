<?php
session_start();
include '../db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name  = $conn->real_escape_string($_POST['name']);
    $price = $conn->real_escape_string($_POST['price']);
    $cat   = $conn->real_escape_string($_POST['category']);

    $img_name = uniqid('product_') . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $img_path = "../uploads/" . $img_name;

    move_uploaded_file($_FILES['image']['tmp_name'], $img_path);

    $sql = "INSERT INTO products (name, price, category, image) VALUES ('$name', '$price', '$cat', '$img_name')";
    if ($conn->query($sql)) {
        echo "<div class='success'>✅ Product added successfully!</div>";
    } else {
        echo "<div class='error'>❌ Error: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product - Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f3f3;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        input[type="text"],
        input[type="number"],
        input[type="file"],
        button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            background-color: #28a745;
            color: white;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }

        .success, .error {
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
            padding: 10px;
            border-radius: 6px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Add New Product</h2>

    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Product Name" required>
        <input type="number" name="price" placeholder="Price (₹)" required>
        <input type="text" name="category" placeholder="Category" required>
        <input type="file" name="image" required>
        <button type="submit">Add Product</button>
    </form>
</div>

</body>
</html>
