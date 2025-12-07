<?php
session_start();
session_destroy();
header("Location: customer_dashboard.php");
exit;
?>
