<!DOCTYPE html>
<html>
<head>
    <title>Order History</title>
    <link rel="stylesheet" href="orderhistory.css?version=4">
</head>
<body>
    <br>
    <a href="buyer_page.php"><button>Back</button></a>
    <h1>Order History</h1>

    <?php
    include "../Admin/database.php";

    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    session_start();
    if (!isset($_SESSION['acct_email'])) {
        die("You need to log in to view your order history.");
    }

    $buyer_email = $_SESSION['acct_email'];

    $buyer_id_query = "SELECT buyer_id FROM buyer_info WHERE buyer_email = ?";
    $stmt = mysqli_prepare($conn, $buyer_id_query);
    mysqli_stmt_bind_param($stmt, "s", $buyer_email);
    mysqli_stmt_execute($stmt);
    $buyer_id_result = mysqli_stmt_get_result($stmt);
    $buyer_id_row = mysqli_fetch_assoc($buyer_id_result);
    $buyer_id = $buyer_id_row['buyer_id'];

    if (!$buyer_id) {
        die("Buyer ID not found.");
    }

    $get_order_details = "
        SELECT 
            p.product_name AS product_name, 
            p.product_price AS product_price, 
            oh.order_quantity AS order_quantity, 
            oh.total_amount AS total_amount, 
            oh.payment_method AS payment_method
        FROM 
            orders_history oh
        JOIN 
            products p ON p.product_id = oh.product_id
        WHERE 
            oh.buyer_id = ?";

    $get_order_stmt = mysqli_prepare($conn, $get_order_details);

    if ($get_order_stmt === false) {
        die("Error preparing statement: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($get_order_stmt, "i", $buyer_id);
    $execute_result = mysqli_stmt_execute($get_order_stmt);

    if ($execute_result === false) {
        die("Error executing statement: " . mysqli_stmt_error($get_order_stmt));
    }

    $result = mysqli_stmt_get_result($get_order_stmt);
    ?>

    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Product Price</th>
                <th>Quantity</th>
                <th>Total Amount</th>
                <th>Payment Method</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $product_name = htmlspecialchars($row['product_name']);
                $product_price = number_format($row['product_price'], 2);
                $order_quantity = $row['order_quantity'];
                $total_amount = number_format($row['total_amount'], 2);
                $payment_method = htmlspecialchars($row['payment_method']);

                echo "<tr>";
                echo "<td>$product_name</td>";
                echo "<td>₱$product_price</td>";
                echo "<td>$order_quantity</td>";
                echo "<td>₱$total_amount</td>";
                echo "<td>$payment_method</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>You have no purchase history</td></tr>";
        }
        ?>
        </tbody>
    </table>

    <?php
    mysqli_stmt_close($get_order_stmt);
    mysqli_close($conn); 
    ?>
</body>
</html>
