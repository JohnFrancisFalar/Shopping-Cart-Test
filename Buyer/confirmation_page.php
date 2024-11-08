<?php
session_start();
include "../Admin/database.php";

// Check if user is logged in
if (!isset($_SESSION['acct_email'])) {
    echo "Please log in to view the confirmation.";
    exit;
}

// Fetch the buyer information
$buyer_email = $_SESSION['acct_email'];
$buyer_id_query = "SELECT buyer_id FROM buyer_info WHERE buyer_email = ?";
$stmt = mysqli_prepare($conn, $buyer_id_query);
mysqli_stmt_bind_param($stmt, "s", $buyer_email);
mysqli_stmt_execute($stmt);
$buyer_id_result = mysqli_stmt_get_result($stmt);
$buyer_id_row = mysqli_fetch_assoc($buyer_id_result);
$buyer_id = $buyer_id_row['buyer_id'];

// Fetch order details
$order_query = "SELECT * FROM orders WHERE buyer_id = ?";
$order_stmt = mysqli_prepare($conn, $order_query);
mysqli_stmt_bind_param($order_stmt, "i", $buyer_id);
mysqli_stmt_execute($order_stmt);
$order_result = mysqli_stmt_get_result($order_stmt);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="confirmation.css">
</head>
<body>
    <h1>Order Confirmation</h1>

    <h2>Your Order Details</h2>
    <table>
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Quantity</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total_amount = 0;
            while ($row = mysqli_fetch_assoc($order_result)) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['product_id']) . '</td>';
                echo '<td>' . htmlspecialchars($row['order_quantity']) . '</td>';
                echo '<td>₱' . htmlspecialchars($row['total_amount']) . '</td>';
                echo '</tr>';
                $total_amount += $row['total_amount'];
            }
            ?>
        </tbody>
    </table>

    <p><strong>Total Amount: ₱<?php echo number_format($total_amount, 2); ?></strong></p>

    <a href="orders.php"><button>Back to Orders</button></a>
</body>
</html>
