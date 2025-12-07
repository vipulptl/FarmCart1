<?php
include "db.php";

if(isset($_GET['id'])){

    $farmer_id = $_GET['id'];

    // Delete farmer – products auto-delete due to CASCADE
    $sql = "DELETE FROM farmers WHERE farmer_id = $farmer_id";

    if(mysqli_query($conn, $sql)){
        echo "<script>alert('Farmer and all uploaded products deleted successfully!'); 
              window.location='manage_farmer.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
