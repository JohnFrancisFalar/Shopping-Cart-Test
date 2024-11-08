<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="products.css?version=6">
    <title>Product List</title>
</head>
<body>
    <form class="bck_button" action="seller_page.php"> 
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
            include("../Admin/database.php");
            include "functions.php";
            
            $get_id = "SELECT seller_id FROM seller_info WHERE seller_email = ?";
            $prepare_query = mysqli_prepare($conn, $get_id);
            mysqli_stmt_bind_param($prepare_query, "s", $_SESSION['acct_email']);
            mysqli_stmt_execute($prepare_query);
            $result = mysqli_stmt_get_result($prepare_query);

            if ($row = mysqli_fetch_assoc($result)) {
                $seller_id = $row['seller_id'];

                $sql = "SELECT * FROM products p JOIN seller_info s ON p.seller_id = s.seller_id WHERE p.seller_id = ?";
                $product_stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($product_stmt, "i", $seller_id);
                mysqli_stmt_execute($product_stmt);
                $product_result = mysqli_stmt_get_result($product_stmt);

                while ($row = mysqli_fetch_assoc($product_result)) {
                    $img_path = '../uploads/' . htmlspecialchars($row['product_pic']);
                    $product_id = htmlspecialchars($row['product_id']);

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
                            <td>' . htmlspecialchars($row['product_price']) . '</td>
                            <td>' . htmlspecialchars($row['product_quantity']) . '</td>
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
            ?>
        </tbody>
    </table>

    <?php
    if (isset($_POST['quantity']) && isset($_POST['product_id'])) {
        $product_quantity = (int) mysqli_real_escape_string($conn, $_POST['quantity']);
        $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
        
        $add_quantity = "UPDATE products SET product_quantity = product_quantity + ? WHERE product_id = ?";
        $stmt = mysqli_prepare($conn, $add_quantity);
        mysqli_stmt_bind_param($stmt, "is", $product_quantity, $product_id);
        mysqli_stmt_execute($stmt);
        
        header("Location: " . htmlspecialchars($_SERVER['PHP_SELF']));
        exit;
    }
    if (isset($_POST['remove'])) {
        $product_id = mysqli_real_escape_string($conn, $_POST['remove']);
        $sql = "DELETE FROM products WHERE product_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $product_id);
        mysqli_stmt_execute($stmt);
        
        header("Location: " . htmlspecialchars($_SERVER['PHP_SELF']));
        exit;
    }
    ?>
</body>
</html>
