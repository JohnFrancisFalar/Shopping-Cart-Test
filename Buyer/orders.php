<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="orders.css?version=4">
    <title>Product List</title>
</head>
<body>
    <form class="bck_button" action="buyer_page.php"> 
        <input type="submit" value="Back">
    </form>

    <h1>Product List</h1>

    <table> 
        <thead>
            <tr>
                <th></th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            session_start();
            include("../Admin/database.php");

            $get_id = "SELECT buyer_id FROM buyer_info WHERE buyer_email = ?";
            $prepare_query = mysqli_prepare($conn, $get_id);
            mysqli_stmt_bind_param($prepare_query, "s", $_SESSION['acct_email']);
            mysqli_stmt_execute($prepare_query);
            $result = mysqli_stmt_get_result($prepare_query);

            if ($row = mysqli_fetch_assoc($result)) {
                $buyer_id = $row['buyer_id'];

                $sql = "SELECT p.product_id, 
                               p.product_name, 
                               p.product_desc, 
                               p.product_price, 
                               p.product_pic, 
                               o.order_quantity, 
                               o.total_amount 
                        FROM products p 
                        JOIN orders o ON p.product_id = o.product_id 
                        WHERE o.buyer_id = ?";

                $product_stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($product_stmt, "s", $buyer_id);
                mysqli_stmt_execute($product_stmt);
                $product_result = mysqli_stmt_get_result($product_stmt);

                $total_order_amount = 0;

                if (mysqli_num_rows($product_result) === 0) {
                    echo '<tr><td colspan="6">You have no items in your cart.</td></tr>';
                } else {
                    while ($row = mysqli_fetch_assoc($product_result)) {
                        $img_path = '../uploads/' . htmlspecialchars($row['product_pic']);
                        $product_id = htmlspecialchars($row['product_id']);

                        $product_price = (float)$row['product_price'];
                        $order_quantity = (int)$row['order_quantity'];
                        $total_amount = $product_price * $order_quantity;

                        $total_order_amount += $total_amount;

                        echo '<tr>
                                <td>';
                        if (file_exists($img_path)) {
                            echo "<br><img src='$img_path' alt='Product Image' width='100' height='100'><br>";
                        } else {
                            echo "Image not found.";
                        }
                        echo '</td>
                                <td>' . htmlspecialchars($row['product_name']) . '</td>
                                <td>' . htmlspecialchars($row['product_desc']) . '</td>
                                <td>' . "₱" . htmlspecialchars($product_price) . '</td>
                                <td>' . htmlspecialchars($order_quantity) . '</td>
                                <td>
                                    <form method="POST" style="display: inline;" action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '"> 
                                        <input type="number" name="quantity" min="1" required>
                                        <input type="hidden" name="product_id" value="' . $product_id . '">
                                        <input type="submit" value="Add quantity">
                                    </form>
                                    <form method="POST" style="display: inline;" action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '"> 
                                        <input type="hidden" name="remove" value="' . $product_id . '">
                                        <input type="submit" value="Remove">
                                    </form>
                                </td>
                              </tr>';
                    }
                }

                echo '<tr>
                        <td colspan="4" style="text-align: right; font-weight: bold;">Total Order Amount:</td>
                        <td>₱' . number_format($total_order_amount, 2) . '</td>
                        <td>
                            <form method="POST" action="checkout.php">
                                <input type="submit" value="Check Out">
                            </form>
                        </td>
                      </tr>';
            } else {
                echo '<tr><td colspan="6">No buyer found.</td></tr>';
            }
            ?>
        </tbody>
    </table>

    <?php
    if (isset($_POST['quantity']) && isset($_POST['product_id'])) {
        $product_quantity = (int)mysqli_real_escape_string($conn, $_POST['quantity']);
        $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);

        $add_quantity = "UPDATE orders 
                         SET order_quantity = order_quantity + ?, 
                             total_amount = order_quantity * 
                             (SELECT product_price FROM products WHERE product_id = ?) 
                         WHERE product_id = ? AND buyer_id = ?";

        $stmt = mysqli_prepare($conn, $add_quantity);
        mysqli_stmt_bind_param($stmt, "isis", $product_quantity, $product_id, $product_id, $buyer_id);
        mysqli_stmt_execute($stmt);

        header("Location: " . htmlspecialchars($_SERVER['PHP_SELF']));
        exit;
    }

    if (isset($_POST['remove'])) {
        $product_id = mysqli_real_escape_string($conn, $_POST['remove']);
        $sql = "DELETE FROM orders WHERE product_id = ? AND buyer_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $product_id, $buyer_id);
        mysqli_stmt_execute($stmt);

        header("Location: " . htmlspecialchars($_SERVER['PHP_SELF']));
        exit;
    }
    ?>
</body>
</html>
