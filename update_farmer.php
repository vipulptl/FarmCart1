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

// ✅ Get farmer ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_farmers.php");
    exit;
}

$f_id = intval($_GET['id']);

// ✅ Fetch existing farmer data
$stmt = $conn->prepare("SELECT * FROM farmers WHERE f_id = ?");
$stmt->bind_param("i", $f_id);
$stmt->execute();
$result = $stmt->get_result();
$farmer = $result->fetch_assoc();
$stmt->close();

if (!$farmer) {
    header("Location: manage_farmers.php");
    exit;
}

// ✅ Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $f_name = $_POST['f_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $farm_location = $_POST['farm_location'];

    $update = $conn->prepare("UPDATE farmers SET f_name=?, email=?, phone=?, farm_location=? WHERE f_id=?");
    $update->bind_param("ssssi", $f_name, $email, $phone, $farm_location, $f_id);

    if ($update->execute()) {
        $msg = "Farmer details updated successfully!";
        // Refresh data after update
        $farmer['f_name'] = $f_name;
        $farmer['email'] = $email;
        $farmer['phone'] = $phone;
        $farmer['farm_location'] = $farm_location;
    } else {
        $msg = "Failed to update farmer details.";
    }

    $update->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Farmer - FarmCart</title>
    <style>
        * {margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif;}
        body {background: #f4f6f9; color: #333;}
        .main {margin-left: 250px; padding: 20px;}
        .container {
            background: white; padding: 30px; border-radius: 10px;
            width: 500px; margin: 40px auto; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 {text-align: center; color: #28a745; margin-bottom: 20px;}
        label {display: block; margin-top: 10px; font-weight: bold;}
        input {
            width: 100%; padding: 10px; margin-top: 5px;
            border: 1px solid #ccc; border-radius: 6px; outline: none;
        }
        input:focus {border-color: #28a745;}
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
        <h2>Update Farmer Details</h2>

        <!-- ✅ Popup message -->
        <?php if (!empty($msg)): ?>
            <div class="popup-msg <?= strpos($msg, 'successfully') !== false ? '' : 'error'; ?>">
                <?= htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label>Farmer Name</label>
            <input type="text" name="f_name" value="<?= htmlspecialchars($farmer['f_name']) ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($farmer['email']) ?>" required>

            <label>Phone</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($farmer['phone']) ?>" required>

            <label>Farm Location</label>
            <input type="text" name="farm_location" value="<?= htmlspecialchars($farmer['farm_location']) ?>" required>

            <button type="submit">Update Farmer</button>
            <a href="manage_farmers.php" class="back-btn">← Back to Farmers List</a>
        </form>
    </div>
</div>

<script>
    // Hide popup after 3 seconds
    setTimeout(() => {
        const msg = document.querySelector('.popup-msg');
        if (msg) msg.remove();
    }, 3000);
</script>
</body>
</html>

<?php $conn->close(); ?>
