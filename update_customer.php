<?php
session_start();
include 'db.php';
include 'admin_home.php';

// ✅ Redirect if admin not logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

$msg = "";

// ✅ Get customer ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_customers.php");
    exit;
}

$c_id = intval($_GET['id']);

// ✅ Fetch existing customer data
$stmt = $conn->prepare("SELECT * FROM customer WHERE c_id = ?");
$stmt->bind_param("i", $c_id);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();
$stmt->close();

if (!$customer) {
    header("Location: manage_customers.php");
    exit;
}

// ✅ Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $c_name = $_POST['c_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $pincode = $_POST['pincode'];

    $update = $conn->prepare("UPDATE customer SET c_name=?, email=?, phone=?, address=?, city=?, state=?, pincode=? WHERE c_id=?");
    $update->bind_param("sssssssi", $c_name, $email, $phone, $address, $city, $state, $pincode, $c_id);

    if ($update->execute()) {
        $msg = "Customer details updated successfully!";
        // Refresh data after update
        $customer = [
            'c_name' => $c_name,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'city' => $city,
            'state' => $state,
            'pincode' => $pincode
        ];
    } else {
        $msg = "Failed to update customer details.";
    }

    $update->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Customer - FarmCart</title>
    <style>
        * {margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif;}
        body {background: #f4f6f9; color: #333;}
        .main {margin-left: 250px; padding: 20px;}
        .container {
            background: white; padding: 30px; border-radius: 10px;
            width: 550px; margin: 40px auto; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 {text-align: center; color: #28a745; margin-bottom: 20px;}
        label {display: block; margin-top: 10px; font-weight: bold;}
        input, textarea {
            width: 100%; padding: 10px; margin-top: 5px;
            border: 1px solid #ccc; border-radius: 6px; outline: none;
        }
        input:focus, textarea:focus {border-color: #28a745;}
        button {
            width: 100%; padding: 10px; margin-top: 20px;
            border: none; border-radius: 6px; cursor: pointer;
            background: #28a745; color: white; font-weight: bold;
        }
        button:hover {background: #218838;}
        a.back-btn {
            display: block; text-align: center; margin-top: 15px;
            text-decoration: none; color: #007bff;
        }
        a.back-btn:hover {text-decoration: underline;}

        /* ✅ Popup message */
        .popup-msg {
            position: fixed; top: 20px; right: 20px;
            background: #28a745; color: white; padding: 12px 20px;
            border-radius: 6px; font-weight: bold;
            box-shadow: 0 3px 8px rgba(0,0,0,0.2);
            z-index: 9999; animation: fadeOut 3s forwards;
        }
        .popup-msg.error {background: #dc3545;}
        @keyframes fadeOut {
            0% {opacity: 1;}
            80% {opacity: 1;}
            100% {opacity: 0; display: none;}
        }
    </style>
</head>
<body>
<div class="main">
    <div class="container">
        <h2>Update Customer Details</h2>

        <!-- ✅ Popup message -->
        <?php if (!empty($msg)): ?>
            <div class="popup-msg <?= strpos($msg, 'successfully') !== false ? '' : 'error'; ?>">
                <?= htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label>Full Name</label>
            <input type="text" name="c_name" value="<?= htmlspecialchars($customer['c_name']) ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($customer['email']) ?>" required>

            <label>Phone</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($customer['phone']) ?>" required>

            <label>Address</label>
            <textarea name="address" required><?= htmlspecialchars($customer['address']) ?></textarea>

            <label>City</label>
            <input type="text" name="city" value="<?= htmlspecialchars($customer['city']) ?>" required>

            <label>State</label>
            <input type="text" name="state" value="<?= htmlspecialchars($customer['state']) ?>" required>

            <label>Pincode</label>
            <input type="text" name="pincode" value="<?= htmlspecialchars($customer['pincode']) ?>" required>

            <button type="submit">Update Customer</button>
            <a href="manage_customers.php" class="back-btn">← Back to Customer List</a>
        </form>
    </div>
</div>

<script>
    // ✅ Hide popup after 3 seconds
    setTimeout(() => {
        const msg = document.querySelector('.popup-msg');
        if (msg) msg.remove();
    }, 3000);
</script>
</body>
</html>

<?php $conn->close(); ?>
