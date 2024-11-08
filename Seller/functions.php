<?php

include '../Admin/database.php'; 
session_start();
class sign_in {
    public function login($email) {
        $_SESSION['acct_email'] = $email;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!empty($_POST['acct_email']) && !empty($_POST['acct_pass'])) {
        $sel_email = $_POST['acct_email'];
        $sel_pass = $_POST['acct_pass'];


        if ($prepare_query = mysqli_prepare($conn, "SELECT seller_pass FROM seller_info WHERE seller_email = ?")) {
            mysqli_stmt_bind_param($prepare_query, "s", $sel_email);
            mysqli_stmt_execute($prepare_query);
            $result = mysqli_stmt_get_result($prepare_query);

            if ($row = mysqli_fetch_assoc($result)) {
                if (password_verify($sel_pass, $row['seller_pass'])) {
                    echo "Login Successful";
                    $_SESSION['acct_email'] = $sel_email; 
                    header("Location: seller_page.php");
                } else {
                    echo "Incorrect password";
                }
            } else {
                echo "Seller not found.";
            }
        }
    }

    if (isset($_SESSION['acct_email']) && !empty($_POST['product_name']) && !empty($_POST['product_desc']) &&
        !empty($_POST['product_price']) && !empty($_POST['product_quantity']) && isset($_FILES['file_pic'])) {
        
        $product_name = $_POST['product_name'];
        $product_desc = $_POST['product_desc'];
        $product_price = $_POST['product_price'];
        $product_quantity = $_POST['product_quantity'];
        $product_pic = $_FILES['file_pic']['name'];

        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($_FILES['file_pic']['name']);
        
        if (move_uploaded_file($_FILES["file_pic"]["tmp_name"], $target_file)) {
   
            $sql = "SELECT seller_id FROM seller_info WHERE seller_email = ?";
            $prepare_query = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($prepare_query, "s", $_SESSION['acct_email']);
            mysqli_stmt_execute($prepare_query);
            $result = mysqli_stmt_get_result($prepare_query);

            if ($row = mysqli_fetch_assoc($result)) {
                $seller_id = $row['seller_id'];

                $insert_sql = "INSERT INTO products (product_name, product_desc, product_price, product_quantity, product_pic, seller_id) 
                               VALUES (?, ?, ?, ?, ?, ?)";
                $insert_query = mysqli_prepare($conn, $insert_sql);
                mysqli_stmt_bind_param($insert_query, "ssdisi", $product_name, $product_desc, $product_price, $product_quantity, $target_file, $seller_id);
                
                if (mysqli_stmt_execute($insert_query)) {
                    $_SESSION['products'][] = array(
                        'name' => $product_name,
                        'desc' => $product_desc,
                        'price' => $product_price,
                        'quantity' => $product_quantity,
                        'pic' => $product_pic
                    );
                    header("Location: add_product_result.php");
                    exit();
                } else {
                    echo "Error executing query: " . mysqli_error($conn);
                }
            } else {
                echo "Seller not found.";
            }
        } else {
            echo "Failed to upload file.";
        }
    }
}
?>
