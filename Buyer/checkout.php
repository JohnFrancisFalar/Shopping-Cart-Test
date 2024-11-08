<?php
include "../Admin/database.php";
include "functions.php";

if (!isset($_SESSION['acct_email'])) {
    echo "Please log in to proceed to checkout.";
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

$get_buyer_info = "SELECT * FROM buyer_info WHERE buyer_email = ?";
$stmt = mysqli_prepare($conn, $get_buyer_info);
mysqli_stmt_bind_param($stmt, "s", $buyer_email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Check Out</title>
    <link rel="stylesheet" href="checkout.css?version=9">
</head>
<body>
    <a href="orders.php"><button>Back</button></a>
    <h1>Check out</h1>

    <div class="buyer-info">
        <?php
        if ($row = mysqli_fetch_assoc($result)) {
            if(is_null($row['shipping_address']) && is_null($row['contact_number'])){
                echo "You have not filled the shipping address and contact number yet. 
                Please fill them up first before proceeding to placing order.";

                echo '<a href="info.php"> Fill here</a>';
            

        }
        else{
            echo "<p>Buyer Name: " . htmlspecialchars($row['buyer_name']) . "</p>";
            echo "<p>Email: " . htmlspecialchars($row['buyer_email']) . "</p>";
            echo "<p>Contact #: " . htmlspecialchars($row['contact_number']) . "</p>";
            echo "<p>Shipping Address: " . htmlspecialchars($row['shipping_address']) . "</p>";
        echo'</div>';
        echo'<form method="POST">';
        echo'<label>Mode of Payment</label>';
        echo'<select name="PaymentMethod" required>';
            echo'<option>Cash on Delivery</option>';
            echo'<option>GCash</option>';
            echo'<option>Bank Payment</option>';
        echo'</select><br>';
        echo'<input type="submit" value="Place Order">';
        echo'</form>';
        }
        }

        ?>
    


</body>
</html>
