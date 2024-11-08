<!DOCTYPE html>
<html>
<head>
    <title>Products Sold</title>
    <link rel="stylesheet" href="products_sold.css?version=4">
</head>
<body>
<br>
    <a href="seller_page.php"><button>Back</button></a>
    <h1>Products Sold</h1>

    <?php
    include "../Admin/database.php";

    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    session_start();
    if (!isset($_SESSION['acct_email'])) {
        die("You need to log in to view your order history.");
    }

    $seller_email = $_SESSION['acct_email'];
    $seller_id_query = "SELECT seller_id FROM seller_info WHERE seller_email = ?";
    $stmt = mysqli_prepare($conn, $seller_id_query);
    mysqli_stmt_bind_param($stmt, "s", $seller_email);
    mysqli_stmt_execute($stmt);
    $seller_id_result = mysqli_stmt_get_result($stmt);
    $seller_id_row = mysqli_fetch_assoc($seller_id_result);
    $seller_id = $seller_id_row['seller_id'];

    if (!$seller_id) {
        die("Seller ID not found.");
    }

    $get_order_details = "
    SELECT 
        bi.buyer_name AS `Buyer Name`,
        p.product_name AS `Product Name`, 
        p.product_price AS `Product Price`, 
        oh.order_quantity AS `Order Quantity`, 
        oh.total_amount AS `Total Amount`,
        oh.payment_method AS `Payment Method`
    FROM 
        orders_history oh
    JOIN 
        buyer_info bi ON bi.buyer_id = oh.buyer_id
    JOIN 
        products p ON p.product_id = oh.product_id
    WHERE 
        p.seller_id = ?";

    $get_order_stmt = mysqli_prepare($conn, $get_order_details);

    if ($get_order_stmt === false) {
        die("Error preparing statement: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($get_order_stmt, "i", $seller_id);
    $execute_result = mysqli_stmt_execute($get_order_stmt);

    if ($execute_result === false) {
        die("Error executing statement: " . mysqli_stmt_error($get_order_stmt));
    }

    $result = mysqli_stmt_get_result($get_order_stmt);
    ?>

    <table>
        <thead>
            <tr>
                <th>Buyer Name</th>
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

                $buyer_name = $row['Buyer Name'];
                $product_name = $row['Product Name'];
                $product_price = $row['Product Price'];
                $order_quantity = $row['Order Quantity'];
                $total_amount = $row['Total Amount'];
                $payment_method = $row['Payment Method'];

                echo "<tr>";
                echo "<td>$buyer_name</td>";
                echo "<td>$product_name</td>";
                echo "<td>₱$product_price</td>"; 
                echo "<td>$order_quantity</td>";
                echo "<td>₱$total_amount</td>";
                echo "<td>₱$payment_method</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>You have no products sold</td></tr>";
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
