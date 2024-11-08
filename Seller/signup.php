<!DOCTYPE html>
<html>
    <head>
        <title>Sign Up</title>
        <link rel="stylesheet" href="signup.css">
</head>
<body>
    
    <form class="sign_up" method="POST" action = "">
    <h1>Sign up</h1>
        <input class="signup_email" type="email" name="acct_email" placeholder="Enter your email" required>
        <br>
        <br>
        <input class="signup_name" type="text" name="acct_name" placeholder="Enter your name" required>
        <br>
        <br>
        <input class="signup_pass"type="password" name="acct_pass" placeholder="Enter your password" required>
        <br>
        <br>
        <input class="signup_submit" type="submit" value="Register Now">
        <br><br>
        Already have an account? <a class="Login_link" link rel="" href="signin.php">Login now</a>
</form>
        <?php
        include"../admin/database.php";
    if ($_SERVER['REQUEST_METHOD'] == 'POST'
    && !empty($_POST['acct_email'])
    && !empty($_POST['acct_name'])
    && !empty($_POST['acct_pass'])
    ){
        $seller_email = $_POST['acct_email'];
        $seller_name = $_POST['acct_name'];
        $seller_pass = $_POST['acct_pass'];

    
        if(empty($seller_email)){
        echo "Please enter email";
    }
    if(empty($seller_pass)){
        echo "Please enter password";
}
if(!empty($seller_email) && !empty($seller_pass)){
    $password_hash = password_hash($seller_pass, PASSWORD_DEFAULT);
    $sql = "INSERT INTO seller_info (seller_email, seller_name, seller_pass) VALUES ('$seller_email', '$seller_name', '$password_hash')";
    if(mysqli_query($conn, $sql)){
        header("Location: sign_up_result.php");
    exit();
    }
    
}
    }
    mysqli_close($conn);
        ?>
</body>
</html>