<?php
session_start();
require_once '../db.php';

// Include Farmer Sidebar / Navigation
include 'farmer_home.php';

if (!isset($_SESSION['f_id'])) {
    header("Location: farmer_login.php");
    exit;
}

$farmer_id = $_SESSION['f_id'];
$msg = "";

// Fetch farmer info
$sql = "SELECT * FROM farmers WHERE f_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $farmer_id);
$stmt->execute();
$result = $stmt->get_result();
$farmer = $result->fetch_assoc();

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['f_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $farm_location = $_POST['farm_location'];
    $password = $_POST['password'];

    if(!empty($password)){
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $update = "UPDATE farmers SET f_name=?, email=?, phone=?, farm_location=?, password=? WHERE f_id=?";
        $stmt2 = $conn->prepare($update);
        $stmt2->bind_param("sssssi",$name,$email,$phone,$farm_location,$hashed,$farmer_id);
    } else {
        $update = "UPDATE farmers SET f_name=?, email=?, phone=?, farm_location=? WHERE f_id=?";
        $stmt2 = $conn->prepare($update);
        $stmt2->bind_param("ssssi",$name,$email,$phone,$farm_location,$farmer_id);
    }

    if($stmt2->execute()) $msg="Profile successfully updated!";
    else $msg="Error updating profile";

    $stmt->execute();
    $result=$stmt->get_result();
    $farmer = $result->fetch_assoc();
    $stmt2->close();
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Farmer Profile</title>
<style>
body{font-family:Arial;background:#eef2f7;color:#333;}
.main{margin-left:250px;padding:30px; max-width:700px;}
.profile-box, form{background:#fff;padding:25px;border-radius:12px;box-shadow:0 6px 18px rgba(0,0,0,.1); margin-top:20px;}
label{display:block;margin-top:15px;font-weight:600;}
input{width:100%;padding:12px;margin-top:5px;border:1px solid #ccc;border-radius:8px;}
button{margin-top:20px;padding:12px 25px;background:#28a745;color:#fff;border:none;border-radius:8px;cursor:pointer;}
button:hover{background:#218838;}
button.cancel{background:#6c757d;}
.msg{margin-top:15px;color:green;font-weight:bold;}

/* Popup */
#popupMsg{
    position:fixed;
    top:25px;
    right:25px;
    background:#28a745;
    color:#fff;
    padding:15px 20px;
    border-radius:12px;
    box-shadow:0 6px 20px rgba(0,0,0,.2);
    font-weight:600;
    display:none;
    z-index:1000;
}
</style>
<script>
function showEdit(){
    document.getElementById('viewProfile').style.display='none';
    document.getElementById('editForm').style.display='block';
}
function cancelEdit(){
    document.getElementById('editForm').style.display='none';
    document.getElementById('viewProfile').style.display='block';
}

// Show popup for 3 seconds and redirect
window.onload = function(){
    var msg = "<?= $msg ? htmlspecialchars($msg) : ''; ?>";
    if(msg){
        var popup = document.getElementById('popupMsg');
        popup.innerText = msg;
        popup.style.display = 'block';
        setTimeout(function(){
            popup.style.display = 'none';
            window.location.href = 'farmer_profile.php';
        }, 3000);
    }
};
</script>
</head>
<body>

<div class="main">

<!-- Popup -->
<div id="popupMsg"></div>

<!-- View Profile -->
<div id="viewProfile" class="profile-box">
    <p><strong>Name:</strong> <?= htmlspecialchars($farmer['f_name']); ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($farmer['email']); ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($farmer['phone']); ?></p>
    <p><strong>Farm Location:</strong> <?= htmlspecialchars($farmer['farm_location']); ?></p>
    <button onclick="showEdit()">Update Profile</button>
</div>

<!-- Edit Profile -->
<form id="editForm" class="profile-box" method="POST" style="display:none;">
    <label>Name</label>
    <input type="text" name="f_name" value="<?= htmlspecialchars($farmer['f_name']); ?>" required>
    <label>Email</label>
    <input type="email" name="email" value="<?= htmlspecialchars($farmer['email']); ?>" required>
    <label>Phone</label>
    <input type="text" name="phone" value="<?= htmlspecialchars($farmer['phone']); ?>" required>
    <label>Farm Location</label>
    <input type="text" name="farm_location" value="<?= htmlspecialchars($farmer['farm_location']); ?>" required>
    <label>Password (leave blank to keep current)</label>
    <input type="password" name="password">
    <button type="submit">Save Changes</button>
    <button type="button" class="cancel" onclick="cancelEdit()">Cancel</button>
</form>

</div>
</body>
</html>
