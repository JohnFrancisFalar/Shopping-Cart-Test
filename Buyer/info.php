<!DOCTYPE html>
<html>
<link rel="stylesheet" type="text/css" href="info.css">

    <head>
        <title>Personal Information</title>
    </head>
    <body>
        <a href="buyer_page.php"><button>Back</button></a>
        <h1>Personal Information</h1>
        <form method="POST" action="">
            <label>Your Email: </label>
            <input type="email" name="buyer_email" placeholder="e.g. john@gmail.com" required>
            
            <label>Shipping Address: </label>
            <input type="text" name="shipping_address" placeholder="e.g. Minglanilla, Cebu" required>
            
            <label>Contact Number: </label>
            <input type="text" name="contact_number" placeholder="e.g. 09770517497" required>

            <input type="submit" value="Submit">
        </form>

        <?php
        include "../Admin/database.php";
        require "functions.php";

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!empty($_POST['buyer_email']) && !empty($_POST['shipping_address']) && !empty($_POST['contact_number'])) {

                $_SESSION['shipping_address'] = $_POST['shipping_address'];
                $_SESSION['contact_number'] = $_POST['contact_number'];
                $_SESSION['buyer_email'] = $_POST['buyer_email'];

                $ShippingAddress = $_SESSION['shipping_address'];
                $ContactNumber = $_SESSION['contact_number'];
                $buyer_email = $_SESSION['buyer_email'];

                $query = "SELECT buyer_email FROM buyer_info WHERE buyer_email = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "s", $buyer_email);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) > 0) {
                    $sql = "UPDATE buyer_info SET shipping_address = ?, contact_number = ? WHERE buyer_email = ?";
                    $stmt_update = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt_update, "sss", $ShippingAddress, $ContactNumber, $buyer_email);

                    if (mysqli_stmt_execute($stmt_update)) {
                        echo "Buyer info updated successfully.";
                    } else {
                        echo "Failed to update: " . mysqli_stmt_error($stmt_update);
                    }
                    mysqli_stmt_close($stmt_update);
                } else {
                    echo "Incorrect email inputted.";
                }

                mysqli_stmt_close($stmt);
            } else {
                echo "Please fill in all the fields.";
            }
        }
        ?>
    </body>
</html>
