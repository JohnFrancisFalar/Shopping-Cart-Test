<?php
include "../Admin/database.php";
include "functions.php";

if (!isset($_SESSION['buyer_id'])) {
    header("Location: login_page.php"); 
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Buy a Product</title>
    <link rel="stylesheet" href="buyer_page.css?version=7">
</head>
<body>
    <div class = "Links" >
    <a  href="../main_page.php"><button>Logout</button></a>

    <br>
    <a  href="orders.php"><button>View Cart</button></a>
    <br>
    <a  href="orderhistory.php"><button>Purchase History</button></a>
    <br>
    <a  href="info.php"><button>Update Info</button></a>
    </div>
    <h1>Product List</h1>
    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $sql = "SELECT * FROM products";
        $product = mysqli_query($conn, $sql);

        if (!$product) {
            die("Database query failed: " . mysqli_error($conn));
        }

        while ($row = mysqli_fetch_assoc($product)) {
            $img_path = '../uploads/' . htmlspecialchars($row['product_pic']);
            $product_id = htmlspecialchars($row['product_id']);
            
            echo '<tr>';
            echo '<td>';
            if (file_exists($img_path)) {
                echo "<img src='$img_path' alt='Product Image' width='100' height='100'>";
            } else {
                echo "Image not found.";
            }
            echo '</td>';
            echo '<td>' .   htmlspecialchars($row['product_name']) . '</td>';
            echo '<td>' . htmlspecialchars($row['product_desc']) . '</td>';
            echo '<td>' . "₱" . htmlspecialchars($row['product_price']) . '</td>';
            echo '<td>' . htmlspecialchars($row['product_quantity']) . '</td>';
            echo '<td>
                    <form method="POST" action="" style="display:inline;">
                        <input type="number" name="quantity" min="1" max="' . htmlspecialchars($row['product_quantity']) . '" placeholder="1" required>
                        <input type="submit" name="Add_to_cart" value="Add to cart">
                        <input type="hidden" name="product_id" value="' . $product_id . '">
                    </form>
                  </td>';
            echo '</tr>';
            
        }
        ?>
        </tbody>
    </table>
    
</body>
</html>
