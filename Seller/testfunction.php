<?php
class sign_in{
    public function login($email){
     $_SESSION['acct_email'] = $email;
    }
}

if(isset($_SESSION['acct_email']['acct_pass'])){
    header("Location: seller_page.php");
}
if($_SERVER['REQUEST_METHOD'] == 'POST'){
$get_email = $_POST['acct_email'];
"SELECT seller_id from seller_info where seller_email = $get_email";

}
if($_SERVER['REQUEST_METHOD'] == 'POST'){
if(!empty($_POST['acct_email'])
&& !empty($_POST['acct_pass']));

    
    $sel_email = $_POST['acct_email'];
    $sel_pass = $_POST['acct_pass'];
    $sql = "SELECT * FROM seller_info";
    if(!empty($sql)){
        if($prepare_query = mysqli_prepare($conn, "SELECT seller_pass FROM seller_info WHERE seller_email = ?" )){
            mysqli_stmt_bind_param($prepare_query, "s", $sel_email);
            mysqli_stmt_execute($prepare_query);
            $result = mysqli_stmt_get_result($prepare_query);

            if($row = mysqli_fetch_assoc($result)){
                if(password_verify($sel_pass, $row['seller_pass'])){
                    echo "Login Successful";
                    header("Location: seller_page.php");
                }
                else{
                    echo "Incorrect password";
                }
            }

            
        }

    }
}

if (!isset($_SESSION['products'])) {
    $_SESSION['products'] = array();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' 
    && !empty($_POST['product_name'])
    && !empty($_POST['product_desc']) 
    && !empty($_POST['product_price'])
    && !empty($_POST['product_quantity']) 
    && isset($_FILES['file_pic'])) {
    
    $product_name = $_POST['product_name'];
    $product_desc = $_POST['product_desc'];
    $product_price = $_POST['product_price'];
    $product_quantity = $_POST['product_quantity'];
    $product_pic = $_FILES['file_pic']['name'];

    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($_FILES['file_pic']['name']);
    
    if(move_uploaded_file($_FILES["file_pic"]["tmp_name"], $target_file)) {
        $get_id = "SELECT seller_id FROM seller_info WHERE seller_email = ?";
        

        $sql = "INSERT INTO products (product_name, product_desc, product_price, product_quantity, product_pic, seller_id) 
                VALUES 
                ('$product_name', '$product_desc', '$product_price', '$product_quantity', '$target_file', '$get_id')";
        echo "SQL Query: " . $sql . "<br>"; 

        if(mysqli_query($conn, $sql)) {
            $_SESSION['products'][] = array(
                'name' => $product_name, 
                'desc' => $product_desc, 
                'price' => $product_price, 
                'quantity' => $product_quantity, 
                'pic' => $_FILES['file_pic']['name']
            );
            header("Location: add_product_result.php");
        } else {
            echo "Error executing query: " . mysqli_error($conn);
        }
    } else {
        echo "Failed to upload file.";
    }

    exit();
}




?>

<!DOCTYPE html>
<html>
<link rel="stylesheet" href="products.css?version=4">
<form class="bck_button" action="seller_page.php"> 
    <input type="submit" value="Back" >
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
        include("functions.php");

        if (!isset($_SESSION['acct_email'])) {
            echo "You need to be logged in to view this page.";
            exit;
        }

        $sql = "SELECT seller_id FROM seller_info WHERE seller_email = ?";
        $prepare_query = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($prepare_query, "s", $_SESSION['acct_email']);
        mysqli_stmt_execute($prepare_query);
        $result = mysqli_stmt_get_result($prepare_query);

        if ($row = mysqli_fetch_assoc($result)) {
            $seller_id = $row['seller_id'];
        } else {
            echo "Seller not found.";
            exit;
        }

        $sql = "SELECT * FROM products WHERE seller_id = ?";
        $prepare_query = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($prepare_query, "i", $seller_id);
        mysqli_stmt_execute($prepare_query);
        $product_result = mysqli_stmt_get_result($prepare_query);

        while ($row = mysqli_fetch_assoc($product_result)) {
            $img_path = '../uploads/' . htmlspecialchars($row['product_pic']);
            echo '
                <tr>
                    <td>';
            if (file_exists($img_path)) {
                echo "<br><img src='$img_path' alt='Product Image' width='100' height='100'><br>";
            } else {
                echo "image not found.";
            }
            echo '<td>' . htmlspecialchars($row['product_name']) 
                . '<td>' . htmlspecialchars($row['product_desc'])
                . '<td>' . htmlspecialchars($row['product_price'])
                . '<td>' . htmlspecialchars($row['product_quantity'])
                . '<td>
                    <form method="POST" style="display: inline;" action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '"> 
                        <input type="hidden" name="remove" value="' . htmlspecialchars($row['product_id']) . '">
                        <input type="submit" value="Remove">
                    </form>
                </td>
                </tr>';
        }
        echo '</tbody></table>';

        if (isset($_POST['remove'])) {
            $product_id = mysqli_real_escape_string($conn, $_POST['remove']);
            $sql = "DELETE FROM products WHERE product_id = '$product_id'";
            mysqli_query($conn, $sql);
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
        ?>
    </tbody>
</table>
</html>
