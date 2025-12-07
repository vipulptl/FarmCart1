<?php
session_start();
require_once '../db.php'; // Database connection

// ✅ Check if logged in
if (!isset($_SESSION['c_id'])) {
    header("Location: login.php");
    exit;
}

// ✅ Get customer & product info
$c_id = $_SESSION['c_id'];
$p_id = $_POST['p_id'];
$quantity_label = $_POST['quantity'];
$address = trim($_POST['address']);
$payment_method = $_POST['payment_method'];

// ✅ Quantity conversion (for calculation)
switch ($quantity_label) {
    case '500 GM': $quantity_in_kg = 0.5; break;
    case '1 KG': $quantity_in_kg = 1; break;
    case '2 KG': $quantity_in_kg = 2; break;
    default: $quantity_in_kg = 1;
}

// ✅ Fetch product details
$prod_sql = "SELECT p_id, f_id, price, quantity, p_name FROM products WHERE p_id = ?";
$stmt = $conn->prepare($prod_sql);
$stmt->bind_param("i", $p_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    die("❌ Product not found!");
}

$f_id = $product['f_id'];
$available_qty = $product['quantity'];
$price = $product['price'];
$total_amount = $price * $quantity_in_kg;

// ✅ Check stock
if ($available_qty < $quantity_in_kg) {
    die("<h3 style='color:red; text-align:center;'>❌ Not enough stock available!</h3>");
}

// ✅ Insert into `orders`
$order_sql = "INSERT INTO orders (c_id, f_id, p_id, quantity, total_amount, order_status, order_date) 
              VALUES (?, ?, ?, ?, ?, 'pending', NOW())";
$stmt2 = $conn->prepare($order_sql);
$stmt2->bind_param("iiiid", $c_id, $f_id, $p_id, $quantity_in_kg, $total_amount);

if ($stmt2->execute()) {
    $order_id = $stmt2->insert_id;

    // ✅ Reduce product quantity
    $new_qty = $available_qty - $quantity_in_kg;
    $update_sql = "UPDATE products SET quantity = ? WHERE p_id = ?";
    $stmt3 = $conn->prepare($update_sql);
    $stmt3->bind_param("di", $new_qty, $p_id);
    $stmt3->execute();

    // ✅ Insert into payments table
    $payment_status = ($payment_method === 'COD') ? 'pending' : 'paid';
    $pay_sql = "INSERT INTO payments (order_id, amount, payment_method, payment_status, payment_date)
                VALUES (?, ?, ?, ?, NOW())";
    $stmt4 = $conn->prepare($pay_sql);
    $stmt4->bind_param("idss", $order_id, $total_amount, $payment_method, $payment_status);
    $stmt4->execute();

    echo "<div style='
            text-align:center;
            margin:100px auto;
            background:#e8f5e9;
            color:#2e7d32;
            width:50%;
            padding:30px;
            border-radius:10px;
            box-shadow:0 0 10px rgba(0,0,0,0.1);
          '>
          ✅ <h2>Order Placed Successfully!</h2>
          <p><strong>Product:</strong> {$product['p_name']}</p>
          <p><strong>Quantity:</strong> {$quantity_label}</p>
          <p><strong>Total Amount:</strong> ₹" . number_format($total_amount, 2) . "</p>
          <p><strong>Payment Method:</strong> {$payment_method}</p>
          <a href='my_orders.php' style='display:inline-block;margin-top:15px;
             background:#2e7d32;color:white;padding:10px 20px;border-radius:5px;
             text-decoration:none;'>View My Orders</a>
          </div>";
} else {
    echo "<h3 style='color:red; text-align:center;'>❌ Failed to place order. Please try again.</h3>";
}

$stmt->close();
$stmt2->close();
$conn->close();
?>
