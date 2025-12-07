<?php
include('db.php');
$msg = "";

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $pincode = $_POST['pincode'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // ✅ Check if email OR phone already exists
    $check = $conn->query("SELECT * FROM customer WHERE email='$email' OR phone='$phone'");

    if ($check->num_rows > 0) {
        $existing = $check->fetch_assoc();
        if ($existing['email'] == $email && $existing['phone'] == $phone) {
            $msg = "Email and Phone number already registered!";
        } elseif ($existing['email'] == $email) {
            $msg = "Email already registered!";
        } else {
            $msg = "Phone number already registered!";
        }
    } else {
        $sql = "INSERT INTO customer (c_name, email, phone, password, address, city, state, pincode) 
                VALUES ('$name', '$email', '$phone', '$password', '$address', '$city', '$state', '$pincode')";
        if ($conn->query($sql) === TRUE) {
            header("Location: login.php");
            exit;
        } else {
            $msg = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Signup - FarmCart</title>
<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #e8f5e9 40%, #ffffff 100%);
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }

    .register-container {
        background: #ffffff;
        width: 450px;
        padding: 40px 35px;
        border-radius: 20px;
        box-shadow: 0 8px 25px rgba(0, 128, 0, 0.15);
        text-align: center;
        transition: 0.3s;
    }

    .register-container:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(46, 125, 50, 0.25);
    }

    h2 {
        color: #2E7D32;
        margin-bottom: 25px;
        letter-spacing: 1px;
    }

    input {
        width: 100%;
        padding: 12px;
        margin: 8px 0;
        border: 1.5px solid #a5d6a7;
        border-radius: 8px;
        outline: none;
        transition: 0.2s;
    }

    input:focus {
        border-color: #2E7D32;
        box-shadow: 0 0 5px rgba(46, 125, 50, 0.3);
    }

    button {
        width: 100%;
        padding: 12px;
        margin-top: 15px;
        background: #66bb6a;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }

    button:hover {
        background: #43a047;
        transform: scale(1.02);
    }

    .msg {
        color: red;
        margin-bottom: 10px;
        font-size: 14px;
        font-weight: 500;
    }

    a {
        display: block;
        margin-top: 15px;
        color: #2E7D32;
        font-weight: 500;
        text-decoration: none;
        transition: 0.3s;
    }

    a:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>
  <div class="register-container">
    <h2>Create Customer Account</h2>
    <?php if($msg != "") echo "<p class='msg'>$msg</p>"; ?>
    <form method="post">
      <input type="text" name="name" placeholder="Full Name" required>
      <input type="email" name="email" placeholder="Email Address" required>
      <input type="text" name="phone" placeholder="Mobile Number" required>
      <input type="text" name="address" placeholder="Address" required>
      <input type="text" name="city" placeholder="City" required>
      <input type="text" name="state" placeholder="State" required>
      <input type="text" name="pincode" placeholder="Pincode" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit" name="register">Register</button>
    </form>
    <a href="login.php">Already have an account? Login here</a>
  </div>
</body>
</html>
