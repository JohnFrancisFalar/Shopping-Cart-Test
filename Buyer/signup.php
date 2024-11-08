<!DOCTYPE html>
<html>
    <head>
        <title>Sign Up</title>
        <link rel="stylesheet" href="signup.css?version=3">
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
        Already have an account? <a class="Login_link" link rel="" href="sign_in.php">Login now</a>
</form>
        <?php
        include"../admin/database.php";
    if ($_SERVER['REQUEST_METHOD'] == 'POST'
    && !empty($_POST['acct_email'])
    && !empty($_POST['acct_name'])
    && !empty($_POST['acct_pass'])
    ){
        $buyer_email = $_POST['acct_email'];
        $buyer_name = $_POST['acct_name'];
        $buyer_pass = $_POST['acct_pass'];

    
        if(empty($buyer_email)){
        echo "Please enter email";
    }
    if(empty($buyer_pass)){
        echo "Please enter password";
}
if(!empty($buyer_email) && !empty($buyer_pass)){
    $password_hash = password_hash($buyer_pass, PASSWORD_DEFAULT);
    $sql = "INSERT INTO buyer_info (buyer_email, buyer_name, buyer_pass) VALUES ('$buyer_email', '$buyer_name', '$password_hash')";
    if(mysqli_query($conn, $sql)){
        header("Location: signup_result.php");
    exit();
    }
    
}
    }
    mysqli_close($conn);
        ?>
</body>
</html>