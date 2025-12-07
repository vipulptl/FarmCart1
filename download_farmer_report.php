<?php
include '../db.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=farmer_report.xls");

$result = $conn->query("
    SELECT 
        f.f_id AS 'Farmer ID',
        f.f_name AS 'Farmer Name',
        p.p_name AS 'Product Name',
        p.category AS 'Category',
        p.price AS 'Price (₹)',
        p.quantity AS 'Quantity'
    FROM farmers f
    JOIN products p ON f.f_id = p.f_id
    ORDER BY p.created_at DESC
");

$firstRow = true;
while ($row = $result->fetch_assoc()) {
    if ($firstRow) {
        echo implode("\t", array_keys($row)) . "\n";
        $firstRow = false;
    }
    echo implode("\t", array_values($row)) . "\n";
}
exit;
?>
