<!DOCTYPE html>
<html>
    <head>
        <title>Login</title>
        <link rel="stylesheet" href="signin.css?version=5">
</head>
<body>
    <form method="POST" class="login" method="POST" action = "">
    <h1>Sign in</h1>
        <input class="login_email" type="email" name="acct_email" placeholder="Enter your email" required>
        <br>
        <br>
        <input class="login_pass"type="password" name="acct_pass" placeholder="Enter your pass" required>
        <br>
        <br>
        <input class="login_submit" type="submit" value="Login">
        <br><br>
        Don't have an account? <a class="log_button" link rel="" href="signup.php">Register here</a>
</form>

<?php
include"../admin/database.php";
include"functions.php";


/*if(isset($_SESSION['acct_email']['acct_pass'])){
    header("Location: seller_page.php");
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
}*/


?>
</body>
</html>