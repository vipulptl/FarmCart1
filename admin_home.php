<?php
// session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Segoe UI',sans-serif;background:#f4f6f9;display:flex;min-height:100vh;}
        .sidebar{width:250px;background:#2c3e50;color:#fff;height:100vh;transition:.3s;overflow:hidden;position:fixed;}
        .sidebar .profile{padding:20px;text-align:center;background:url('https://source.unsplash.com/400x150/?mountains') no-repeat center/cover;}
        .sidebar .profile img{width:80px;height:80px;border-radius:50%;border:3px solid #fff;}
        .sidebar .profile h3{margin-top:10px;font-size:18px;color:#fff;}
        .sidebar ul{list-style:none;padding:20px 0;}
        .sidebar ul li{padding:15px 20px;transition:.3s;}
        .sidebar ul li:hover{background:#1abc9c;cursor:pointer;}
        .sidebar ul li i{margin-right:10px;}
        .sidebar ul li a{color:#fff;text-decoration:none;display:block;font-size:16px;}
        .main{margin-left:250px;padding:30px;width:100%;transition:.3s;}
        .main h1{margin-bottom:20px;}
        .main p{color:#333;line-height:1.6;}
        .toggle-btn{position:absolute;left:250px;top:20px;cursor:pointer;font-size:24px;background:#2980b9;color:#fff;border-radius:50%;width:30px;height:30px;text-align:center;line-height:30px;}

    </style>
</head>
<body>

    <div class="sidebar">
        <div class="profile">
            <img src="FarmCart logo.jpg" alt="Admin">
            <h3>FarmCart</h3>
        </div>
        <ul>
            <li><a href="admin_dashboard.php">🏠 Dashboard</a></li>
            <li><a href="manage_farmers.php">👨‍🌾 Manage Farmers</a></li>
            <li><a href="manage_customers.php">🧑‍💼 Manage Customers</a></li>
            <li><a href="manage_products.php">⚙️ Manage Products</a></li>
            <li><a href="view_reports.php">📊 View Reports</a></li>
            <li><a href="logout.php">🚪 Sign Out</a></li>
        </ul>
    </div>

    
    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const main = document.querySelector('.main');
            const toggle = document.querySelector('.toggle-btn');
            if (sidebar.style.width === "80px") {
                sidebar.style.width = "250px";
                main.style.marginLeft = "250px";
            } else {
                sidebar.style.width = "80px";
                main.style.marginLeft = "80px";
            }
        }
    </script>
</body>
</html>
