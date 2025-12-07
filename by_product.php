<?php
session_start();
include('db.php');

// Redirect if not logged in
if (!isset($_SESSION['c_id'])) {
    header("Location: login.php");
    exit();
}

// Get product id from URL
if (!isset($_GET['id'])) {
    echo "<script>alert('Invalid product selection.'); window.location='customer_dashboard.php';</script>";
    exit();
}

$p_id = intval($_GET['id']);

// ✅ Fetch product
$product_query = "SELECT * FROM products WHERE p_id = $p_id";
$product_result = $conn->query($product_query);
if ($product_result->num_rows == 0) {
    echo "<script>alert('Product not found!'); window.location='customer_dashboard.php';</script>";
    exit();
}
$product = $product_result->fetch_assoc();

// ✅ Check stock availability
if ($product['quantity'] <= 0) {
    echo "<script>alert('Sorry, {$product['p_name']} is currently out of stock!'); window.location='customer_dashboard.php';</script>";
    exit();
}

// ✅ Fetch customer details
$c_id = $_SESSION['c_id'];
$customer_query = "SELECT * FROM customer WHERE c_id = $c_id";
$customer_result = $conn->query($customer_query);
$customer = $customer_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Buy Product | FarmCart</title>
  <style>
    body {
      font-family: "Poppins", sans-serif;
      background-color: #f5f7fa;
      margin: 0;
      padding: 0;
    }

    .navbar {
      background-color: #2e7d32;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 40px;
    }

    .navbar a {
      color: white;
      text-decoration: none;
      margin-left: 20px;
    }

    .container {
      max-width: 900px;
      background: white;
      margin: 40px auto;
      border-radius: 12px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
      padding: 30px;
    }

    .product-details {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 30px;
    }

    .product-details img {
      width: 300px;
      height: 250px;
      border-radius: 10px;
      object-fit: cover;
    }

    .info {
      flex: 1;
    }

    .info h2 {
      color: #2e7d32;
      margin-bottom: 10px;
    }

    .info p {
      margin-bottom: 8px;
      font-size: 16px;
      color: #333;
    }

    .price {
      font-size: 22px;
      color: #1b5e20;
      font-weight: 600;
      margin: 10px 0;
    }

    label {
      font-weight: 600;
      color: #333;
    }

    select, textarea, input[type="radio"] {
      margin-top: 5px;
    }

    select, textarea {
      width: 100%;
      padding: 8px;
      border-radius: 5px;
      border: 1px solid #ccc;
      font-size: 15px;
    }

    textarea {
      resize: none;
      height: 70px;
    }

    .btn-buy {
      background-color: #2e7d32;
      color: white;
      padding: 12px 25px;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      transition: 0.3s;
      margin-top: 15px;
      width: 100%;
    }

    .btn-buy:hover {
      background-color: #1b5e20;
    }

    .radio-group {
      margin: 10px 0;
    }

    @media (max-width: 768px) {
      .product-details {
        flex-direction: column;
      }

      .product-details img {
        width: 100%;
        height: auto;
      }
    }
  </style>
</head>
<body>

  <div class="navbar">
    <h2>FarmCart</h2>
    <div>
      <a href="customer_dashboard.php">Dashboard</a>
      <a href="my_profile.php">My Profile</a>
      <a href="logout.php">Logout</a>
    </div>
  </div>

  <div class="container">
    <div class="product-details">
      <?php
        // ✅ Dynamic image path handling
        $folderName = str_replace(' ', '_', strtolower($product['category']));
        $imagePath = "../uploads/$folderName/" . $product['image'];
        if (!empty($product['image']) && file_exists($imagePath)) {
            echo "<img src='$imagePath' alt='{$product['p_name']}'>";
        } else {
            echo "<img src='../uploads/no_image.jpg' alt='No Image'>";
        }
      ?>
      <div class="info">
        <h2><?php echo htmlspecialchars($product['p_name']); ?></h2>
        <p class="price">₹<?php echo number_format($product['price'], 2); ?></p>
        <p>Available Quantity: <b><?php echo $product['quantity']; ?> KG</b></p>

        <form action="place_order.php" method="POST">
          <input type="hidden" name="p_id" value="<?php echo $product['p_id']; ?>">
          <input type="hidden" name="f_id" value="<?php echo $product['f_id']; ?>">

          <!-- Quantity Input -->
          <label for="quantity">Enter Quantity (in KG):</label><br>
          <input type="number" name="quantity" id="quantity" min="0.5" max="<?php echo $product['quantity']; ?>" step="0.5" required><br><br>

          <!-- Address Section -->
          <label for="address">Delivery Address:</label>
          <textarea name="address" id="address" required><?php echo htmlspecialchars($customer['address'] . ', ' . $customer['city'] . ', ' . $customer['state'] . ' - ' . $customer['pincode']); ?></textarea><br><br>

          <!-- Payment Method -->
          <label>Payment Method:</label><br>
          <div class="radio-group">
            <input type="radio" name="payment_method" value="COD" required> Cash on Delivery (COD)<br>
            <input type="radio" name="payment_method" value="UPI" required> UPI Payment
          </div>

          <button type="submit" class="btn-buy">Place Order</button>
        </form>
      </div>
    </div>
  </div>

</body>
</html>
