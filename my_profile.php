<?php
session_start();
include('db.php');

// Redirect if not logged in
if (!isset($_SESSION['c_id'])) {
  header("Location: login.php");
  exit();
}

$c_id = $_SESSION['c_id'];

// Fetch user data
$query = "SELECT * FROM customer WHERE c_id='$c_id'";
$result = $conn->query($query);
if ($result->num_rows > 0) {
  $user = $result->fetch_assoc();
} else {
  echo "User not found.";
  exit();
}

// Update user details
if (isset($_POST['update'])) {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $phone = $_POST['phone'];
  $address = $_POST['address'];
  $city = $_POST['city'];
  $state = $_POST['state'];
  $pincode = $_POST['pincode'];

  $sql = "UPDATE customer 
          SET c_name='$name', email='$email', phone='$phone', address='$address',
              city='$city', state='$state', pincode='$pincode'
          WHERE c_id='$c_id'";

  if ($conn->query($sql)) {
    echo "<script>alert('Profile updated successfully!'); window.location='my_profile.php';</script>";
  } else {
    echo "<script>alert('Error updating profile!');</script>";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile | FarmCart</title>
  <style>
    body {
      font-family: "Poppins", sans-serif;
      background-color: #f8faf9;
      margin: 0;
      padding: 0;
    }

    /* Navbar */
    .navbar {
      background-color: #2e7d32;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 40px;
    }

    .navbar h2 {
      font-size: 22px;
    }

    .navbar a {
      color: white;
      text-decoration: none;
      margin-left: 20px;
      font-weight: 500;
    }

    /* Profile box */
    .profile-container {
      max-width: 600px;
      background: white;
      padding: 30px 40px;
      margin: 50px auto;
      border-radius: 12px;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }

    .profile-container h2 {
      text-align: center;
      color: #2e7d32;
      margin-bottom: 25px;
    }

    label {
      display: block;
      font-weight: 600;
      margin-top: 12px;
      color: #333;
    }

    input, textarea {
      width: 100%;
      padding: 10px;
      margin-top: 6px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 15px;
      resize: none;
    }

    input[readonly], textarea[readonly] {
      background-color: #f3f3f3;
      cursor: not-allowed;
    }

    .btn-edit, .btn-update {
      width: 100%;
      background-color: #2e7d32;
      color: white;
      border: none;
      padding: 12px;
      border-radius: 6px;
      font-size: 16px;
      font-weight: 600;
      margin-top: 20px;
      cursor: pointer;
      transition: 0.3s;
    }

    .btn-edit:hover, .btn-update:hover {
      background-color: #1b5e20;
    }

    .footer {
      text-align: center;
      margin-top: 40px;
      color: #777;
      font-size: 14px;
    }

    @media (max-width: 600px) {
      .profile-container {
        width: 90%;
        padding: 20px;
      }
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <div class="navbar">
    <h2>FarmCart</h2>
    <div>
      <a href="customer_dashboard.php">Dashboard</a>
      <a href="logout.php">Logout</a>
    </div>
  </div>

  <!-- Profile Container -->
  <div class="profile-container">
    <h2>My Profile</h2>
    <form method="POST" action="">
      <label>Full Name</label>
      <input type="text" name="name" value="<?php echo $user['c_name']; ?>" readonly>

      <label>Email</label>
      <input type="email" name="email" value="<?php echo $user['email']; ?>" readonly>

      <label>Phone Number</label>
      <input type="text" name="phone" value="<?php echo $user['phone']; ?>" readonly>

      <label>Address</label>
      <textarea name="address" rows="3" readonly><?php echo $user['address']; ?></textarea>

      <label>City</label>
      <input type="text" name="city" value="<?php echo $user['city']; ?>" readonly>

      <label>State</label>
      <input type="text" name="state" value="<?php echo $user['state']; ?>" readonly>

      <label>Pincode</label>
      <input type="text" name="pincode" value="<?php echo $user['pincode']; ?>" readonly>

      <button type="button" id="editBtn" class="btn-edit" onclick="enableEdit()">Edit Profile</button>
      <button type="submit" name="update" id="updateBtn" class="btn-update" style="display:none;">Save Changes</button>
    </form>
  </div>

  <div class="footer">
    &copy; <?php echo date('Y'); ?> FarmCart. All Rights Reserved.
  </div>

  <script>
    function enableEdit() {
      document.querySelectorAll('input, textarea').forEach(el => {
        el.removeAttribute('readonly');
      });
      document.getElementById('editBtn').style.display = 'none';
      document.getElementById('updateBtn').style.display = 'block';
    }
  </script>

</body>
</html>
