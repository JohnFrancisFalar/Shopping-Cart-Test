<?php
session_start();
include "../Admin/database.php";

if (!isset($_SESSION['acct_email'])) {
    echo "Please log in to add products to the cart.";
    exit;
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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    $check_cart_query = "SELECT order_id, order_quantity FROM orders WHERE buyer_id = ? AND product_id = ?";
    $check_stmt = mysqli_prepare($conn, $check_cart_query);
    mysqli_stmt_bind_param($check_stmt, "ii", $buyer_id, $product_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);

    if (mysqli_num_rows($check_result) > 0) {
        $row = mysqli_fetch_assoc($check_result);
        $new_quantity = $row['order_quantity'] + $quantity;
        $update_query = "UPDATE orders SET order_quantity = ? WHERE order_id = ?";
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($update_stmt, "ii", $new_quantity, $row['order_id']);
        mysqli_stmt_execute($update_stmt);
        mysqli_stmt_close($update_stmt);
    } else {
        $insert_query = "
            INSERT INTO orders (temptransac_id, product_id, order_quantity, buyer_id, total_amount) 
            VALUES (1, ?, ?, ?, (SELECT product_price * ? FROM products WHERE product_id = ?))
        ";
        $insert_stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($insert_stmt, "iiiii", $product_id, $quantity, $buyer_id, $quantity, $product_id);
        mysqli_stmt_execute($insert_stmt);
        mysqli_stmt_close($insert_stmt);
    }

    mysqli_stmt_close($check_stmt);
    mysqli_close($conn);

    echo "Product added to cart successfully.";
    header("Location: add_to_cart_result.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['PaymentMethod'])) {
    $PaymentMethod = $_POST['PaymentMethod'];

    $order_query = "SELECT order_id, product_id, order_quantity, total_amount FROM orders WHERE buyer_id = ?";
    $order_stmt = mysqli_prepare($conn, $order_query);
    mysqli_stmt_bind_param($order_stmt, "i", $buyer_id);
    mysqli_stmt_execute($order_stmt);
    $order_result = mysqli_stmt_get_result($order_stmt);

    $order_date = date('Y-m-d H:i:s');
    $add_orders_history_query = "
        INSERT INTO orders_history (order_id, product_id, order_quantity, buyer_id, order_date, total_amount, payment_method)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

    $insert_stmt = mysqli_prepare($conn, $add_orders_history_query);

    while ($row = mysqli_fetch_assoc($order_result)) {
        $order_id = $row['order_id'];
        $product_id = $row['product_id'];
        $order_quantity = $row['order_quantity'];
        $total_amount = $row['total_amount'];

        // Debugging output for the values being inserted
        echo "Order ID: $order_id, Product ID: $product_id, Order Quantity: $order_quantity, Buyer ID: $buyer_id, Order Date: $order_date, Total Amount: $total_amount, Payment Method: $PaymentMethod\n";

        mysqli_stmt_bind_param($insert_stmt, "iiisids", 
            $order_id, $product_id, $order_quantity, $buyer_id, $order_date, $total_amount, $PaymentMethod);

        // Check for insert execution errors
        if (!mysqli_stmt_execute($insert_stmt)) {
            echo "Insert Error: " . mysqli_stmt_error($insert_stmt);
        }

        // Update product quantity
        $update_product_query = "UPDATE products SET product_quantity = product_quantity - ? WHERE product_id = ?";
        $update_product_stmt = mysqli_prepare($conn, $update_product_query);
        mysqli_stmt_bind_param($update_product_stmt, "ii", $order_quantity, $product_id);
        mysqli_stmt_execute($update_product_stmt);
        mysqli_stmt_close($update_product_stmt);
    }

    mysqli_stmt_close($order_stmt);
    mysqli_stmt_close($insert_stmt);

    $delete_orders_query = "DELETE FROM orders WHERE buyer_id = ?";
    $delete_stmt = mysqli_prepare($conn, $delete_orders_query);
    mysqli_stmt_bind_param($delete_stmt, "i", $buyer_id);
    mysqli_stmt_execute($delete_stmt);
    mysqli_stmt_close($delete_stmt);

    mysqli_close($conn);

    header("Location: purchase_result.php");
    exit();
}
?>
