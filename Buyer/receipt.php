<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="receipt.css?version=6">
    <title>Receipt</title>
</head>
<body>

<h1>Receipt</h1>
<h1>LAZSHOPEE</h1>
<h2><a href="purchase_result.php"><button>Back</button></a></h2>
<div></div>

<?php
include "../Admin/database.php";
include "functions.php";

if (!isset($_SESSION['acct_email'])) {
    die("You need to log in to view your order history.");
}

$buyer_email = $_SESSION['acct_email'];
$buyer_id_query = "SELECT buyer_id, buyer_name FROM buyer_info WHERE buyer_email = ?";
$stmt = mysqli_prepare($conn, $buyer_id_query);
mysqli_stmt_bind_param($stmt, "s", $buyer_email);
mysqli_stmt_execute($stmt);
$buyer_id_result = mysqli_stmt_get_result($stmt);
$buyer_id_row = mysqli_fetch_assoc($buyer_id_result);
$buyer_id = $buyer_id_row['buyer_id'];
$buyer_name = $buyer_id_row['buyer_name'];

if (!$buyer_id) {
    die("Buyer ID not found.");
}

$latest_order_query = "
    SELECT order_id 
    FROM orders_history
    WHERE buyer_id = ? 
    ORDER BY order_date DESC 
    LIMIT 1";

$latest_order_stmt = mysqli_prepare($conn, $latest_order_query);
mysqli_stmt_bind_param($latest_order_stmt, "i", $buyer_id);
mysqli_stmt_execute($latest_order_stmt);
$latest_order_result = mysqli_stmt_get_result($latest_order_stmt);
$latest_order_row = mysqli_fetch_assoc($latest_order_result);

if (!$latest_order_row) {
    die("No orders found.");
}

$latest_order_id = $latest_order_row['order_id']; 

$order_date = date("Y-m-d H:i:s");
$formatted_date = date("F j, Y", strtotime($order_date));

$get_order_details = "
    SELECT 
        p.product_name AS 'Product Name', 
        p.product_price AS 'Product Price', 
        oh.order_quantity AS 'Order Quantity', 
        oh.total_amount AS 'Total Amount',
        oh.payment_method AS 'Payment Method'
    FROM 
        orders_history oh
    JOIN 
        products p ON p.product_id = oh.product_id
    WHERE 
        oh.order_id = ?";

$get_order_stmt = mysqli_prepare($conn, $get_order_details);
mysqli_stmt_bind_param($get_order_stmt, "i", $latest_order_id);
$execute_result = mysqli_stmt_execute($get_order_stmt);

if ($execute_result === false) {
    die("Error executing statement: " . mysqli_stmt_error($get_order_stmt));
}

$result = mysqli_stmt_get_result($get_order_stmt);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result); 

    echo "<div class='receipt'>";
    echo "<p><strong>Order ID:</strong> {$latest_order_id}</p>";
    echo "<p><strong>Order Date:</strong> {$formatted_date}</p>"; 
    echo "<p><strong>Buyer Name:</strong> {$buyer_name}</p>";
    echo "<p><strong>Payment Method:</strong> {$row['Payment Method']}</p>";
    echo "<hr>";

    echo "<div class='item' style='font-weight: bold;'>";
    echo "<span class='product-name'>Product Name</span>";
    echo "<span class='product-price'>Price</span>";
    echo "<span class='order-quantity'>Quantity</span>";
    echo "<span class='total-amount'>Total</span>";
    echo "</div>";
    echo "<hr>";

    do {
        echo "<div class='item'>";
        echo "<span class='product-name'>{$row['Product Name']}</span>";
        echo "<span class='product-price'>₱" . number_format($row['Product Price'], 2) . "</span>";
        echo "<span class='order-quantity'>{$row['Order Quantity']}</span>";
        echo "<span class='total-amount'>₱" . number_format($row['Total Amount'], 2) . "</span>";
        echo "</div>";
    } while ($row = mysqli_fetch_assoc($result));
} else {
    echo "<p>No order details found.</p>";
}

mysqli_stmt_close($get_order_stmt);
mysqli_stmt_close($latest_order_stmt);

echo "</div>";
?>

<div class="button-container">
<h2><a href="pdf.php" target="_blank"><button>View PDF</button></a></h2>

</div>

</body>
</html>
