<?php
include '../db.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=customer_report.xls");

$result = $conn->query("
    SELECT 
        c.c_name AS 'Customer Name',
        p.p_name AS 'Product Name',
        f.f_name AS 'Farmer Name',
        o.quantity AS 'Quantity',
        o.total_amount AS 'Total Amount (₹)',
        o.order_date AS 'Order Date'
    FROM orders o
    JOIN customer c ON o.c_id = c.c_id
    JOIN products p ON o.p_id = p.p_id
    JOIN farmers f ON o.f_id = f.f_id
    ORDER BY o.order_date DESC
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
