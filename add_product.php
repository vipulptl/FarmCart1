<?php
session_start();
require_once 'db.php';
require_once 'farmer_home.php';


// ✅ Check if farmer is logged in
if (!isset($_SESSION['f_id'])) {
    header("Location: farmer_login.php");
    exit;
}

$farmer_id = $_SESSION['f_id'];
$message = "";
$redirect = false; // Flag for redirect

// ✅ Handle product submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['p_name']);
    $category = trim($_POST['category']);
    $price_per_kg = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $image = "";

    // ✅ Image upload
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../uploads/";
        $image = time() . "_" . basename($_FILES['image']['name']);
        $target_file = $target_dir . $image;

        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_type, $allowed_types)) {
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $message = "❌ Failed to upload image.";
            }
        } else {
            $message = "❌ Only JPG, JPEG, PNG & GIF files are allowed.";
        }
    }

    // ✅ Insert into database if no errors
    if (empty($message)) {
        $sql = "INSERT INTO products (p_name, category, price, quantity, image, f_id) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdisi", $name, $category, $price_per_kg, $quantity, $image, $farmer_id);

        if ($stmt->execute()) {
            $message = "✅ Product added successfully! Redirecting...";
            $redirect = true;
        } else {
            $message = "❌ Database Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product - Farmer Panel</title>
    <style>
        /* body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 20px;
        } */
        .container {
            max-width: 500px;
            margin: 40px auto;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            font-weight: bold;
            color: #333;
            display: block;
            margin-bottom: 5px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }
        input[type="file"] {
            border: none;
        }
        button {
            width: 100%;
            background: #27ae60;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            background: #219150;
        }
        .message {
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 6px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Add New Product</h2>

    <?php if (!empty($message)): ?>
        <div class="message <?= strpos($message, '✅') !== false ? 'success' : 'error'; ?>">
            <?= $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Product Name</label>
            <input type="text" name="p_name" required>
        </div>
        <div class="form-group">
            <label>Category</label>
            <select name="category" required>
                <option value="">Select Category</option>
                <option value="Fruits">Fruits</option>
                <option value="Vegetables">Vegetables</option>
                <option value="Dry Fruits">Dry Fruits</option>
            </select>
        </div>
        <div class="form-group">
            <label>Price per kg (₹)</label>
            <input type="number" name="price" step="0.01" required>
        </div>
        <div class="form-group">
            <label>Available Quantity (kg)</label>
            <input type="number" name="quantity" required>
        </div>
        <div class="form-group">
            <label>Upload Image</label>
            <input type="file" name="image" accept=".jpg,.jpeg,.png,.gif">
        </div>
        <button type="submit">Add Product</button>
    </form>
</div>

<?php if ($redirect): ?>
<script>
    setTimeout(function() {
        window.location.href = "farmer_products.php";
    }, 3000); // Redirect after 3 seconds
</script>
<?php endif; ?>

</body>
</html>
