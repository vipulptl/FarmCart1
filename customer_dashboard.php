<?php
session_start();
include('db.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FarmCart | Dashboard</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Poppins", sans-serif; }
    body { background: #f5f7fa; }

    /* Navbar */
    .navbar {
      display: flex; align-items: center; justify-content: space-between;
      background-color: #05618fff; color: white;
      padding: 12px 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      position: relative;
    }
    .logo img { height: 45px; }

    .search-form { display: flex; align-items: center; gap: 10px; }
    .search-bar {
      width: 300px; padding: 8px 12px;
      border-radius: 5px; border: none; outline: none;
    }
    .search-btn {
      background: #2e7d32; color: #fff;
      border: none; padding: 8px 15px;
      border-radius: 5px; cursor: pointer;
      transition: 0.3s;
    }
    .search-btn:hover { background: #1b5e20; }

    .nav-links { position: relative; display: flex; align-items: center; gap: 25px; }
    .nav-links a { color: white; text-decoration: none; font-weight: 500; transition: 0.3s; }
    .nav-links a:hover { text-decoration: underline; }

    /* Dropdown */
    .user-menu { position: relative; display: inline-block; }
    .user-name { cursor: pointer; background: none; border: none; color: white; font-weight: 600; font-size: 16px; }
    .dropdown-content {
      display: none; position: absolute; right: 0;
      background-color: white; color: black;
      min-width: 160px; border-radius: 6px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.15); z-index: 1;
    }
    .dropdown-content a {
      color: #333; padding: 10px 15px; text-decoration: none; display: block; border-bottom: 1px solid #eee;
    }
    .dropdown-content a:hover { background-color: #f1f1f1; }
    .user-menu:hover .dropdown-content { display: block; }

    /* Categories */
    .categories {
      display: flex; justify-content: center; align-items: center; flex-wrap: wrap; gap: 20px;
      background: #fff; padding: 20px; margin: 20px auto; border-radius: 10px;
      max-width: 90%; box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .category {
      background: #f1f8e9; border-radius: 10px; padding: 10px 20px;
      text-align: center; cursor: pointer; transition: all 0.3s; width: 120px;
    }
    .category:hover { background: #c5e1a5; transform: translateY(-3px); }
    .category a { text-decoration: none; color: #33691e; font-weight: 600; }

    /* Products */
    .offers {
      background: white; margin: 25px auto; padding: 20px;
      border-radius: 10px; width: 90%;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    .offers h2 { margin-bottom: 15px; color: #1b5e20; text-align: center; }
    .offer-grid { display: flex; justify-content: center; flex-wrap: wrap; gap: 20px; }
    .offer-item {
      width: 200px; background: #fafafa; border-radius: 10px;
      padding: 15px; text-align: center; transition: all 0.3s;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .offer-item:hover { transform: translateY(-5px); box-shadow: 0 5px 12px rgba(0,0,0,0.15); }
    .offer-item img { width: 50%; height: 90px; object-fit: cover; border-radius: 10px; }
    .offer-item p { margin-top: 10px; font-size: 15px; color: #333; font-weight: 500; }
    .btn-buy {
      display: inline-block; margin-top: 8px; background: #2e7d32; color: white;
      padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 14px; transition: background 0.3s;
    }
    .btn-buy:hover { background: #1b5e20; }
  </style>
</head>
<body>

  <!-- ✅ Header -->
  <header class="navbar">
    <div class="logo">
      <img src="../uploads/farmcart_logo.jpg" alt="FarmCart Logo">
    </div>

    <!-- ✅ Search Form -->
    <form method="GET" class="search-form">
      <input type="text" name="search" placeholder="Search for farm products..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" class="search-bar">
      <button type="submit" class="search-btn">Search</button>
    </form>

    <div class="nav-links">
      <?php if(isset($_SESSION['c_id'])): ?>
        <div class="user-menu">
          <button class="user-name"><?php echo htmlspecialchars($_SESSION['c_name']); ?> ▼</button>
          <div class="dropdown-content">
            <a href="my_profile.php">Manage Profile</a>
            <a href="my_orders.php">My Orders</a>
            <a href="logout.php">Logout</a>
          </div>
        </div>
      <?php else: ?>
        <a href="login.php">Login</a>
      <?php endif; ?>
    </div>
  </header>

  <!-- ✅ Categories -->
  <section class="categories">
    <div class="category"><a href="customer_dashboard.php">All</a></div>
    <?php
      $cat_sql = "SELECT * FROM categories";
      $cat_result = $conn->query($cat_sql);
      if ($cat_result && $cat_result->num_rows > 0) {
        while($cat = $cat_result->fetch_assoc()) {
          echo '<div class="category">
                  <a href="?cat_id='.$cat['c_id'].'">'.htmlspecialchars($cat['c_name']).'</a>
                </div>';
        }
      } else {
        echo "<p>No categories found.</p>";
      }
    ?>
  </section>

  <!-- ✅ Products -->
  <section class="offers">
    <?php
      $search = isset($_GET['search']) ? trim($_GET['search']) : '';
      $where = "1"; // base condition

      // Filter by category if selected
      if (isset($_GET['cat_id'])) {
        $cat_id = intval($_GET['cat_id']);
        $cat_name_sql = "SELECT c_name FROM categories WHERE c_id = $cat_id";
        $cat_name_result = $conn->query($cat_name_sql);
        if ($cat_name_result->num_rows > 0) {
          $cat_row = $cat_name_result->fetch_assoc();
          $cat_name = $cat_row['c_name'];
          $where .= " AND category = '".$conn->real_escape_string($cat_name)."'";
          echo "<h2>Category: " . htmlspecialchars($cat_name) . "</h2>";
        }
      } else {
        echo "<h2>Available Farm Products</h2>";
      }

      // Filter by search term
      if (!empty($search)) {
        $where .= " AND p_name LIKE '%" . $conn->real_escape_string($search) . "%'";
      }

      // Final query
      $prod_sql = "SELECT * FROM products WHERE $where";
      $prod_result = $conn->query($prod_sql);
    ?>

    <div class="offer-grid">
      <?php
        if ($prod_result && $prod_result->num_rows > 0) {
          while($prod = $prod_result->fetch_assoc()) {
      ?>
      <div class="offer-item">
        <img src="../uploads/<?php echo htmlspecialchars($prod['image']); ?>" alt="">
        <p><?php echo htmlspecialchars($prod['p_name']); ?></p>
        <p>₹<?php echo number_format($prod['price'], 2); ?></p>
        <?php
          if (isset($_SESSION['c_id'])) {
            echo '<a href="by_product.php?id='.$prod['p_id'].'" class="btn-buy">Buy Now</a>';
          } else {
            echo '<a href="login.php?redirect=by_product.php?id='.$prod['p_id'].'" class="btn-buy">Buy Now</a>';
          }
        ?>
      </div>
      <?php
          }
        } else {
          echo "<p style='text-align:center;'>No products found.</p>";
        }
      ?>
    </div>
  </section>

</body>
</html>
